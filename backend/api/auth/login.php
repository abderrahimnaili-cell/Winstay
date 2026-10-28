<?php
/**
 * User Login Endpoint
 * POST /api/auth/login.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    // Get input data
    $data = getJsonInput();
    
    // Validate required fields
    validateRequired($data, ['email', 'mot_de_passe']);
    
    $email = sanitizeInput($data['email']);
    $motDePasse = $data['mot_de_passe'];
    
    // Get database connection
    $pdo = getConnection();
    
    // Find user by email
    $stmt = $pdo->prepare("
        SELECT id, nom, email, mot_de_passe, photo_profil, date_inscription, statut_hote
        FROM Utilisateurs
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        sendError('No account found with this email. Please sign up first.', 404);
    }
    
    // Verify password
    if (!password_verify($motDePasse, $user['mot_de_passe'])) {
        sendError('Incorrect password. Please try again.', 401);
    }
    
    // Check if user is admin
    $stmt = $pdo->prepare("SELECT id FROM Admin WHERE utilisateur_id = ?");
    $stmt->execute([$user['id']]);
    $isAdmin = $stmt->fetch() ? true : false;
    
    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nom'] = $user['nom'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['is_admin'] = $isAdmin;
    
    // Prepare user data (exclude password)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM Logements WHERE proprietaire_id = ?");
    $stmt->execute([$user['id']]);
    $totalLogements = (int)$stmt->fetch()['count'];

    $userData = [
        'id' => $user['id'],
        'nom' => $user['nom'],
        'email' => $user['email'],
        'photo_profil' => $user['photo_profil'],
        'date_inscription' => $user['date_inscription'],
        'statut_hote' => $user['statut_hote'],
        'is_admin' => $isAdmin,
        'total_logements' => $totalLogements
    ];

    sendSuccess($userData, 'Login successful');
    
} catch (PDOException $e) {
    error_log("Login Error: " . $e->getMessage());
    sendError('Login failed', 500);
}
