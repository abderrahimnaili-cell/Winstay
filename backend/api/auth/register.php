<?php
/**
 * User Registration Endpoint
 * POST /api/auth/register.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    // Get input data
    $data = getJsonInput();
    
    // DEBUG LOG
    file_put_contents(__DIR__ . '/../../debug_reg.txt', date('Y-m-d H:i:s') . " | Attempt: " . json_encode($data) . "\n", FILE_APPEND);
    
    // Validate required fields
    validateRequired($data, ['nom', 'email', 'mot_de_passe']);
    
    // Sanitize inputs
    $nom = sanitizeInput($data['nom']);
    $email = sanitizeInput($data['email']);
    $motDePasse = $data['mot_de_passe'];
    
    // 1. Strict Email Validation
    $emailValidation = Mailer::validateEmail($email);
    if (!$emailValidation['success']) {
        sendError($emailValidation['message'], 400);
    }
    
    // Validate password strength (minimum 6 characters)
    if (strlen($motDePasse) < 6) {
        sendError('Password must be at least 6 characters long', 400);
    }
    
    // Get database connection
    $pdo = getConnection();
    
    // 2. Check if email already exists in main table
    $stmt = $pdo->prepare("SELECT id FROM Utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        sendError('Email already registered', 409);
    }
    
    // 3. Generate 6-digit OTP
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    $hashedPassword = password_hash($motDePasse, PASSWORD_BCRYPT);
    
    // 4. Store in Temporary table
    $stmt = $pdo->prepare("
        INSERT INTO Utilisateurs_Temp (nom, email, mot_de_passe, otp_code, expires_at)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            nom = VALUES(nom),
            mot_de_passe = VALUES(mot_de_passe),
            otp_code = VALUES(otp_code),
            expires_at = VALUES(expires_at)
    ");
    $stmt->execute([$nom, $email, $hashedPassword, $otp, $expiresAt]);
    
    // 5. Send OTP Email
    Mailer::sendOTP($email, $otp, $nom);
    
    // Return success indicating verification is needed
    sendSuccess(['email' => $email, 'verification_required' => true], 'Verification code sent to your email', 200);
    
} catch (PDOException $e) {
    error_log("Registration Error: " . $e->getMessage());
    sendError('Registration failed', 500);
}

