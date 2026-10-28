<?php
/**
 * Update User Profile Endpoint
 * PUT /api/users/update.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

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
    $userId = requireAuth();
    
    $data = getJsonInput();
    $pdo = getConnection();
    
    $updates = [];
    $params = [];
    
    // Update name if provided
    if (isset($data['nom']) && trim($data['nom']) !== '') {
        $updates[] = "nom = ?";
        $params[] = sanitizeInput($data['nom']);
    }
    
    // Update email if provided
    if (isset($data['email']) && trim($data['email']) !== '') {
        $email = sanitizeInput($data['email']);
        
        if (!validateEmail($email)) {
            sendError('Invalid email format', 400);
        }
        
        // Check if email is already used by another user
        $stmt = $pdo->prepare("SELECT id FROM Utilisateurs WHERE email = ? AND id != ?");
        $stmt->execute([$email, $userId]);
        
        if ($stmt->fetch()) {
            sendError('Email already in use', 409);
        }
        
        $updates[] = "email = ?";
        $params[] = $email;
    }
    
    // Update password if provided
    if (isset($data['mot_de_passe']) && trim($data['mot_de_passe']) !== '') {
        if (strlen($data['mot_de_passe']) < 6) {
            sendError('Password must be at least 6 characters long', 400);
        }
        
        $updates[] = "mot_de_passe = ?";
        $params[] = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);
    }

    // Update photo_profil if key exists (supports null for removal)
    if (array_key_exists('photo_profil', $data)) {
        $updates[] = "photo_profil = ?";
        $params[] = $data['photo_profil'];
    }
    
    if (empty($updates)) {
        sendError('No fields to update', 400);
    }
    
    $params[] = $userId;
    
    $sql = "UPDATE Utilisateurs SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    // Get updated user data
    $stmt = $pdo->prepare("SELECT id, nom, email, photo_profil FROM Utilisateurs WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    sendSuccess($user, 'Profile updated successfully');
    
} catch (PDOException $e) {
    error_log("Update Profile Error: " . $e->getMessage());
    sendError('Failed to update profile', 500);
}
