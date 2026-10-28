<?php
/**
 * Authentication Middleware
 * Verify user session and permissions
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/response.php';

/**
 * Check if user is authenticated
 */
function requireAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        sendError('Authentication required', 401);
    }
    
    return $_SESSION['user_id'];
}

/**
 * Get current user information
 */
function getCurrentUser() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("
            SELECT id, nom, email, photo_profil, date_inscription, statut_hote
            FROM Utilisateurs
            WHERE id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Get Current User Error: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if user is admin
 */
function requireAdmin() {
    $userId = requireAuth();
    
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT id FROM Admin WHERE utilisateur_id = ?");
        $stmt->execute([$userId]);
        
        if (!$stmt->fetch()) {
            sendError('Admin access required', 403);
        }
        
        return $userId;
    } catch (PDOException $e) {
        error_log("Admin Check Error: " . $e->getMessage());
        sendError('Authorization check failed', 500);
    }
}

/**
 * Check if user owns a resource
 */
function requireOwnership($resourceType, $resourceId, $ownerField = 'proprietaire_id') {
    $userId = requireAuth();
    
    try {
        $pdo = getConnection();
        
        // Map resource types to tables
        $tables = [
            'property' => 'Logements',
            'reservation' => 'DemandesReservation'
        ];
        
        if (!isset($tables[$resourceType])) {
            sendError('Invalid resource type', 400);
        }
        
        $table = $tables[$resourceType];
        
        // Special handling for reservations (need to check through annonce->logement)
        if ($resourceType === 'reservation') {
            $stmt = $pdo->prepare("
                SELECT l.proprietaire_id
                FROM DemandesReservation dr
                JOIN Annonces a ON dr.annonce_id = a.id
                JOIN Logements l ON a.logement_id = l.id
                WHERE dr.id = ?
            ");
            $stmt->execute([$resourceId]);
            $result = $stmt->fetch();
            
            if (!$result || $result['proprietaire_id'] != $userId) {
                sendError('Access denied', 403);
            }
        } else {
            $stmt = $pdo->prepare("SELECT $ownerField FROM $table WHERE id = ?");
            $stmt->execute([$resourceId]);
            $result = $stmt->fetch();
            
            if (!$result || $result[$ownerField] != $userId) {
                sendError('Access denied', 403);
            }
        }
        
        return $userId;
    } catch (PDOException $e) {
        error_log("Ownership Check Error: " . $e->getMessage());
        sendError('Authorization check failed', 500);
    }
}
