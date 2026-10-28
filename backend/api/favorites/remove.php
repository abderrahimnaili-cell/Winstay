<?php
/**
 * Remove from Favorites Endpoint
 * DELETE /api/favorites/remove.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    sendError('Method not allowed', 405);
}

try {
    $userId = requireAuth();
    
    $data = getJsonInput();
    validateRequired($data, ['logement_id']);
    
    $logementId = (int)$data['logement_id'];
    $pdo = getConnection();
    
    // Remove from favorites
    $stmt = $pdo->prepare("
        DELETE FROM Favoris
        WHERE utilisateur_id = ? AND logement_id = ?
    ");
    $stmt->execute([$userId, $logementId]);
    
    if ($stmt->rowCount() === 0) {
        sendError('Favorite not found', 404);
    }
    
    sendSuccess(null, 'Removed from favorites');
    
} catch (PDOException $e) {
    error_log("Remove Favorite Error: " . $e->getMessage());
    sendError('Failed to remove favorite', 500);
}
