<?php
/**
 * Add Property Review Endpoint
 * POST /api/reviews/add-property-review.php
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
    // commentaire is optional (nullable TEXT in DB), only note and reservation ID are required
    validateRequired($data, ['demande_reservation_id', 'note']);
    
    // Validate rating
    if ($data['note'] < 1 || $data['note'] > 5) {
        sendError('Rating must be between 1 and 5', 400);
    }
    
    $pdo = getConnection();
    
    // Verify reservation exists and belongs to user
    $stmt = $pdo->prepare("
        SELECT dr.statut, dr.utilisateur_id, l.proprietaire_id
        FROM DemandesReservation dr
        JOIN Annonces a ON dr.annonce_id = a.id
        JOIN Logements l ON a.logement_id = l.id
        WHERE dr.id = ?
    ");
    $stmt->execute([$data['demande_reservation_id']]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        sendError('Reservation not found', 404);
    }
    
    if ($reservation['utilisateur_id'] != $userId) {
        sendError('Access denied', 403);
    }
    
    // Can only review completed/confirmed reservations
    if (!in_array($reservation['statut'], ['completee', 'confirmee'])) {
        sendError('Can only review confirmed or completed stays', 400);
    }
    
    // Check if already reviewed
    $stmt = $pdo->prepare("
        SELECT id FROM AvisSurProprietes
        WHERE demande_reservation_id = ?
    ");
    $stmt->execute([$data['demande_reservation_id']]);
    
    if ($stmt->fetch()) {
        sendError('You have already reviewed this property', 409);
    }
    
    $commentaire = isset($data['commentaire']) ? sanitizeInput($data['commentaire']) : '';

    // Add review
    $stmt = $pdo->prepare("
        INSERT INTO AvisSurProprietes (demande_reservation_id, note, commentaire)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $data['demande_reservation_id'],
        $data['note'],
        $commentaire
    ]);
    
    $reviewId = $pdo->lastInsertId();
    
    // Notify property owner — wrapped in its own try/catch so a missing
    // Notifications table never prevents the review from being saved
    try {
        $tables = $pdo->query("SHOW TABLES LIKE 'Notifications'")->fetchAll();
        if (!empty($tables)) {
            $stmt = $pdo->prepare("
                INSERT INTO Notifications (utilisateur_id, type_notification, message)
                VALUES (?, 'new_review', ?)
            ");
            $stmt->execute([
                $reservation['proprietaire_id'],
                'You received a new review for your property'
            ]);
        }
    } catch (Exception $notifEx) {
        error_log("Notification insert skipped: " . $notifEx->getMessage());
    }
    
    sendSuccess(['id' => $reviewId], 'Review added successfully', 201);
    
} catch (PDOException $e) {
    error_log("Add Property Review Error: " . $e->getMessage());
    sendError('Failed to add review: ' . $e->getMessage(), 500);
}

