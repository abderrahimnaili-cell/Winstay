<?php
/**
 * Add/Remove Favorites Endpoint
 * POST /api/favorites/add.php
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
    validateRequired($data, ['logement_id']);
    
    $logementId = (int)$data['logement_id'];
    $pdo = getConnection();
    
    // Check if property exists
    $stmt = $pdo->prepare("SELECT id FROM Logements WHERE id = ?");
    $stmt->execute([$logementId]);
    
    if (!$stmt->fetch()) {
        sendError('Property not found', 404);
    }
    
    // Check if already favorited
    $stmt = $pdo->prepare("
        SELECT id FROM Favoris
        WHERE utilisateur_id = ? AND logement_id = ?
    ");
    $stmt->execute([$userId, $logementId]);
    
    if ($stmt->fetch()) {
        sendError('Property already in favorites', 409);
    }
    
    // Add to favorites
    $stmt = $pdo->prepare("
        INSERT INTO Favoris (utilisateur_id, logement_id)
        VALUES (?, ?)
    ");
    $stmt->execute([$userId, $logementId]);
    
    sendSuccess(['id' => $pdo->lastInsertId()], 'Added to favorites', 201);
    
} catch (PDOException $e) {
    error_log("Add Favorite Error: " . $e->getMessage());
    sendError('Failed to add favorite', 500);
}
