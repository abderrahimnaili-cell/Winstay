<?php
/**
 * Get Property Details Endpoint
 * GET /api/properties/get.php?id={property_id}
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
    if (!isset($_GET['id'])) {
        sendError('Property ID is required', 400);
    }
    
    $propertyId = (int)$_GET['id'];
    $pdo = getConnection();
    
    $dateArrivee = isset($_GET['date_arrivee']) ? $_GET['date_arrivee'] : null;
    $dateDepart = isset($_GET['date_depart']) ? $_GET['date_depart'] : null;

    $isBookedSubquery = "";
    $isBookedParams = [];
    if ($dateArrivee && $dateDepart) {
        $isBookedSubquery = "
            (SELECT COUNT(*) FROM demandesreservation dr 
             JOIN annonces a_sub ON dr.annonce_id = a_sub.id 
             WHERE a_sub.logement_id = l.id AND dr.statut = 'confirmee' 
             AND (
                (dr.date_arrivee <= ? AND dr.date_depart >= ?)
                OR (dr.date_arrivee <= ? AND dr.date_depart >= ?)
                OR (dr.date_arrivee >= ? AND dr.date_depart <= ?)
             )) > 0 as is_booked";
        $isBookedParams = [$dateArrivee, $dateArrivee, $dateDepart, $dateDepart, $dateArrivee, $dateDepart];
    } else {
        $isBookedSubquery = "
            (SELECT COUNT(*) FROM demandesreservation dr 
             JOIN annonces a_sub ON dr.annonce_id = a_sub.id 
             WHERE a_sub.logement_id = l.id AND dr.statut = 'confirmee' 
             AND CURDATE() BETWEEN dr.date_arrivee AND dr.date_depart) > 0 as is_booked";
    }

    // Get property details
    $stmt = $pdo->prepare("
        SELECT 
            l.*,
            u.id as proprietaire_id,
            u.nom as proprietaire_nom,
            u.email as proprietaire_email,
            u.photo_profil as proprietaire_photo,
            u.date_inscription as proprietaire_date_inscription,
            u.bio as proprietaire_bio,
            u.hote_depuis as proprietaire_since,
            u.profession as proprietaire_profession,
            u.langues as proprietaire_languages,
            $isBookedSubquery,
            (SELECT MAX(dr.date_depart) FROM demandesreservation dr 
             JOIN annonces a_sub ON dr.annonce_id = a_sub.id 
             WHERE a_sub.logement_id = l.id AND dr.statut = 'confirmee' 
             AND dr.date_depart >= CURDATE()) as booked_until
        FROM logements l
        JOIN utilisateurs u ON l.proprietaire_id = u.id
        WHERE l.id = ?
    ");
    $stmt->execute(array_merge($isBookedParams, [$propertyId]));
    $property = $stmt->fetch();
    
    if (!$property) {
        sendError('Property not found', 404);
    }
    
    // Get property images
    $stmt = $pdo->prepare("
        SELECT id, url_image
        FROM images
        WHERE logement_id = ?
        ORDER BY id ASC
    ");
    $stmt->execute([$propertyId]);
    $property['images'] = $stmt->fetchAll();

    // Get all confirmed blocked dates for the calendar
    $stmt = $pdo->prepare("
        SELECT dr.date_arrivee, dr.date_depart 
        FROM demandesreservation dr
        JOIN annonces a ON dr.annonce_id = a.id
        WHERE a.logement_id = ? AND dr.statut = 'confirmee' AND dr.date_depart >= CURDATE()
    ");
    $stmt->execute([$propertyId]);
    $property['blocked_dates'] = $stmt->fetchAll();
    
    
    // Get espaces with their images
    $stmt = $pdo->prepare("
        SELECT e.id, e.type_espace
        FROM espaces e
        WHERE e.logement_id = ?
    ");
    $stmt->execute([$propertyId]);
    $espaces = $stmt->fetchAll();
    
    foreach ($espaces as &$espace) {
        $stmt = $pdo->prepare("
            SELECT id, url_image
            FROM images
            WHERE espace_id = ?
        ");
        $stmt->execute([$espace['id']]);
        $espace['images'] = $stmt->fetchAll();
    }
    $property['espaces'] = $espaces;
    
    // Get reviews and ratings
    $stmt = $pdo->prepare("
        SELECT 
            asp.*,
            dr.utilisateur_id,
            u.nom as utilisateur_nom,
            u.photo_profil as utilisateur_photo
        FROM avissurproprietes asp
        JOIN demandesreservation dr ON asp.demande_reservation_id = dr.id
        JOIN annonces a ON dr.annonce_id = a.id
        JOIN utilisateurs u ON dr.utilisateur_id = u.id
        WHERE a.logement_id = ?
        ORDER BY asp.date_avis DESC
    ");
    $stmt->execute([$propertyId]);
    $property['reviews'] = $stmt->fetchAll();
    
    // Calculate average rating
    $stmt = $pdo->prepare("
        SELECT 
            AVG(asp.note) as moyenne_note,
            COUNT(*) as total_avis
        FROM avissurproprietes asp
        JOIN demandesreservation dr ON asp.demande_reservation_id = dr.id
        JOIN annonces a ON dr.annonce_id = a.id
        WHERE a.logement_id = ?
    ");
    $stmt->execute([$propertyId]);
    $rating = $stmt->fetch();
    $property['moyenne_note'] = $rating['moyenne_note'] ? round($rating['moyenne_note'], 1) : null;
    $property['total_avis'] = (int)$rating['total_avis'];
    
    // Get annonce ID for bookings
    $stmt = $pdo->prepare("SELECT id FROM annonces WHERE logement_id = ?");
    $stmt->execute([$propertyId]);
    $annonce = $stmt->fetch();
    $property['annonce_id'] = $annonce ? $annonce['id'] : null;
    
    // Get all confirmed reservation dates (blocked dates)
    $stmt = $pdo->prepare("
        SELECT date_arrivee, date_depart
        FROM DemandesReservation dr
        JOIN Annonces a ON dr.annonce_id = a.id
        WHERE a.logement_id = ? AND dr.statut = 'confirmee' AND dr.date_depart >= CURDATE()
    ");
    $stmt->execute([$propertyId]);
    $property['blocked_dates'] = $stmt->fetchAll();
    
    sendSuccess($property, 'Property details retrieved successfully');
    
} catch (PDOException $e) {
    error_log("Get Property Error: " . $e->getMessage());
    sendError('Failed to retrieve property details', 500);
}
