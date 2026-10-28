<?php
/**
 * Delete Property Endpoint
 * DELETE /api/properties/delete.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../utils/upload.php';

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
    requireAuth();
    
    $data = getJsonInput();
    
    if (!isset($data['id'])) {
        sendError('Property ID is required', 400);
    }
    
    $propertyId = (int)$data['id'];
    
    // Verify ownership
    requireOwnership('property', $propertyId);
    
    $pdo = getConnection();
    
    // Get all images to delete them from filesystem
    $stmt = $pdo->prepare("SELECT url_image FROM Images WHERE logement_id = ?");
    $stmt->execute([$propertyId]);
    $images = $stmt->fetchAll();
    
    // Delete property (cascade will handle related records)
    $stmt = $pdo->prepare("DELETE FROM Logements WHERE id = ?");
    $stmt->execute([$propertyId]);
    
    // Delete image files
    foreach ($images as $image) {
        deleteImage($image['url_image']);
    }
    
    sendSuccess(null, 'Property deleted successfully');
    
} catch (PDOException $e) {
    error_log("Delete Property Error: " . $e->getMessage());
    sendError('Failed to delete property', 500);
}
