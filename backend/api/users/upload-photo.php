<?php

/**
 * Upload Profile Photo Endpoint
 * POST /api/users/upload-photo.php
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
    $userId = requireAuth();
    
    if (!isset($_FILES['photo'])) {
        sendError('No file uploaded', 400);
    }
    
    // Upload image
    $result = uploadImage($_FILES['photo'], 'uploads/profiles');
    
    if (!$result['success']) {
        sendError($result['message'], 400);
    }
    
    $pdo = getConnection();
    
    // Get old photo to delete it
    $stmt = $pdo->prepare("SELECT photo_profil FROM Utilisateurs WHERE id = ?");
    $stmt->execute([$userId]);
    $oldPhoto = $stmt->fetch()['photo_profil'];
    
    // Update user photo
    $stmt = $pdo->prepare("UPDATE Utilisateurs SET photo_profil = ? WHERE id = ?");
    $stmt->execute([$result['path'], $userId]);
    
    // Delete old photo if exists
    if ($oldPhoto) {
        deleteImage($oldPhoto);
    }
    
    sendSuccess(['photo_profil' => $result['path']], 'Profile photo updated successfully');
    
} catch (PDOException $e) {
    error_log("Upload Photo Error: " . $e->getMessage());
    sendError('Failed to upload photo', 500);
}
