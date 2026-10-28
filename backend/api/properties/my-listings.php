<?php
/**
 * Get User's Properties Endpoint
 * GET /api/properties/my-listings.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

try {
    $userId = requireAuth();
    
    $pdo = getConnection();
    
    // Get user's properties
    $stmt = $pdo->prepare("
        SELECT 
            l.*,
            (SELECT url_image FROM Images WHERE logement_id = l.id LIMIT 1) as image_principale,
            (SELECT COUNT(*) FROM DemandesReservation dr
             JOIN Annonces a ON dr.annonce_id = a.id
             WHERE a.logement_id = l.id) as total_reservations,
            (SELECT COUNT(*) FROM DemandesReservation dr
             JOIN Annonces a ON dr.annonce_id = a.id
             WHERE a.logement_id = l.id AND dr.statut = 'en_attente') as reservations_en_attente,
            (SELECT AVG(note) FROM AvisSurProprietes asp
             JOIN DemandesReservation dr ON asp.demande_reservation_id = dr.id
             JOIN Annonces a ON dr.annonce_id = a.id
             WHERE a.logement_id = l.id) as moyenne_note
        FROM Logements l
        WHERE l.proprietaire_id = ?
        ORDER BY l.date_creation DESC
    ");
    $stmt->execute([$userId]);
    $properties = $stmt->fetchAll();
    
    // Format results
    foreach ($properties as &$property) {
        $property['moyenne_note'] = $property['moyenne_note'] ? round($property['moyenne_note'], 1) : null;
        $property['total_reservations'] = (int)$property['total_reservations'];
        $property['reservations_en_attente'] = (int)$property['reservations_en_attente'];
    }
    
    sendSuccess($properties, 'User properties retrieved successfully');
    
} catch (PDOException $e) {
    error_log("Get My Listings Error: " . $e->getMessage());
    sendError('Failed to retrieve properties', 500);
}
