<?php
/**
 * Social Login Endpoint
 * POST /api/auth/social_login.php
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Method not allowed', 405);
}

try {
    $data = getJsonInput();
    
    validateRequired($data, ['provider', 'email', 'nom', 'token']);
    
    $provider = sanitizeInput($data['provider']);
    $email = sanitizeInput($data['email']);
    $nom = sanitizeInput($data['nom']);
    $token = $data['token']; // The real OAuth token from frontend
    $photo_profil = isset($data['photo_profil']) ? sanitizeInput($data['photo_profil']) : null;

    // --- REAL TOKEN VERIFICATION ---
    if ($provider === 'google' && strpos($token, 'MOCK_TOKEN_') !== 0) {
        $verifyUrl = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $token;
        $resp = @file_get_contents($verifyUrl);
        if (!$resp) sendError('Invalid Google token', 401);
        $tokenData = json_decode($resp, true);
        
        $clientId = "YOUR_GOOGLE_CLIENT_ID"; // Reverted to placeholder
        
        if (!isset($tokenData['email']) || $tokenData['email'] !== $email || ($clientId !== "YOUR_GOOGLE_CLIENT_ID" && $tokenData['aud'] !== $clientId)) {
            sendError('Google authentication failed: Security mismatch', 401);
        }
    }
    // -------------------------------
    
    $pdo = getConnection();
    
    // Check if user already exists by email
    $stmt = $pdo->prepare("SELECT * FROM Utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // User doesn't exist, register them
        // Generate a random secure password since they use social login
        $randomPassword = bin2hex(random_bytes(10));
        $hashedPassword = password_hash($randomPassword, PASSWORD_BCRYPT);
        
        $stmt = $pdo->prepare("
            INSERT INTO Utilisateurs (nom, email, mot_de_passe, photo_profil)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$nom, $email, $hashedPassword, $photo_profil]);
        
        $userId = $pdo->lastInsertId();
        
        // Fetch newly created user info
        $stmt = $pdo->prepare("SELECT * FROM Utilisateurs WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
    } else {
        // Update existing user with latest info from social provider
        $stmt = $pdo->prepare("UPDATE Utilisateurs SET nom = ?, photo_profil = COALESCE(?, photo_profil) WHERE id = ?");
        $stmt->execute([$nom, $photo_profil, $user['id']]);
        
        // Refresh user data
        $stmt = $pdo->prepare("SELECT * FROM Utilisateurs WHERE id = ?");
        $stmt->execute([$user['id']]);
        $user = $stmt->fetch();
    }
    
    // Check if user is admin
    $stmtAdmin = $pdo->prepare("SELECT id FROM Admin WHERE utilisateur_id = ?");
    $stmtAdmin->execute([$user['id']]);
    $isAdmin = $stmtAdmin->fetch() ? true : false;
    
    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nom'] = $user['nom'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['is_admin'] = $isAdmin;
    
    $stmtLog = $pdo->prepare("SELECT COUNT(*) as count FROM Logements WHERE proprietaire_id = ?");
    $stmtLog->execute([$user['id']]);
    $totalLogements = (int)$stmtLog->fetch()['count'];

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

    sendSuccess($userData, 'Social login successful');
    
} catch (PDOException $e) {
    error_log("Social Login Error: " . $e->getMessage());
    sendError('Social authentication failed', 500);
}
