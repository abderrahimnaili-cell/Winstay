<?php
/**
 * Update Property Endpoint
 * PUT /api/properties/update.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    sendError('Method not allowed', 405);
}

try {
    requireAuth();
    
    $data = getJsonInput();
    
    if (!isset($data['id'])) {
        sendError('Property ID is required', 400);
    }
    
    $propertyId = (int)$data['id'];
    
    // Verify ownership
    requireOwnership('property', $propertyId);
    
    $pdo = getConnection();
    
    $updates = [];
    $params = [];
    
    // Build dynamic update query
    $updateableFields = [
        'titre', 'description', 'wilaya', 'adresse', 'latitude', 'longitude', 'prix_par_nuit',
        'nombre_invites', 'nombre_chambres', 'nombre_lits',
        'nombre_salles_bain', 'categorie', 'cancel_policy', 'house_rules', 'safety_rules', 'statut'
    ];
    
    foreach ($updateableFields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = in_array($field, ['titre', 'description', 'wilaya', 'categorie', 'statut']) 
                ? sanitizeInput($data[$field]) 
                : $data[$field];
        }
    }
    
    if (!empty($updates)) {
        $params[] = $propertyId;
        $sql = "UPDATE Logements SET " . implode(", ", $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    // Update Espaces (Amenities)
    if (isset($data['espaces']) && is_array($data['espaces'])) {
        // Delete existing spaces
        $stmt = $pdo->prepare("DELETE FROM Espaces WHERE logement_id = ?");
        $stmt->execute([$propertyId]);

        // Insert new spaces
        $stmt = $pdo->prepare("INSERT INTO Espaces (logement_id, type_espace) VALUES (?, ?)");
        foreach ($data['espaces'] as $espace) {
            $stmt->execute([$propertyId, sanitizeInput($espace)]);
        }
    }

    // Delete images if requested
    if (isset($data['deleted_images']) && is_array($data['deleted_images']) && !empty($data['deleted_images'])) {
        $deleteIds = array_map('intval', $data['deleted_images']);
        $placeholders = implode(',', array_fill(0, count($deleteIds), '?'));
        
        // Fetch files to delete from disk
        $stmt = $pdo->prepare("SELECT url_image FROM Images WHERE logement_id = ? AND id IN ($placeholders)");
        $stmt->execute(array_merge([$propertyId], $deleteIds));
        $imagesToDelete = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        require_once __DIR__ . '/../../utils/upload.php';
        foreach ($imagesToDelete as $filepath) {
            deleteImage($filepath);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM Images WHERE logement_id = ? AND id IN ($placeholders)");
        $stmt->execute(array_merge([$propertyId], $deleteIds));
    }

    // Update main image (swap URL to make it the lowest ID)
    if (isset($data['main_image_id']) && !empty($data['main_image_id'])) {
        $mainImageId = (int)$data['main_image_id'];
        
        // Find the image with the lowest ID for this logement
        $stmt = $pdo->prepare("SELECT id, url_image FROM Images WHERE logement_id = ? ORDER BY id ASC LIMIT 1");
        $stmt->execute([$propertyId]);
        $firstImage = $stmt->fetch();
        
        if ($firstImage && $firstImage['id'] != $mainImageId) {
            // Get URL of the selected main image
            $stmt = $pdo->prepare("SELECT url_image FROM Images WHERE id = ? AND logement_id = ?");
            $stmt->execute([$mainImageId, $propertyId]);
            $selectedImageUrl = $stmt->fetchColumn();
            
            if ($selectedImageUrl) {
                // Swap the URLs!
                $pdo->beginTransaction();
                try {
                    $stmt = $pdo->prepare("UPDATE Images SET url_image = ? WHERE id = ?");
                    $stmt->execute([$firstImage['url_image'], $mainImageId]);
                    $stmt->execute([$selectedImageUrl, $firstImage['id']]);
                    $pdo->commit();
                } catch (Exception $e) {
                    $pdo->rollBack();
                }
            }
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
            WHERE id = (SELECT proprietaire_id FROM Logements WHERE id = ?)
        ");
        $stmt->execute([
            isset($data['host_bio']) && !empty($data['host_bio']) ? sanitizeInput($data['host_bio']) : null,
            isset($data['host_profession']) && !empty($data['host_profession']) ? sanitizeInput($data['host_profession']) : null,
            isset($data['host_since']) && !empty($data['host_since']) ? sanitizeInput($data['host_since']) : null,
            isset($data['host_languages']) && !empty($data['host_languages']) ? sanitizeInput($data['host_languages']) : null,
            $propertyId
        ]);
    }

    sendSuccess(['id' => $propertyId], 'Property updated successfully');
    
} catch (PDOException $e) {
    error_log("Update Property Error: " . $e->getMessage());
    sendError('Failed to update property', 500);
}
