<?php
/**
 * Get Pending Host Requests
 * GET /api/admin/host_requests.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendError('Method not allowed', 405);
}

try {
    // Requires admin access
    requireAdmin();

    $pdo = getConnection();

    $stmt = $pdo->prepare("
        SELECT 
            d.id, 
            d.utilisateur_id, 
            d.nom_complet, 
            d.telephone, 
            d.carte_national,
            d.date_demande,
            u.email,
            u.photo_profil
        FROM DemandesHote d
        JOIN Utilisateurs u ON d.utilisateur_id = u.id
        WHERE d.statut = 'en_attente'
        ORDER BY d.date_demande ASC
    ");
    $stmt->execute();
    
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendSuccess($requests, 'Pending requests fetched successfully');

} catch (PDOException $e) {
    error_log("Get Host Requests Error: " . $e->getMessage());
    sendError('Failed to fetch requests', 500);
}
