<?php
/**
 * Upload Property Images Endpoint
 * POST /api/properties/upload-images.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../utils/upload.php';

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
    requireAuth();
    
    if (!isset($_POST['logement_id'])) {
        sendError('Property ID is required', 400);
    }
    
    $logementId = (int)$_POST['logement_id'];
    
    // Verify ownership
    requireOwnership('property', $logementId);
    
    if (!isset($_FILES['images'])) {
        sendError('No images uploaded', 400);
    }
    
    // Upload images
    $result = uploadMultipleImages($_FILES['images'], 'uploads/properties');
    
    if (!$result['success']) {
        sendError('No images were uploaded successfully', 400, $result['errors']);
    }
    
    // Insert image records
    $pdo = getConnection();
    $stmt = $pdo->prepare("INSERT INTO Images (logement_id, url_image) VALUES (?, ?)");
    
    $uploadedImages = [];
    foreach ($result['files'] as $filepath) {
        $stmt->execute([$logementId, $filepath]);
        $uploadedImages[] = [
            'id' => $pdo->lastInsertId(),
            'url_image' => $filepath
        ];
    }
    
    sendSuccess([
        'images' => $uploadedImages,
        'errors' => $result['errors']
    ], count($uploadedImages) . ' image(s) uploaded successfully');
    
} catch (PDOException $e) {
    error_log("Upload Images Error: " . $e->getMessage());
    sendError('Failed to upload images', 500);
}
