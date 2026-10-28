<?php
/**
 * Approve or Reject Host Request
 * POST /api/admin/approve_host.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../middleware/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    // Requires admin access
    requireAdmin();
    $data = getJsonInput();

    // Validate fields
    validateRequired($data, ['demande_id', 'action']);

    $demandeId = (int)$data['demande_id'];
    $action = $data['action']; // 'approve' or 'reject'

    if (!in_array($action, ['approve', 'reject'])) {
        sendError('Invalid action', 400);
    }

    $pdo = getConnection();

    // Check if application exists
    $stmt = $pdo->prepare("SELECT utilisateur_id, statut FROM DemandesHote WHERE id = ?");
    $stmt->execute([$demandeId]);
    $demande = $stmt->fetch();

    if (!$demande) {
        sendError('Request not found', 404);
    }

    if ($demande['statut'] !== 'en_attente') {
        sendError('Request is not pending', 400);
    }

    // Fetch applicant info BEFORE the transaction to ensure it's always available
    $stmtUser = $pdo->prepare("SELECT nom, email FROM Utilisateurs WHERE id = ?");
    $stmtUser->execute([$demande['utilisateur_id']]);
    $applicant = $stmtUser->fetch();

    if (!$applicant || empty($applicant['email'])) {
        sendError('Applicant not found or missing email', 404);
    }

    $newStatutDemande = ($action === 'approve') ? 'approuvee' : 'rejetee';
    $newStatutUtilisateur = ($action === 'approve') ? 'approuve' : 'rejete';
    $motifRejet = ($action === 'reject') ? ($data['motif_rejet'] ?? null) : null;

    $pdo->beginTransaction();

    try {
        // Update request status (with motif_rejet if rejecting)
        if ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE DemandesHote SET statut = ?, motif_rejet = ? WHERE id = ?");
            $stmt->execute([$newStatutDemande, $motifRejet, $demandeId]);
        } else {
            $stmt = $pdo->prepare("UPDATE DemandesHote SET statut = ? WHERE id = ?");
            $stmt->execute([$newStatutDemande, $demandeId]);
        }

        // Update user status
        $stmt = $pdo->prepare("UPDATE Utilisateurs SET statut_hote = ? WHERE id = ?");
        $stmt->execute([$newStatutUtilisateur, $demande['utilisateur_id']]);

        $pdo->commit();

        // Send email notification after successful DB update
        try {
            require_once __DIR__ . '/../../includes/mailer.php';
            if ($action === 'approve') {
                Mailer::sendHostApproval($applicant['email'], $applicant['nom']);
            } else {
                Mailer::sendHostRejection($applicant['email'], $applicant['nom'], $motifRejet);
            }
        } catch (Exception $e) {
            // Log email failure but don't fail the whole request
            error_log("Host Application Email Failed [" . strtoupper($action) . "] to " . $applicant['email'] . ": " . $e->getMessage());
        }

        $message = ($action === 'approve') ? 'Request approved successfully' : 'Request rejected successfully';
        sendSuccess(null, $message);

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    error_log("Approve Host Error: " . $e->getMessage());
    sendError('Failed to process request', 500);
}
