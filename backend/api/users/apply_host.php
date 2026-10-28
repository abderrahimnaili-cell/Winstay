<?php
/**
 * Submit Host Application
 * POST /api/users/apply_host.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    // Requires authenticated user
    $userId = requireAuth();

    // Use $_POST instead of JSON because of FormData
    if (!isset($_POST['nom_complet']) || !isset($_POST['telephone']) || !isset($_FILES['carte_national'])) {
        sendError('Missing required fields', 400);
    }

    $nomComplet = sanitizeInput($_POST['nom_complet']);
    $telephone = sanitizeInput($_POST['telephone']);
    
    // Handle file upload
    $file = $_FILES['carte_national'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        sendError('File upload failed', 400);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!in_array($file['type'], $allowedTypes)) {
        sendError('Invalid file type. Only JPG, PNG, and PDF are allowed.', 400);
    }
    
    $uploadDir = __DIR__ . '/../../../uploads/ids/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('id_') . '.' . $extension;
    $destination = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        sendError('Failed to save uploaded file', 500);
    }
    
    $carteNationalPath = 'uploads/ids/' . $filename;

    $pdo = getConnection();

    // Check current status
    $stmt = $pdo->prepare("SELECT statut_hote FROM Utilisateurs WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if ($user['statut_hote'] === 'approuve') {
        sendError('You are already an approved host', 400);
    }
    if ($user['statut_hote'] === 'en_attente') {
        sendError('You already have a pending host application', 400);
    }

    $pdo->beginTransaction();

    try {
        // Insert application
        $stmt = $pdo->prepare("
            INSERT INTO DemandesHote (utilisateur_id, nom_complet, telephone, carte_national, statut) 
            VALUES (?, ?, ?, ?, 'en_attente')
        ");
        $stmt->execute([$userId, $nomComplet, $telephone, $carteNationalPath]);

        // Update user status
        $stmt = $pdo->prepare("UPDATE Utilisateurs SET statut_hote = 'en_attente' WHERE id = ?");
        $stmt->execute([$userId]);

        $pdo->commit();

        sendSuccess(null, 'Host application submitted successfully. Please wait for admin approval.');

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    error_log("Apply Host Error: " . $e->getMessage());
    sendError('Failed to submit application', 500);
}
