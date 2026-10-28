<?php
/**
 * Cancel Reservation Endpoint
 * POST /api/reservations/cancel.php
 * For guests to cancel their own reservations
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
    validateRequired($data, ['id']);
    
    $reservationId = (int)$data['id'];
    $pdo = getConnection();
    
    // Verify reservation belongs to user
    $stmt = $pdo->prepare("
        SELECT dr.utilisateur_id, l.proprietaire_id, l.titre
        FROM DemandesReservation dr
        JOIN Annonces a ON dr.annonce_id = a.id
        JOIN Logements l ON a.logement_id = l.id
        WHERE dr.id = ?
    ");
    $stmt->execute([$reservationId]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        sendError('Reservation not found', 404);
    }
    
    if ($reservation['utilisateur_id'] != $userId) {
        sendError('Access denied', 403);
    }
    
    // Update status
    $stmt = $pdo->prepare("UPDATE DemandesReservation SET statut = 'annulee' WHERE id = ?");
    $stmt->execute([$reservationId]);
    

    
    sendSuccess(null, 'Reservation cancelled successfully');
    
} catch (PDOException $e) {
    error_log("Cancel Reservation Error: " . $e->getMessage());
    sendError('Failed to cancel reservation', 500);
}
