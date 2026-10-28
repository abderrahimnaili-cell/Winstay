<?php
/**
 * OTP Verification Endpoint
 * POST /api/auth/verify_otp.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    $data = getJsonInput();
    validateRequired($data, ['email', 'otp']);
    
    $email = sanitizeInput($data['email']);
    $otp = sanitizeInput($data['otp']);
    
    $pdo = getConnection();
    
    // 1. Find the temporary record
    $stmt = $pdo->prepare("SELECT * FROM Utilisateurs_Temp WHERE email = ? AND otp_code = ?");
    $stmt->execute([$email, $otp]);
    $tempUser = $stmt->fetch();
    
    if (!$tempUser) {
        sendError('Invalid verification code', 400);
    }
    
    // 2. Check expiration
    if (strtotime($tempUser['expires_at']) < time()) {
        sendError('Verification code has expired', 400);
    }
    
    // 3. Move to main Utilisateurs table
    $stmt = $pdo->prepare("
        INSERT INTO Utilisateurs (nom, email, mot_de_passe)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$tempUser['nom'], $tempUser['email'], $tempUser['mot_de_passe']]);
    $userId = $pdo->lastInsertId();
    
    // 4. Clean up temp record
    $stmt = $pdo->prepare("DELETE FROM Utilisateurs_Temp WHERE id = ?");
    $stmt->execute([$tempUser['id']]);
    
    // 5. Create session
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_nom'] = $tempUser['nom'];
    $_SESSION['user_email'] = $tempUser['email'];
    
    // Get full user data
    $stmt = $pdo->prepare("SELECT id, nom, email, photo_profil, date_inscription, statut_hote FROM Utilisateurs WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    $user['is_admin'] = false;
    $user['total_logements'] = 0;

    sendSuccess($user, 'Email verified successfully! Welcome to WINSTAY.');
    
} catch (PDOException $e) {
    error_log("OTP Verification Error: " . $e->getMessage());
    sendError('Verification failed', 500);
}
