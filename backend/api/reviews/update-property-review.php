<?php
/**
 * Update Property Review Endpoint
 * PUT /api/reviews/update-property-review.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendError('Method not allowed', 405);
}

try {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (!isset($_SESSION['user_id'])) {
        sendError('Authentication required', 401);
    }
    
    $userId = $_SESSION['user_id'];
    
    $data = getJsonInput();
    validateRequired($data, ['review_id', 'note', 'commentaire']);
    
    // Validate rating
    if ($data['note'] < 1 || $data['note'] > 5) {
        sendError('Rating must be between 1 and 5', 400);
    }
    
    $pdo = getConnection();
    
    // Verify review exists and belongs to the current user (through their reservation)
    $stmt = $pdo->prepare("
        SELECT asp.id 
        FROM AvisSurProprietes asp
        JOIN DemandesReservation dr ON asp.demande_reservation_id = dr.id
        WHERE asp.id = ? AND dr.utilisateur_id = ?
    ");
    $stmt->execute([$data['review_id'], $userId]);
    
    if (!$stmt->fetch()) {
        sendError('Review not found or access denied', 404);
    }
    
    // Update review
    $stmt = $pdo->prepare("
        UPDATE AvisSurProprietes 
        SET note = ?, commentaire = ?, date_avis = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    $stmt->execute([
        $data['note'],
        sanitizeInput($data['commentaire']),
        $data['review_id']
    ]);
    
    sendSuccess(null, 'Review updated successfully');
    
} catch (PDOException $e) {
    error_log("Update Property Review Error: " . $e->getMessage());
    sendError('Failed to update review', 500);
}
