<?php
/**
 * Update Reservation Status Endpoint
 * PUT /api/reservations/update-status.php
 * For property owners to confirm/cancel/complete reservations
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
    validateRequired($data, ['id', 'statut']);
    
    $reservationId = (int)$data['id'];
    $newStatus = $data['statut'];
    
    // Validate status
    $validStatuses = ['confirmee', 'annulee', 'completee'];
    if (!in_array($newStatus, $validStatuses)) {
        sendError('Invalid status', 400);
    }
    
    // Verify ownership
    requireOwnership('reservation', $reservationId);
    
    $pdo = getConnection();
    
    // Get reservation details for notification
    $stmt = $pdo->prepare("
        SELECT dr.utilisateur_id, l.titre
        FROM DemandesReservation dr
        JOIN Annonces a ON dr.annonce_id = a.id
        JOIN Logements l ON a.logement_id = l.id
        WHERE dr.id = ?
    ");
    $stmt->execute([$reservationId]);
    $reservation = $stmt->fetch();
    
    // Update status (Primary Task)
    $stmt = $pdo->prepare("UPDATE DemandesReservation SET statut = ? WHERE id = ?");
    $stmt->execute([$newStatus, $reservationId]);
    
    if ($stmt->rowCount() === 0) {
        // This might happen if status is already set to the new value
        // We'll continue anyway to ensure the emails are sent if needed
    }

    // If confirmed: auto-reject all other overlapping pending requests for the same property
    if ($newStatus === 'confirmee') {
        // Get the confirmed reservation's dates and annonce_id
        $stmtDates = $pdo->prepare("
            SELECT dr.annonce_id, dr.date_arrivee, dr.date_depart, u.nom, u.email
            FROM DemandesReservation dr
            JOIN Utilisateurs u ON dr.utilisateur_id = u.id
            WHERE dr.id = ?
        ");
        $stmtDates->execute([$reservationId]);
        $confirmed = $stmtDates->fetch();

        if ($confirmed) {
            // Send Confirmation Email to Guest (Safely)
            try {
                require_once __DIR__ . '/../../includes/mailer.php';
                $dateStr = $confirmed['date_arrivee'] . " to " . $confirmed['date_depart'];
                Mailer::sendBookingConfirmation($confirmed['email'], $confirmed['nom'], $reservation['titre'], $dateStr);
            } catch (Exception $e) {
                error_log("Confirmation Email Failed: " . $e->getMessage());
            }

            // Find conflicting pending requests (Smart Date Overlap Check)
            $stmt = $pdo->prepare("
                SELECT id FROM DemandesReservation
                WHERE annonce_id = ?
                AND id != ?
                AND statut = 'en_attente'
                AND date_arrivee < ? 
                AND date_depart > ?
            ");
            $stmt->execute([
                $confirmed['annonce_id'],
                $reservationId,
                $confirmed['date_depart'],
                $confirmed['date_arrivee']
            ]);
            $conflicting = $stmt->fetchAll();
            
            if (!empty($conflicting)) {
                $ids = array_column($conflicting, 'id');
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                
                // Automatically Reject them
                $stmt = $pdo->prepare("UPDATE DemandesReservation SET statut = 'annulee' WHERE id IN ($placeholders)");
                $stmt->execute($ids);

                // Send rejection emails to all auto-rejected guests
                foreach ($conflicting as $conflict) {
                    $stmtGuest = $pdo->prepare("
                        SELECT dr.date_arrivee, dr.date_depart, u.nom, u.email, l.titre
                        FROM DemandesReservation dr
                        JOIN Utilisateurs u ON dr.utilisateur_id = u.id
                        JOIN Annonces a ON dr.annonce_id = a.id
                        JOIN Logements l ON a.logement_id = l.id
                        WHERE dr.id = ?
                    ");
                    $stmtGuest->execute([$conflict['id']]);
                    $guestData = $stmtGuest->fetch();

                    if ($guestData) {
                        try {
                            $dateStr = $guestData['date_arrivee'] . " to " . $guestData['date_depart'];
                            Mailer::sendBookingRejection($guestData['email'], $guestData['nom'], $guestData['titre'], $dateStr);
                        } catch (Exception $e) {
                            error_log("Auto-Rejection Email Failed: " . $e->getMessage());
                        }
                    }
                }
            }
        }
    }

    // If rejected by host: send rejection email to guest
    if ($newStatus === 'annulee') {
        $stmtGuest = $pdo->prepare("
            SELECT dr.date_arrivee, dr.date_depart, u.nom, u.email, l.titre
            FROM DemandesReservation dr
            JOIN Utilisateurs u ON dr.utilisateur_id = u.id
            JOIN Annonces a ON dr.annonce_id = a.id
            JOIN Logements l ON a.logement_id = l.id
            WHERE dr.id = ?
        ");
        $stmtGuest->execute([$reservationId]);
        $guestData = $stmtGuest->fetch();

        if ($guestData) {
            try {
                require_once __DIR__ . '/../../includes/mailer.php';
                $dateStr = $guestData['date_arrivee'] . " to " . $guestData['date_depart'];
                Mailer::sendBookingRejection($guestData['email'], $guestData['nom'], $guestData['titre'], $dateStr);
            } catch (Exception $e) {
                error_log("Rejection Email Failed: " . $e->getMessage());
            }
        }
    }

    // Create notification for guest
    /* 
    $messages = [
        'confirmee' => 'Your reservation has been confirmed!',
        'annulee'   => 'Your reservation has been cancelled by the host.',
        'completee' => 'Your stay is complete. Please leave a review!'
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO Notifications (utilisateur_id, type_notification, message)
        VALUES (?, 'reservation_update', ?)
    ");
    $stmt->execute([
        $reservation['utilisateur_id'],
        $messages[$newStatus]
    ]);
    */
    
    sendSuccess(['statut' => $newStatus], 'Reservation status updated successfully');
    
} catch (PDOException $e) {
    error_log("Update Reservation Status Error: " . $e->getMessage());
    sendError('Failed to update reservation status', 500);
}
