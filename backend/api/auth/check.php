<?php
/**
 * Session Check Endpoint
 * GET /api/auth/check.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

try {
    $user = getCurrentUser();
    
    if (!$user) {
        sendError('Not authenticated', 401);
    }
    
    // Check if admin
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id FROM Admin WHERE utilisateur_id = ?");
    $stmt->execute([$user['id']]);
    $isAdmin = $stmt->fetch() ? true : false;
    
    $user['is_admin'] = $isAdmin;

    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM Logements WHERE proprietaire_id = ?");
    $stmt->execute([$user['id']]);
    $user['total_logements'] = (int)$stmt->fetch()['count'];

    sendSuccess($user, 'User session active');
    
} catch (Exception $e) {
    error_log("Session Check Error: " . $e->getMessage());
    sendError('Session check failed', 500);
}
