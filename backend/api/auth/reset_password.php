<?php
/**
 * Reset Password Endpoint
 * POST /api/auth/reset_password.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    $data = getJsonInput();
    validateRequired($data, ['email', 'otp', 'new_password']);
    
    $email = sanitizeInput($data['email']);
    $otp = sanitizeInput($data['otp']);
    $newPassword = $data['new_password'];
    
    $pdo = getConnection();
    
    // 1. Verify OTP
    $stmt = $pdo->prepare("SELECT * FROM Utilisateurs_Temp WHERE email = ? AND otp_code = ?");
    $stmt->execute([$email, $otp]);
    $temp = $stmt->fetch();
    
    if (!$temp || strtotime($temp['expires_at']) < time()) {
        sendError('Invalid or expired reset code', 400);
    }
    
    // 2. Update Password in main table
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE Utilisateurs SET mot_de_passe = ? WHERE email = ?");
    $stmt->execute([$hashedPassword, $email]);
    
    // 3. Cleanup
    $stmt = $pdo->prepare("DELETE FROM Utilisateurs_Temp WHERE id = ?");
    $stmt->execute([$temp['id']]);
    
    sendSuccess(null, 'Password updated successfully! You can now log in.');
    
} catch (PDOException $e) {
    error_log("Reset Password Error: " . $e->getMessage());
    sendError('Failed to update password', 500);
}
