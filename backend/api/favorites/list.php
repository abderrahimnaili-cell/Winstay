<?php
/**
 * Get User Favorites Endpoint
 * GET /api/favorites/list.php
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
    
    // Get user's favorite properties
    $stmt = $pdo->prepare("
        SELECT 
            f.*,
            l.*,
            u.nom as proprietaire_nom,
            u.photo_profil as proprietaire_photo,
            (SELECT url_image FROM Images WHERE logement_id = l.id LIMIT 1) as image_principale,
            (SELECT AVG(note) FROM AvisSurProprietes asp
             JOIN DemandesReservation dr ON asp.demande_reservation_id = dr.id
             JOIN Annonces a ON dr.annonce_id = a.id
             WHERE a.logement_id = l.id) as moyenne_note
        FROM Favoris f
        JOIN Logements l ON f.logement_id = l.id
        JOIN Utilisateurs u ON l.proprietaire_id = u.id
        WHERE f.utilisateur_id = ?
        ORDER BY f.date_ajout DESC
    ");
    $stmt->execute([$userId]);
    $favorites = $stmt->fetchAll();
    
    // Format results
    foreach ($favorites as &$favorite) {
        $favorite['moyenne_note'] = $favorite['moyenne_note'] ? round($favorite['moyenne_note'], 1) : null;
    }
    
    sendSuccess($favorites, 'Favorites retrieved successfully');
    
} catch (PDOException $e) {
    error_log("Get Favorites Error: " . $e->getMessage());
    sendError('Failed to retrieve favorites', 500);
}
