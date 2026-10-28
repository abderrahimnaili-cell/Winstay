<?php
/**
 * Resend OTP Endpoint
 * POST /api/auth/resend_otp.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    $data = getJsonInput();
    validateRequired($data, ['email']);
    
    $email = sanitizeInput($data['email']);
    
    $pdo = getConnection();
    
    // 1. Check if email exists in Utilisateurs_Temp (for registration or reset)
    $stmt = $pdo->prepare("SELECT nom FROM Utilisateurs_Temp WHERE email = ?");
    $stmt->execute([$email]);
    $tempUser = $stmt->fetch();
    
    if (!$tempUser) {
        // If not in Temp, check if it's an existing user who needs a reset resend
        $stmt = $pdo->prepare("SELECT nom FROM Utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $activeUser = $stmt->fetch();
        
        if (!$activeUser) {
            sendError('No pending verification found for this email', 404);
        }
        
        // If it's an active user, they should have been in Temp if they requested a reset,
        // but let's be safe and re-insert if they are active (like forgot_password does)
        $nom = $activeUser['nom'];
    } else {
        $nom = $tempUser['nom'];
    }
    
    // 2. Generate New OTP
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
    // 3. Update Utilisateurs_Temp
    $stmt = $pdo->prepare("
        UPDATE Utilisateurs_Temp 
        SET otp_code = ?, expires_at = ? 
        WHERE email = ?
    ");
    $stmt->execute([$otp, $expiresAt, $email]);
    
    if ($stmt->rowCount() === 0) {
        // If update failed (maybe record was deleted), re-insert if we have the name
        // (This part is optional but good for robustness)
        $stmt = $pdo->prepare("
            INSERT INTO Utilisateurs_Temp (nom, email, mot_de_passe, otp_code, expires_at)
            VALUES (?, ?, 'RESEND_PENDING', ?, ?)
            ON DUPLICATE KEY UPDATE 
                otp_code = VALUES(otp_code),
                expires_at = VALUES(expires_at)
        ");
        $stmt->execute([$nom, $email, $otp, $expiresAt]);
    }
    
    // 4. Send Email
    Mailer::sendOTP($email, $otp, $nom);
    
    sendSuccess(['email' => $email], 'New verification code sent to your email');
    
} catch (PDOException $e) {
    error_log("Resend OTP Error: " . $e->getMessage());
    sendError('Failed to resend code', 500);
}
