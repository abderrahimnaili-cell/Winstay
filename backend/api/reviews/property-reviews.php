<?php
/**
 * Get Property Reviews Endpoint
 * GET /api/reviews/property-reviews.php?logement_id={id}
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';

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
    if (!isset($_GET['logement_id'])) {
        sendError('Property ID is required', 400);
    }
    
    $logementId = (int)$_GET['logement_id'];
    $pdo = getConnection();
    
    // Get reviews
    $stmt = $pdo->prepare("
        SELECT 
            asp.*,
            u.nom as utilisateur_nom,
            u.photo_profil as utilisateur_photo,
            dr.utilisateur_id,
            dr.date_arrivee,
            dr.date_depart
        FROM AvisSurProprietes asp
        JOIN DemandesReservation dr ON asp.demande_reservation_id = dr.id
        JOIN Utilisateurs u ON dr.utilisateur_id = u.id
        JOIN Annonces a ON dr.annonce_id = a.id
        WHERE a.logement_id = ?
        ORDER BY asp.date_avis DESC
    ");
    $stmt->execute([$logementId]);
    $reviews = $stmt->fetchAll();
    
    // Get average rating
    $stmt = $pdo->prepare("
        SELECT 
            AVG(asp.note) as moyenne_note,
            COUNT(*) as total_reviews
        FROM AvisSurProprietes asp
        JOIN DemandesReservation dr ON asp.demande_reservation_id = dr.id
        JOIN Annonces a ON dr.annonce_id = a.id
        WHERE a.logement_id = ?
    ");
    $stmt->execute([$logementId]);
    $stats = $stmt->fetch();

    $canReview = false;
    $eligibleReservationId = null;

    // Check if user is logged in and eligible to review
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        
        // Check for eligible reservation (confirmee or completee) that hasn't been reviewed
        $stmt = $pdo->prepare("
            SELECT dr.id 
            FROM DemandesReservation dr
            JOIN Annonces a ON dr.annonce_id = a.id
            WHERE a.logement_id = ? 
            AND dr.utilisateur_id = ? 
            AND dr.statut IN ('confirmee', 'completee')
            AND dr.id NOT IN (SELECT demande_reservation_id FROM AvisSurProprietes)
            LIMIT 1
        ");
        $stmt->execute([$logementId, $userId]);
        $eligible = $stmt->fetch();
        
        if ($eligible) {
            $canReview = true;
            $eligibleReservationId = $eligible['id'];
        }
    }

    sendSuccess([
        'reviews' => $reviews,
        'moyenne_note' => $stats['moyenne_note'] ? round($stats['moyenne_note'], 1) : null,
        'total_reviews' => (int)$stats['total_reviews'],
        'can_review' => $canReview,
        'eligible_reservation_id' => $eligibleReservationId
    ], 'Property reviews retrieved successfully');
    
} catch (PDOException $e) {
    error_log("Get Property Reviews Error: " . $e->getMessage());
    sendError('Failed to retrieve reviews', 500);
}
