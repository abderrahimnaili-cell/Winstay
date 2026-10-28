<?php
/**
 * Contact Host Endpoint
 * POST /api/messages/contact_host.php
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
    $guestId = requireAuth();
    $data = getJsonInput();

    validateRequired($data, ['property_id', 'message']);

    $propertyId = (int)$data['property_id'];
    $message = sanitizeInput($data['message']);

    $pdo = getConnection();

    // Get Guest Info
    $stmtGuest = $pdo->prepare("SELECT nom, email FROM Utilisateurs WHERE id = ?");
    $stmtGuest->execute([$guestId]);
    $guest = $stmtGuest->fetch();
    
    if (!$guest) {
        sendError('Guest not found', 404);
    }

    // Get Property and Host Info
    $stmtProperty = $pdo->prepare("
        SELECT l.titre, u.id as host_id, u.nom as host_name, u.email as host_email 
        FROM Logements l 
        JOIN Utilisateurs u ON l.proprietaire_id = u.id 
        WHERE l.id = ?
    ");
    $stmtProperty->execute([$propertyId]);
    $property = $stmtProperty->fetch();

    if (!$property) {
        sendError('Property or Host not found', 404);
    }

    // Temporarily disabled so you can test sending messages to your own properties
    // if ($property['host_id'] == $guestId) {
    //     sendError('You cannot contact yourself', 400);
    // }

    // Send Email
    $mailSent = Mailer::sendHostMessage(
        $property['host_email'], 
        $property['host_name'], 
        $guest['nom'], 
        $guest['email'], 
        $property['titre'], 
        $message
    );

    if ($mailSent) {
        sendSuccess(null, 'Message sent successfully');
    } else {
        // Even if mail fails locally, we might want to pretend success or show error.
        // For standard UX, we can return error if it actually failed.
        sendError('Could not dispatch email. Please try again later.', 500);
    }

} catch (PDOException $e) {
    error_log("Contact Host Error: " . $e->getMessage());
    sendError('Failed to process request', 500);
} catch (Exception $e) {
    error_log("Contact Host Error: " . $e->getMessage());
    sendError($e->getMessage(), 500);
}
