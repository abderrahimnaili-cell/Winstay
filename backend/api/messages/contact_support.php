<?php
/**
 * Contact Support Endpoint
 * POST /api/messages/contact_support.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    // 1. Require user to be logged in
    $userId = requireAuth();

    $data = getJsonInput();
    validateRequired($data, ['message']);
    $message = sanitizeInput($data['message']);

    // 2. Fetch real email and name from Database
    $pdo = getConnection();
    $stmtUser = $pdo->prepare("SELECT nom, email FROM Utilisateurs WHERE id = ?");
    $stmtUser->execute([$userId]);
    $user = $stmtUser->fetch();
    
    if (!$user) {
        sendError('User not found', 404);
    }

    $name = $user['nom'];
    $email = $user['email'];

    // Admin email pulled from config
    $config = require __DIR__ . '/../../config/mail.php';
    $adminEmail = $config['from_email']; // Sending to the system email (acting as admin)
    
    // We reuse sendHostMessage to dispatch the email to admin without modifying Mailer logic
    $mailSent = Mailer::sendHostMessage(
        $adminEmail, 
        "Admin", 
        $name, 
        $email, 
        "Help Center Ticket", 
        $message
    );

    if ($mailSent) {
        sendSuccess(null, 'Message sent to support successfully');
    } else {
        sendError('Could not dispatch email. Please try again later.', 500);
    }

} catch (PDOException $e) {
    error_log("Contact Support DB Error: " . $e->getMessage());
    sendError('Failed to process request', 500);
} catch (Exception $e) {
    error_log("Contact Support Error: " . $e->getMessage());
    sendError($e->getMessage(), 500);
}
