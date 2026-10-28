<?php
/**
 * Create Reservation Request Endpoint
 * POST /api/reservations/create.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    $userId = requireAuth();
    
    $data = getJsonInput();
    validateRequired($data, ['annonce_id', 'date_arrivee', 'date_depart', 'nombre_invites']);
    
    // Normalize Dates to YYYY-MM-DD
    $data['date_arrivee'] = date('Y-m-d', strtotime($data['date_arrivee']));
    $data['date_depart'] = date('Y-m-d', strtotime($data['date_depart']));
    
    $pdo = getConnection();
    
    // Validate annonce exists
    $stmt = $pdo->prepare("
        SELECT a.id, l.proprietaire_id, l.prix_par_nuit, l.nombre_invites
        FROM Annonces a
        JOIN Logements l ON a.logement_id = l.id
        WHERE a.id = ? AND l.statut IN ('disponible', 'occupe')
    ");
    $stmt->execute([$data['annonce_id']]);
    $annonce = $stmt->fetch();
    
    if (!$annonce) {
        sendError('Property not available', 404);
    }
    
    // Can't book own property
    if ($annonce['proprietaire_id'] == $userId) {
        sendError('Cannot book your own property', 400);
    }
    
    // Validate guest capacity
    if ($data['nombre_invites'] > $annonce['nombre_invites']) {
        sendError('Number of guests exceeds property capacity', 400);
    }
    
    // Check for conflicting reservations (only confirmed bookings block new requests)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM DemandesReservation
        WHERE annonce_id = ?
        AND statut = 'confirmee'
        AND (
            (date_arrivee <= ? AND date_depart >= ?)
            OR (date_arrivee <= ? AND date_depart >= ?)
            OR (date_arrivee >= ? AND date_depart <= ?)
        )
    ");
    $stmt->execute([
        $data['annonce_id'],
        $data['date_arrivee'], $data['date_arrivee'],
        $data['date_depart'], $data['date_depart'],
        $data['date_arrivee'], $data['date_depart']
    ]);
    
    if ($stmt->fetch()['count'] > 0) {
        sendError('Property already has a confirmed booking for these dates', 409);
    }
    
    // BROAD PROPERTY SHIELD: One pending request per property per user
    $stmtCheck = $pdo->prepare("
        SELECT dr.id FROM DemandesReservation dr
        JOIN Annonces a ON dr.annonce_id = a.id
        WHERE dr.utilisateur_id = ? 
        AND a.logement_id = (SELECT logement_id FROM Annonces WHERE id = ?)
        AND dr.date_arrivee = ? 
        AND dr.date_depart = ?
        AND dr.statut = 'en_attente'
        LIMIT 1
    ");
    $stmtCheck->execute([
        $userId, 
        $data['annonce_id'], 
        $data['date_arrivee'], 
        $data['date_depart']
    ]);
    $existing = $stmtCheck->fetch();

    if ($existing) {
        sendSuccess([
            'id' => $existing['id'],
            'statut' => 'en_attente',
            'is_duplicate' => true,
            'reason' => 'Broad property shield blocked'
        ], 'You already have a pending request for this home.', 200);
        exit;
    }

    // Create reservation (Primary Task)
    $stmt = $pdo->prepare("
        INSERT INTO DemandesReservation (
            utilisateur_id, annonce_id, date_arrivee, date_depart,
            nombre_invites, statut
        ) VALUES (?, ?, ?, ?, ?, 'en_attente')
    ");
    $stmt->execute([
        $userId,
        $data['annonce_id'],
        $data['date_arrivee'],
        $data['date_depart'],
        $data['nombre_invites']
    ]);
    $reservationId = $pdo->lastInsertId();
    
    // Fetch guest name for the notification
    $guestStmt = $pdo->prepare("SELECT nom FROM Utilisateurs WHERE id = ?");
    $guestStmt->execute([$userId]);
    $guest = $guestStmt->fetch();
    $guestName = $guest ? $guest['nom'] : 'A guest';

    // Send Email Alert to Host (Only Email, no in-app notification)
    $stmtHost = $pdo->prepare("
        SELECT u.nom, u.email, l.titre 
        FROM Utilisateurs u 
        JOIN Logements l ON l.proprietaire_id = u.id 
        JOIN Annonces a ON a.logement_id = l.id 
        WHERE a.id = ? AND u.id = ?
    ");
    $stmtHost->execute([$data['annonce_id'], $annonce['proprietaire_id']]);
    $hostData = $stmtHost->fetch();

    if ($hostData) {
        try {
            require_once __DIR__ . '/../../includes/mailer.php';
            $dates = $data['date_arrivee'] . " to " . $data['date_depart'];
            Mailer::sendBookingAlert($hostData['email'], $hostData['nom'], $guestName, $hostData['titre'], $dates);
        } catch (Exception $e) {
            error_log("Email Alert Failed: " . $e->getMessage());
        }
    }
    
    // Ensure we don't have any pending headers that might cause resubmission issues
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    sendSuccess([
        'id' => $reservationId,
        'statut' => 'en_attente',
        'message' => 'New reservation created'
    ], 'Reservation request created successfully', 201);
    
} catch (Exception $e) {
    error_log("Create Reservation Error: " . $e->getMessage());
    sendError('Error: ' . $e->getMessage(), 500);
}
