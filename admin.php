<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/backend/config/database.php';
require_once __DIR__ . '/backend/includes/mailer.php';

$error = '';
$isLoggedIn = false;
$adminName = '';

$pdo = getConnection();

// Check if any admins exist
$adminCount = $pdo->query("SELECT COUNT(*) FROM Admin")->fetchColumn();
$noAdminsExist = ($adminCount == 0);

// Handle initial admin setup
if ($noAdminsExist && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_admin'])) {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $hashedPass = password_hash($pass, PASSWORD_BCRYPT);

    if ($email !== 'winstaybba18@gmail.com') {
        $error = 'You must use winstaybba18@gmail.com as the administrator email.';
    } else {
        try {
            $pdo->beginTransaction();
            
            // 1. Create or get user
            $stmt = $pdo->prepare("SELECT id FROM Utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                $userId = $user['id'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO Utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
                $stmt->execute([$nom, $email, $hashedPass]);
                $userId = $pdo->lastInsertId();
            }
            
            // 2. Make admin
            $stmt = $pdo->prepare("INSERT INTO Admin (utilisateur_id) VALUES (?)");
            $stmt->execute([$userId]);
            
            $pdo->commit();
            
            // 3. Log them in automatically
            $_SESSION['admin_logged'] = true;
            $_SESSION['admin_id']     = $userId;
            $_SESSION['admin_name']   = $nom;
            
            header('Location: admin.php');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Setup failed: ' . $e->getMessage();
        }
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && !isset($_POST['setup_admin'])) {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    if ($email !== 'winstaybba18@gmail.com') {
        $error = 'This account does not have admin privileges.';
    } else {
        $stmt = $pdo->prepare("SELECT id, nom, mot_de_passe FROM Utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['mot_de_passe'])) {
            // Check if admin
            $stmtA = $pdo->prepare("SELECT id FROM Admin WHERE utilisateur_id = ?");
            $stmtA->execute([$user['id']]);
            if ($stmtA->fetch()) {
                $_SESSION['admin_logged'] = true;
                $_SESSION['admin_id']     = $user['id'];
                $_SESSION['admin_name']   = $user['nom'];
            } else {
                $error = 'This account does not have admin privileges.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged'], $_SESSION['admin_id'], $_SESSION['admin_name']);
    header('Location: admin.php');
    exit;
}

// Check if already logged in as admin
if (!empty($_SESSION['admin_logged']) && !empty($_SESSION['admin_id'])) {
    $isLoggedIn = true;
    $adminName = $_SESSION['admin_name'];
}

// Handle approve/reject/reset via POST — must run BEFORE display data fetch
if ($isLoggedIn && isset($_POST['action'], $_POST['demande_id'])) {
    $action    = $_POST['action'];
    $demandeId = (int)$_POST['demande_id'];
    $pdo = getConnection();

    // Fetch applicant info first (before any DB changes)
    $stmt = $pdo->prepare("
        SELECT d.utilisateur_id, u.nom, u.email
        FROM DemandesHote d
        JOIN Utilisateurs u ON d.utilisateur_id = u.id
        WHERE d.id = ?
    ");
    $stmt->execute([$demandeId]);
    $demande = $stmt->fetch();

    if ($demande) {
        $newStatutDemande     = 'en_attente';
        $newStatutUtilisateur = 'en_attente';
        $motifRejet           = null;

        if ($action === 'approve') {
            $newStatutDemande     = 'approuvee';
            $newStatutUtilisateur = 'approuve';
        } elseif ($action === 'reject') {
            $newStatutDemande     = 'rejetee';
            $newStatutUtilisateur = 'rejete';
            $motifRejet           = $_POST['motif_rejet'] ?? null;
        }

        // Update DB
        if ($action === 'reject') {
            $pdo->prepare("UPDATE DemandesHote SET statut = ?, motif_rejet = ? WHERE id = ?")->execute([$newStatutDemande, $motifRejet, $demandeId]);
        } elseif ($action === 'reset') {
            $pdo->prepare("UPDATE DemandesHote SET statut = ?, motif_rejet = NULL WHERE id = ?")->execute([$newStatutDemande, $demandeId]);
        } else {
            $pdo->prepare("UPDATE DemandesHote SET statut = ? WHERE id = ?")->execute([$newStatutDemande, $demandeId]);
        }
        $pdo->prepare("UPDATE Utilisateurs SET statut_hote = ? WHERE id = ?")->execute([$newStatutUtilisateur, $demande['utilisateur_id']]);

        // Send email notification for approve or reject
        if ($action === 'approve' || $action === 'reject') {
            try {
                if ($action === 'approve') {
                    $mailSent = Mailer::sendHostApproval($demande['email'], $demande['nom']);
                    $_SESSION['mail_msg'] = $mailSent
                        ? "✅ Approval email sent to " . $demande['email']
                        : "❌ DB updated, but failed to send approval email.";
                } else {
                    $mailSent = Mailer::sendHostRejection($demande['email'], $demande['nom'], $motifRejet);
                    $_SESSION['mail_msg'] = $mailSent
                        ? "✅ Rejection email sent to " . $demande['email']
                        : "❌ DB updated, but failed to send rejection email.";
                }
            } catch (Exception $e) {
                error_log("Admin Email Error [" . strtoupper($action) . "]: " . $e->getMessage());
                $_SESSION['mail_msg'] = "❌ Email error: " . $e->getMessage();
            }
        }
    }

    $currentFilter = $_POST['current_filter'] ?? 'en_attente';
    header("Location: admin.php?filter=$currentFilter");
    exit;
}

// Fetch display data AFTER action processing
if ($isLoggedIn) {
    // Get filter status
    $filterStatus = $_GET['filter'] ?? 'en_attente';
    $validFilters = ['en_attente', 'approuvee', 'rejetee'];
    if (!in_array($filterStatus, $validFilters)) { $filterStatus = 'en_attente'; }

    $pdo = getConnection();
    $stmt = $pdo->prepare("
        SELECT d.id, d.nom_complet, d.telephone, d.carte_national, d.date_demande, d.statut, d.motif_rejet, u.email, u.id as user_id
        FROM DemandesHote d
        JOIN Utilisateurs u ON d.utilisateur_id = u.id
        WHERE d.statut = ?
        ORDER BY d.date_demande DESC
    ");
    $stmt->execute([$filterStatus]);
    $requests = $stmt->fetchAll();

    // Get counts for badge tabs
    $counts = $pdo->query("SELECT statut, COUNT(*) as total FROM DemandesHote GROUP BY statut")->fetchAll(PDO::FETCH_KEY_PAIR);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – WINSTAY</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;600&family=Montserrat:wght@200;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #121212;
            --text-color: #e0f7fa;
            --pink-accent: #00E0C6; /* Turquoise */
            --primary-color: var(--pink-accent);
            --menu-bg: rgba(30, 30, 30, 0.85);
            --border-color: rgba(0, 224, 198, 0.2);
            --card-bg: rgba(255, 255, 255, 0.05);
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.07);
            --radius-md: 24px;
            --shadow-premium: 0 0 20px rgba(0, 224, 198, 0.3);
            --transition-smooth: 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Montserrat', sans-serif; 
            background-color: var(--bg-color); 
            color: var(--text-color); 
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Background with Villa Blur */
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            background-image: url('https://images.unsplash.com/photo-1512917774080-9991f1c4c750?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            filter: blur(6px) brightness(0.3);
            transform: scale(1.05);
        }

        /* ── LOGIN / SETUP ── */
        .login-wrap {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: var(--menu-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 85px 45px 60px;
            width: 100%; max-width: 450px;
            text-align: center;
            box-shadow: var(--shadow-premium);
        }
        *:focus { outline: none !important; }
        html, body, div, span, h1, h2, h3, p, a, b, i, td, th {
            caret-color: transparent !important;
        }
        input, textarea, [contenteditable="true"] {
            caret-color: auto !important;
        }
        .login-logo {
            font-size: 32px; font-weight: 800;
            color: var(--pink-accent); letter-spacing: 4px;
            margin-bottom: 20px;
            margin-top: 15px;
            text-transform: uppercase;
            position: relative;
            cursor: pointer;
            user-select: none;
            -webkit-user-select: none;
        }
        .logo-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 10;
        }
        .login-sub { color: rgba(255,255,255,0.5); font-size: 14px; margin-bottom: 40px; font-weight: 300; }
        .login-card input {
            width: 100%; padding: 18px 20px;
            background: rgba(0,0,0,0.2);
            border: 1px solid var(--glass-border);
            border-radius: 15px; color: #fff;
            font-size: 15px; font-family: inherit;
            margin-bottom: 15px; outline: none;
            transition: 0.3s;
        }
        .login-card input:focus { border-color: var(--pink-accent); background: rgba(0,0,0,0.4); }
        
        .login-btn {
            width: 100%; padding: 18px;
            background: var(--pink-accent); color: #121212;
            border: none; border-radius: 15px;
            font-size: 16px; font-weight: 700;
            cursor: pointer; font-family: inherit;
            margin-top: 10px; transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 0 15px rgba(0, 224, 198, 0.4);
        }
        .login-btn:hover { 
            transform: scale(1.02);
            background: #00fae1;
            box-shadow: 0 0 25px rgba(0, 224, 198, 0.6);
        }
        
        .login-err {
            background: rgba(255,90,95,0.15);
            border: 1px solid rgba(255,90,95,0.4);
            color: #ff8e91; border-radius: 12px;
            padding: 14px; font-size: 14px;
            margin-bottom: 25px;
            display: flex; align-items: center; gap: 10px;
        }
        .back-home {
            display: inline-block; margin-top: 30px;
            color: rgba(255,255,255,0.4); font-size: 13px;
            text-decoration: none; transition: 0.2s;
        }
        .back-home:hover { color: var(--pink-accent); }

        /* ── DASHBOARD ── */
        .topbar {
            background: var(--menu-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--border-color);
            padding: 20px 5%;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 100;
        }
        .topbar-brand { font-size: 24px; font-weight: 800; color: var(--pink-accent); letter-spacing: 3px; text-transform: uppercase; }
        .topbar-brand span { color: rgba(255,255,255,0.5); font-size: 14px; font-weight: 300; margin-left: 15px; letter-spacing: 1px; }
        
        .topbar-right { display: flex; align-items: center; gap: 20px; }
        .topbar-user { font-size: 15px; color: var(--text-color); font-weight: 500; }
        
        .logout-btn {
            padding: 10px 20px; 
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border); 
            border-radius: 30px;
            color: var(--text-color); font-size: 13px; cursor: pointer;
            text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
            transition: 0.3s;
        }
        .logout-btn:hover { 
            border-color: var(--pink-accent); 
            background: rgba(0, 224, 198, 0.1);
            color: var(--pink-accent);
        }

        .page-body { max-width: 1100px; margin: 60px auto; padding: 0 5% 100px; }
        .page-title { 
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem; font-weight: 600; margin-bottom: 10px; 
        }
        .page-sub { color: rgba(255,255,255,0.5); font-size: 16px; margin-bottom: 50px; font-weight: 300; }

        .empty-state {
            text-align: center; padding: 100px 20px; 
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            color: rgba(255,255,255,0.4);
        }
        .empty-state i { font-size: 60px; margin-bottom: 20px; display: block; color: var(--pink-accent); opacity: 0.5; }

        /* Request Card */
        .req-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: 35px 40px;
            margin-bottom: 20px;
            display: flex; justify-content: space-between;
            align-items: center; gap: 40px;
            transition: var(--transition-smooth);
        }
        .req-card:hover { 
            border-color: var(--pink-accent); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            background: rgba(255,255,255,0.08);
            transform: translateY(-5px);
        }

        .req-name { font-size: 22px; font-weight: 700; margin-bottom: 15px; color: #fff; }
        .req-detail { font-size: 14px; color: rgba(255,255,255,0.7); margin-bottom: 8px; display: flex; align-items: center; gap: 12px; }
        .req-detail i { width: 18px; color: var(--pink-accent); text-align: center; }
        .req-date { font-size: 12px; color: rgba(255,255,255,0.3); margin-top: 15px; font-weight: 300; }
        
        .actions { display: flex; flex-direction: column; gap: 12px; flex-shrink: 0; min-width: 180px; }
        
        .btn-yes {
            width: 100%;
            padding: 12px 0; background: var(--pink-accent); color: #121212;
            border: none; border-radius: 12px; font-size: 14px;
            font-weight: 700; cursor: pointer; font-family: inherit;
            transition: 0.3s; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .btn-yes:hover { transform: scale(1.05); background: #00fae1; }
        
        .btn-no {
            width: 100%;
            padding: 12px 0; background: transparent;
            color: #ff5a5f; border: 1px solid #ff5a5f;
            border-radius: 12px; font-size: 14px; font-weight: 700;
            cursor: pointer; font-family: inherit;
            transition: 0.3s; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .btn-no:hover { background: #ff5a5f; color: #fff; }

        .badge-count {
            display: inline-flex; align-items: center; justify-content: center;
            background: var(--pink-accent); color: #121212;
            font-size: 13px; font-weight: 800;
            width: 26px; height: 26px; border-radius: 50%;
            margin-left: 10px;
            box-shadow: 0 0 10px rgba(0, 224, 198, 0.5);
        }

        /* ── TABS ── */
        .admin-tabs {
            display: flex; gap: 15px; margin-bottom: 40px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
        }
        .tab-link {
            padding: 12px 25px; border-radius: 30px;
            color: rgba(255,255,255,0.5); text-decoration: none;
            font-size: 14px; font-weight: 600;
            transition: 0.3s; border: 1px solid transparent;
            display: flex; align-items: center; gap: 10px;
        }
        .tab-link:hover { color: #fff; background: rgba(255,255,255,0.05); }
        .tab-link.active { 
            background: var(--pink-accent); color: #121212; 
            box-shadow: 0 0 15px rgba(0, 224, 198, 0.3);
        }
        .tab-link .t-count { 
            font-size: 11px; padding: 2px 8px; border-radius: 10px; 
            background: rgba(0,0,0,0.1); font-weight: 800;
        }
        .tab-link.active .t-count { background: rgba(0,0,0,0.2); }

        .btn-reset {
            width: 100%;
            padding: 12px 0; background: rgba(255,255,255,0.1);
            color: #fff; border: 1px solid var(--glass-border);
            border-radius: 12px; font-size: 14px; font-weight: 700;
            cursor: pointer; font-family: inherit;
            transition: 0.3s; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .btn-reset:hover { background: rgba(255,255,255,0.2); border-color: #fff; }

        /* ── MODAL STYLES ── */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex; align-items: center; justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-card {
            background: var(--menu-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            width: 90%; max-width: 550px;
            box-shadow: var(--shadow-premium), 0 20px 50px rgba(0,0,0,0.5);
            overflow: hidden;
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-overlay.active .modal-card {
            transform: scale(1);
        }
        .modal-header {
            padding: 25px 30px;
            border-bottom: 1px solid var(--glass-border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .modal-header h2 {
            font-size: 20px; font-weight: 700; color: #fff;
            display: flex; align-items: center;
        }
        .modal-close-btn {
            background: none; border: none; color: rgba(255,255,255,0.4);
            font-size: 28px; cursor: pointer; transition: 0.2s;
            line-height: 1;
        }
        .modal-close-btn:hover { color: #fff; }
        .modal-body {
            padding: 30px;
        }
        .modal-instruction {
            color: rgba(255,255,255,0.6);
            font-size: 14px; margin-bottom: 20px;
            line-height: 1.5;
        }
        .modal-body textarea {
            width: 100%; height: 150px;
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--glass-border);
            border-radius: 15px; color: #fff;
            padding: 15px 20px; font-size: 15px; font-family: inherit;
            resize: vertical; outline: none; transition: 0.3s;
            line-height: 1.6;
        }
        .modal-body textarea:focus {
            border-color: var(--pink-accent);
            box-shadow: 0 0 15px rgba(0, 224, 198, 0.2);
            background: rgba(0,0,0,0.5);
        }
        .modal-footer {
            padding: 20px 30px;
            background: rgba(0,0,0,0.2);
            border-top: 1px solid var(--glass-border);
            display: flex; justify-content: flex-end; gap: 15px;
        }
        .modal-btn-cancel {
            padding: 12px 25px; background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border); border-radius: 12px;
            color: var(--text-color); font-size: 14px; font-weight: 600;
            cursor: pointer; transition: 0.3s;
        }
        .modal-btn-cancel:hover {
            background: rgba(255,255,255,0.1);
        }
        .modal-btn-confirm {
            padding: 12px 25px; background: var(--pink-accent);
            border: none; border-radius: 12px;
            color: #121212; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: 0.3s;
            box-shadow: 0 0 15px rgba(0, 224, 198, 0.4);
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .modal-btn-confirm:hover {
            background: #00fae1;
            box-shadow: 0 0 25px rgba(0, 224, 198, 0.6);
            transform: scale(1.02);
        }
    </style>
</head>
<body>

<?php if (!$isLoggedIn): ?>

    <!-- ═══════════ LOGIN / SETUP ═══════════ -->
    <div class="login-wrap">
        <div class="login-card">
            <?php if ($noAdminsExist): ?>
                <div class="login-logo">WINSTAY<div class="logo-overlay"></div></div>
                <h2 style="color: #fff; margin-bottom: 5px; font-size: 20px;">Initial Admin Setup</h2>
                <p class="login-sub">Create the first administrator account</p>

                <?php if ($error): ?>
                    <div class="login-err"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="admin.php">
                    <input type="hidden" name="setup_admin" value="1">
                    <input type="text" name="nom" placeholder="Full Name" required autofocus>
                    <input type="email" name="email" placeholder="Admin Email" required>
                    <input type="password" name="password" placeholder="Create Password" required>
                    <button type="submit" class="login-btn">
                        <i class="fas fa-user-plus"></i> Create Admin Account
                    </button>
                    <p style="color: rgba(255,255,255,0.3); font-size: 11px; margin-top: 15px;">
                        This setup page will disappear once the first admin is created.
                    </p>
                </form>
            <?php else: ?>
                <div class="login-logo">WINSTAY<div class="logo-overlay"></div></div>
                <p class="login-sub">Admin access only</p>

                <?php if ($error): ?>
                    <div class="login-err"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="admin.php">
                    <input type="email" name="email" placeholder="Email address" required autofocus
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>
            <?php endif; ?>

            <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Back to WINSTAY</a>
        </div>
    </div>

<?php else: ?>

    <!-- ═══════════ DASHBOARD ═══════════ -->
    <div class="topbar">
        <div class="topbar-brand">
            <img src="images/meho.png" alt="" style="height:45px; width:auto; object-fit:contain;">
            <img src="images/ketba.png" alt="WinStay" style="height:40px; width:auto; object-fit:contain; position:relative; top:10px;">
            <span>Admin Dashboard</span>
        </div>
        <div class="topbar-right">
            <span class="topbar-user"><i class="fas fa-user-shield" style="color:#00a8b5; margin-right:6px;"></i><?= htmlspecialchars($adminName) ?></span>
            <a href="index.php" class="logout-btn"><i class="fas fa-home"></i> Site</a>
            <a href="admin.php?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="page-body">
        <h1 class="page-title">Management Console</h1>
        <p class="page-sub">Review and manage hosting applications from users.</p>

        <!-- Email Status Feedback -->
        <?php if (isset($_SESSION['mail_msg'])): ?>
            <div style="background: rgba(0, 224, 198, 0.1); border: 1px solid var(--pink-accent); color: var(--pink-accent); padding: 15px 20px; border-radius: 12px; margin-bottom: 30px; display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-info-circle"></i>
                <span><?= htmlspecialchars($_SESSION['mail_msg']) ?></span>
                <?php unset($_SESSION['mail_msg']); ?>
            </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="admin-tabs">
            <a href="admin.php?filter=en_attente" class="tab-link <?= $filterStatus === 'en_attente' ? 'active' : '' ?>">
                <i class="fas fa-clock"></i> Pending
                <span class="t-count"><?= $counts['en_attente'] ?? 0 ?></span>
            </a>
            <a href="admin.php?filter=approuvee" class="tab-link <?= $filterStatus === 'approuvee' ? 'active' : '' ?>">
                <i class="fas fa-check-circle"></i> Approved
                <span class="t-count"><?= $counts['approuvee'] ?? 0 ?></span>
            </a>
            <a href="admin.php?filter=rejetee" class="tab-link <?= $filterStatus === 'rejetee' ? 'active' : '' ?>">
                <i class="fas fa-times-circle"></i> Rejected
                <span class="t-count"><?= $counts['rejetee'] ?? 0 ?></span>
            </a>
        </div>

        <?php if (empty($requests)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p style="font-size:18px; font-weight:600; margin-bottom:10px;">No applications found</p>
                <p>There are no host applications in this category at the moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($requests as $req): ?>
                <div class="req-card">
                    <div>
                        <div class="req-name"><?= htmlspecialchars($req['nom_complet']) ?></div>
                        <div class="req-detail"><i class="fas fa-envelope"></i><?= htmlspecialchars($req['email']) ?></div>
                        <div class="req-detail"><i class="fas fa-phone"></i><?= htmlspecialchars($req['telephone']) ?></div>
                        <div class="req-detail"><i class="fas fa-id-card"></i> <a href="<?= htmlspecialchars($req['carte_national']) ?>" target="_blank" style="color: var(--pink-accent); text-decoration: underline;">View National ID Card</a></div>
                        <div class="req-date">
                            <i class="fas fa-calendar-alt"></i> 
                            <?= $req['statut'] === 'en_attente' ? 'Applied' : 'Processed' ?>: 
                            <?= date('d M Y, H:i', strtotime($req['date_demande'])) ?>
                        </div>
                        
                        <?php if ($req['statut'] === 'rejetee' && !empty($req['motif_rejet'])): ?>
                            <div style="margin-top: 15px; padding: 12px 18px; background: rgba(0, 224, 198, 0.05); border-left: 3px solid var(--pink-accent); border-radius: 12px; font-size: 13px; color: rgba(255,255,255,0.85); line-height: 1.6;">
                                <strong style="color: var(--pink-accent); font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 6px;"><i class="fas fa-comment-dots" style="margin-right: 6px;"></i>Rejection Reason</strong>
                                <?= htmlspecialchars($req['motif_rejet']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="actions">
                        <form method="POST" action="admin.php">
                            <input type="hidden" name="demande_id" value="<?= $req['id'] ?>">
                            <input type="hidden" name="current_filter" value="<?= $filterStatus ?>">
                            
                            <?php if ($req['statut'] === 'en_attente'): ?>
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn-yes" style="margin-bottom:12px;"><i class="fas fa-check"></i> Approve</button>
                            <?php endif; ?>
                        </form>
                        
                        <?php if ($req['statut'] === 'en_attente'): ?>
                            <button type="button" class="btn-no" onclick="openRejectModal(<?= $req['id'] ?>)"><i class="fas fa-times"></i> Reject</button>
                        <?php elseif ($req['statut'] === 'approuvee' || $req['statut'] === 'rejetee'): ?>
                            <form method="POST" action="admin.php">
                                <input type="hidden" name="demande_id" value="<?= $req['id'] ?>">
                                <input type="hidden" name="current_filter" value="<?= $filterStatus ?>">
                                <input type="hidden" name="action" value="reset">
                                <button type="submit" class="btn-reset"><i class="fas fa-undo"></i> Reset to Pending</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php endif; ?>

<!-- ── REJECTION MODAL ── -->
<div id="rejectModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h2><i class="fas fa-times-circle" style="color: var(--pink-accent); margin-right: 12px;"></i>Reject Application</h2>
            <button type="button" class="modal-close-btn" onclick="closeRejectModal()">&times;</button>
        </div>
        <form method="POST" action="admin.php">
            <input type="hidden" id="modal_demande_id" name="demande_id" value="">
            <input type="hidden" name="current_filter" value="<?= htmlspecialchars($filterStatus) ?>">
            <input type="hidden" name="action" value="reject">
            
            <div class="modal-body">
                <p class="modal-instruction">Specify the reason for rejecting this host application. This message will be sent to their email:</p>
                <textarea name="motif_rejet" id="modal_motif_rejet" required placeholder="Type the reason for rejection here..."></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="modal-btn-cancel" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="modal-btn-confirm">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

<script>
const defaultRejectionReason = "We have declined your application because your profile information is incomplete. If you still wish to list your property, please contact us so we can let you know which details are missing.";

function openRejectModal(demandeId) {
    document.getElementById('modal_demande_id').value = demandeId;
    document.getElementById('modal_motif_rejet').value = defaultRejectionReason;
    
    const modal = document.getElementById('rejectModal');
    modal.classList.add('active');
    
    // Focus the textarea and place cursor at the end
    const textarea = document.getElementById('modal_motif_rejet');
    textarea.focus();
    textarea.setSelectionRange(textarea.value.length, textarea.value.length);
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('active');
}

// Close modal if clicked outside the modal card
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>

</body>
</html>
