<?php
/**
 * User Profile Endpoint
 * GET /api/users/profile.php?id={user_id}
 * If no ID provided, returns current user's profile
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

try {
    requireAuth();
    
    $pdo = getConnection();
    
    // Get user ID from query param or use current user
    $userId = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];
    
    // Get user information
    $stmt = $pdo->prepare("
        SELECT id, nom, email, photo_profil, date_inscription
        FROM Utilisateurs
        WHERE id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        sendError('User not found', 404);
    }
    
    // Get user statistics
    // Count properties owned
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM Logements WHERE proprietaire_id = ?");
    $stmt->execute([$userId]);
    $user['total_logements'] = $stmt->fetch()['count'];
    
    // Count reservations made (as guest)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM DemandesReservation WHERE utilisateur_id = ?");
    $stmt->execute([$userId]);
    $user['total_reservations'] = $stmt->fetch()['count'];
    
    // Count reservations received (as host)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM DemandesReservation dr
        JOIN Annonces a ON dr.annonce_id = a.id
        JOIN Logements l ON a.logement_id = l.id
        WHERE l.proprietaire_id = ?
    ");
    $stmt->execute([$userId]);
    $user['total_bookings_received'] = $stmt->fetch()['count'];
    
    // Get average rating
    $stmt = $pdo->prepare("
        SELECT AVG(note) as avg_note, COUNT(*) as total_avis
        FROM AvisSurUtilisateurs
        WHERE utilisateur_id = ?
    ");
    $stmt->execute([$userId]);
    $rating = $stmt->fetch();
    $user['average_rating'] = $rating['avg_note'] ? round($rating['avg_note'], 1) : null;
    $user['total_reviews'] = $rating['total_avis'];
    
    sendSuccess($user, 'User profile retrieved');
    
} catch (PDOException $e) {
    error_log("Profile Error: " . $e->getMessage());
    sendError('Failed to retrieve profile', 500);
}
