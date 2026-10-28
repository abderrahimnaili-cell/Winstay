<?php
/**
 * List Properties Endpoint
 * GET /api/properties/list.php
 * 
 * Query parameters:
 * - wilaya: filter by wilaya
 * - categorie: filter by category
 * - prix_min: minimum price
 * - prix_max: maximum price
 * - invites: number of guests
 * - search: search in title/description
 * - page: page number (default 1)
 * - limit: items per page (default 20)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';

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
    $pdo = getConnection();
    
    // Build query with filters
    $where = ["l.statut IN ('disponible', 'occupe')"];
    $params = [];
    
    if (isset($_GET['wilaya']) && $_GET['wilaya'] !== '') {
        $where[] = "l.wilaya = ?";
        $params[] = $_GET['wilaya'];
    }
    
    if (isset($_GET['categorie']) && $_GET['categorie'] !== '') {
        $where[] = "l.categorie = ?";
        $params[] = $_GET['categorie'];
    }
    
    if (isset($_GET['prix_min']) && $_GET['prix_min'] !== '') {
        $where[] = "l.prix_par_nuit >= ?";
        $params[] = $_GET['prix_min'];
    }
    
    if (isset($_GET['prix_max']) && $_GET['prix_max'] !== '') {
        $where[] = "l.prix_par_nuit <= ?";
        $params[] = $_GET['prix_max'];
    }
    
    if (isset($_GET['invites']) && $_GET['invites'] !== '') {
        $where[] = "l.nombre_invites >= ?";
        $params[] = $_GET['invites'];
    }
    
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        $where[] = "(l.titre LIKE ? OR l.description LIKE ?)";
        $searchTerm = '%' . $_GET['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    // Date filtering logic
    $dateArrivee = isset($_GET['date_arrivee']) ? $_GET['date_arrivee'] : null;
    $dateDepart = isset($_GET['date_depart']) ? $_GET['date_depart'] : null;

    if ($dateArrivee && $dateDepart) {
        $where[] = "l.id NOT IN (
            SELECT a_sub.logement_id 
            FROM DemandesReservation dr 
            JOIN Annonces a_sub ON dr.annonce_id = a_sub.id 
            WHERE dr.statut = 'confirmee'
            AND (
                (dr.date_arrivee <= ? AND dr.date_depart >= ?)
                OR (dr.date_arrivee <= ? AND dr.date_depart >= ?)
                OR (dr.date_arrivee >= ? AND dr.date_depart <= ?)
            )
        )";
        $params[] = $dateArrivee;
        $params[] = $dateArrivee;
        $params[] = $dateDepart;
        $params[] = $dateDepart;
        $params[] = $dateArrivee;
        $params[] = $dateDepart;
    }

    $whereClause = implode(' AND ', $where);
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM Logements l WHERE $whereClause";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    
    // Pagination
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
    $offset = ($page - 1) * $limit;
    
    // Get properties
    $currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Define is_booked logic based on provided dates or current date
    $isBookedSubquery = "";
    $isBookedParams = [];
    if ($dateArrivee && $dateDepart) {
        $isBookedSubquery = "
            (SELECT COUNT(*) FROM DemandesReservation dr 
             JOIN Annonces a_sub ON dr.annonce_id = a_sub.id 
             WHERE a_sub.logement_id = l.id AND dr.statut = 'confirmee' 
             AND (
                (dr.date_arrivee <= ? AND dr.date_depart >= ?)
                OR (dr.date_arrivee <= ? AND dr.date_depart >= ?)
                OR (dr.date_arrivee >= ? AND dr.date_depart <= ?)
             )) > 0 as is_booked";
        $isBookedParams = [$dateArrivee, $dateArrivee, $dateDepart, $dateDepart, $dateArrivee, $dateDepart];
    } else {
        $isBookedSubquery = "
            (SELECT COUNT(*) FROM DemandesReservation dr 
             JOIN Annonces a_sub ON dr.annonce_id = a_sub.id 
             WHERE a_sub.logement_id = l.id AND dr.statut = 'confirmee' 
             AND CURDATE() BETWEEN dr.date_arrivee AND dr.date_depart) > 0 as is_booked";
    }

    $sql = "
        SELECT 
            l.*,
            u.nom as proprietaire_nom,
            u.photo_profil as proprietaire_photo,
            (SELECT url_image FROM Images WHERE logement_id = l.id LIMIT 1) as image_principale,
            (SELECT AVG(note) FROM AvisSurProprietes asp 
             JOIN DemandesReservation dr ON asp.demande_reservation_id = dr.id
             JOIN Annonces a ON dr.annonce_id = a.id
             WHERE a.logement_id = l.id) as moyenne_note,
            (SELECT COUNT(*) FROM AvisSurProprietes asp
             JOIN DemandesReservation dr ON asp.demande_reservation_id = dr.id
             JOIN Annonces a ON dr.annonce_id = a.id
             WHERE a.logement_id = l.id) as total_avis,
            $isBookedSubquery,
            (SELECT MAX(dr.date_depart) FROM DemandesReservation dr 
             JOIN Annonces a_sub ON dr.annonce_id = a_sub.id 
             WHERE a_sub.logement_id = l.id AND dr.statut = 'confirmee' 
             AND dr.date_depart >= CURDATE()) as booked_until,
            " . ($currentUserId ? "($currentUserId = l.proprietaire_id)" : "0") . " as is_mine,
            " . ($currentUserId ? "(SELECT COUNT(*) FROM Favoris WHERE logement_id = l.id AND utilisateur_id = $currentUserId) > 0" : "0") . " as is_favorited,
            " . ($currentUserId ? "(SELECT COUNT(*) FROM DemandesReservation dr JOIN Annonces a_sub ON dr.annonce_id = a_sub.id WHERE a_sub.logement_id = l.id AND dr.utilisateur_id = $currentUserId AND dr.statut IN ('en_attente','confirmee') AND dr.date_depart >= CURDATE()) > 0" : "0") . " as is_my_booking
        FROM Logements l
        JOIN Utilisateurs u ON l.proprietaire_id = u.id
        WHERE $whereClause
        ORDER BY l.date_creation DESC
        LIMIT ? OFFSET ?
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmtParams = array_merge($isBookedParams, $params, [$limit, $offset]);
    $stmt->execute($stmtParams);
    $properties = $stmt->fetchAll();
    
    // Format results
    foreach ($properties as &$property) {
        $property['moyenne_note'] = $property['moyenne_note'] ? round($property['moyenne_note'], 1) : null;
        $property['total_avis'] = (int)$property['total_avis'];
    }
    
    sendSuccess([
        'properties' => $properties,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'total_pages' => ceil($total / $limit)
        ]
    ], 'Properties retrieved successfully');
    
} catch (PDOException $e) {
    error_log("List Properties Error: " . $e->getMessage());
    sendError('Failed to retrieve properties', 500);
}
