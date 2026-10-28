<?php
/**
 * Create Property Endpoint
 * POST /api/properties/create.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    $userId = requireAuth();
    
    $data = getJsonInput();
    
    // Validate required fields
    validateRequired($data, [
        'titre', 'wilaya', 'prix_par_nuit', 'nombre_invites',
        'nombre_chambres', 'nombre_lits', 'nombre_salles_bain', 'categorie'
    ]);
    
    $pdo = getConnection();
    $pdo->beginTransaction();
    
    // Insert property
    $stmt = $pdo->prepare("
        INSERT INTO Logements (
            proprietaire_id, titre, description, wilaya, adresse, latitude, longitude, prix_par_nuit,
            nombre_invites, nombre_chambres, nombre_lits, nombre_salles_bain,
            categorie, cancel_policy, house_rules, safety_rules, statut
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $userId,
        sanitizeInput($data['titre']),
        isset($data['description']) ? sanitizeInput($data['description']) : null,
        sanitizeInput($data['wilaya']),
        isset($data['adresse']) ? sanitizeInput($data['adresse']) : null,
        isset($data['latitude']) ? $data['latitude'] : null,
        isset($data['longitude']) ? $data['longitude'] : null,
        $data['prix_par_nuit'],
        $data['nombre_invites'],
        $data['nombre_chambres'],
        $data['nombre_lits'],
        $data['nombre_salles_bain'],
        $data['categorie'],
        isset($data['cancel_policy']) ? sanitizeInput($data['cancel_policy']) : null,
        isset($data['house_rules']) ? sanitizeInput($data['house_rules']) : null,
        isset($data['safety_rules']) ? sanitizeInput($data['safety_rules']) : null,
        'disponible'
    ]);
    
    $logementId = $pdo->lastInsertId();
    
    // Create annonce for this property
    $stmt = $pdo->prepare("INSERT INTO Annonces (logement_id, type_usage) VALUES (?, ?)");
    $typeUsage = isset($data['type_usage']) ? sanitizeInput($data['type_usage']) : 'Vacation';
    $stmt->execute([$logementId, $typeUsage]);
    
    // Insert espaces if provided
    if (isset($data['espaces']) && is_array($data['espaces'])) {
        $stmt = $pdo->prepare("INSERT INTO Espaces (logement_id, type_espace) VALUES (?, ?)");
        foreach ($data['espaces'] as $espace) {
            $stmt->execute([$logementId, sanitizeInput($espace)]);
        }
    }
    
    // Update host profile if info provided
    if (isset($data['host_bio']) || isset($data['host_profession']) || isset($data['host_since']) || isset($data['host_languages'])) {
        $stmt = $pdo->prepare("
            UPDATE Utilisateurs 
            SET bio = COALESCE(?, bio), 
                profession = COALESCE(?, profession), 
                hote_depuis = COALESCE(?, hote_depuis), 
                langues = COALESCE(?, langues) 
            WHERE id = ?
        ");
        $stmt->execute([
            isset($data['host_bio']) && !empty($data['host_bio']) ? sanitizeInput($data['host_bio']) : null,
            isset($data['host_profession']) && !empty($data['host_profession']) ? sanitizeInput($data['host_profession']) : null,
            isset($data['host_since']) && !empty($data['host_since']) ? sanitizeInput($data['host_since']) : null,
            isset($data['host_languages']) && !empty($data['host_languages']) ? sanitizeInput($data['host_languages']) : null,
            $userId
        ]);
    }
    
    $pdo->commit();
    
    sendSuccess([
        'id' => $logementId,
        'message' => 'Property created successfully. You can now upload images.'
    ], 'Property created successfully', 201);
    
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Create Property Error: " . $e->getMessage());
    sendError('Failed to create property', 500);
}
