<?php
/**
 * List User Reservations Endpoint
 * GET /api/reservations/list.php
 * Query: type=guest|host (default: guest)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

try {
    $userId = requireAuth();
    $pdo = getConnection();
    
    $type = isset($_GET['type']) ? $_GET['type'] : 'guest';
    
    if ($type === 'guest') {
        // Get reservations made by user
        $stmt = $pdo->prepare("
            SELECT 
                dr.*,
                l.id as logement_id,
                l.titre as logement_titre,
                l.wilaya,
                l.prix_par_nuit,
                l.proprietaire_id,
                u.nom as proprietaire_nom,
                u.photo_profil as proprietaire_photo,
                (SELECT url_image FROM Images WHERE logement_id = l.id LIMIT 1) as logement_image
            FROM DemandesReservation dr
            JOIN Annonces a ON dr.annonce_id = a.id
            JOIN Logements l ON a.logement_id = l.id
            JOIN Utilisateurs u ON l.proprietaire_id = u.id
            WHERE dr.utilisateur_id = ?
            GROUP BY dr.id
            ORDER BY dr.date_demande DESC
        ");
        $stmt->execute([$userId]);
    } else {
        // Get reservations for user's properties
        $stmt = $pdo->prepare("
            SELECT 
                dr.*,
                l.id as logement_id,
                l.titre as logement_titre,
                l.wilaya,
                l.prix_par_nuit,
                u.id as client_id,
                u.nom as client_nom,
                u.email as client_email,
                u.photo_profil as client_photo,
                (SELECT url_image FROM Images WHERE logement_id = l.id LIMIT 1) as logement_image
            FROM DemandesReservation dr
            JOIN Annonces a ON dr.annonce_id = a.id
            JOIN Logements l ON a.logement_id = l.id
            JOIN Utilisateurs u ON dr.utilisateur_id = u.id
            WHERE l.proprietaire_id = ?
            GROUP BY dr.id
            ORDER BY dr.date_demande DESC
        ");
        $stmt->execute([$userId]);
    }
    
    $reservations = $stmt->fetchAll();
    
    // Calculate total price for each reservation
    foreach ($reservations as &$reservation) {
        $days = (strtotime($reservation['date_depart']) - strtotime($reservation['date_arrivee'])) / (60 * 60 * 24);
        $reservation['nombre_nuits'] = $days;
        $reservation['prix_total'] = $days * $reservation['prix_par_nuit'];
    }
    
    sendSuccess($reservations, 'Reservations retrieved successfully');
    
} catch (PDOException $e) {
    error_log("List Reservations Error: " . $e->getMessage());
    sendError('Failed to retrieve reservations', 500);
}
