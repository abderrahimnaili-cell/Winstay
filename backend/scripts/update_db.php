<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getConnection();
    
    // Create Temporary Users table for OTP verification
    $sql = "CREATE TABLE IF NOT EXISTS Utilisateurs_Temp (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        mot_de_passe VARCHAR(255) NOT NULL,
        otp_code VARCHAR(6) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Database updated successfully: Utilisateurs_Temp table created.\n";
    
} catch (PDOException $e) {
    die("Database Update Error: " . $e->getMessage());
}
