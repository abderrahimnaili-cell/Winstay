-- ============================================
-- WINSTAY Database Installation Script
-- Property Rental Platform Database Schema
-- ============================================

-- Drop existing tables if they exist (in reverse order of dependencies)

DROP TABLE IF EXISTS Favoris;
DROP TABLE IF EXISTS AvisSurProprietes;
DROP TABLE IF EXISTS DemandesReservation;
DROP TABLE IF EXISTS Annonces;
DROP TABLE IF EXISTS Images;
DROP TABLE IF EXISTS Espaces;
DROP TABLE IF EXISTS Logements;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS DemandesHote;
DROP TABLE IF EXISTS Utilisateurs_Temp;
DROP TABLE IF EXISTS Utilisateurs;

-- ============================================
-- Table: Utilisateurs_Temp (Temporary OTP Verification)
-- ============================================
CREATE TABLE Utilisateurs_Temp (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Utilisateurs (المستخدمين)
-- ============================================
CREATE TABLE Utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    photo_profil VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    profession VARCHAR(100) DEFAULT NULL,
    hote_depuis VARCHAR(50) DEFAULT NULL,
    langues VARCHAR(255) DEFAULT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut_hote ENUM('aucun', 'en_attente', 'approuve', 'rejete') DEFAULT 'aucun',
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: DemandesHote (طلبات الاستضافة)
-- ============================================
CREATE TABLE DemandesHote (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    nom_complet VARCHAR(150) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    carte_national VARCHAR(255) NOT NULL,
    statut ENUM('en_attente', 'approuvee', 'rejetee') DEFAULT 'en_attente',
    motif_rejet TEXT DEFAULT NULL,
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Admin (المسؤول)
-- ============================================
CREATE TABLE Admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT UNIQUE NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (utilisateur_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Logements (المساكن)
-- ============================================
CREATE TABLE Logements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    proprietaire_id INT NOT NULL,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    wilaya VARCHAR(50) NOT NULL,
    adresse TEXT DEFAULT NULL,
    latitude DECIMAL(10, 8) DEFAULT NULL,
    longitude DECIMAL(11, 8) DEFAULT NULL,
    prix_par_nuit DECIMAL(10, 2) NOT NULL,
    nombre_invites INT NOT NULL,
    nombre_chambres INT NOT NULL,
    nombre_lits INT NOT NULL,
    nombre_salles_bain INT NOT NULL,
    categorie ENUM('House', 'Apartment', 'Guesthouse', 'Hotel', 'Cabin', 'Villa') NOT NULL,
    cancel_policy TEXT DEFAULT NULL,
    house_rules TEXT DEFAULT NULL,
    safety_rules TEXT DEFAULT NULL,
    statut ENUM('disponible', 'occupe', 'en_maintenance', 'inactif') DEFAULT 'disponible',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proprietaire_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_proprietaire (proprietaire_id),
    INDEX idx_wilaya (wilaya),
    INDEX idx_categorie (categorie),
    INDEX idx_statut (statut),
    INDEX idx_prix (prix_par_nuit),
    INDEX idx_date_creation (date_creation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Espaces (المساحات)
-- ============================================
CREATE TABLE Espaces (
    id INT PRIMARY KEY AUTO_INCREMENT,
    logement_id INT NOT NULL,
    type_espace VARCHAR(50) NOT NULL,
    FOREIGN KEY (logement_id) REFERENCES Logements(id) ON DELETE CASCADE,
    INDEX idx_logement (logement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Images (الصور)
-- ============================================
CREATE TABLE Images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    logement_id INT DEFAULT NULL,
    espace_id INT DEFAULT NULL,
    url_image VARCHAR(255) NOT NULL,
    FOREIGN KEY (logement_id) REFERENCES Logements(id) ON DELETE CASCADE,
    FOREIGN KEY (espace_id) REFERENCES Espaces(id) ON DELETE CASCADE,
    INDEX idx_logement (logement_id),
    INDEX idx_espace (espace_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: Annonces (الإعلانات)
-- ============================================
CREATE TABLE Annonces (
    id INT PRIMARY KEY AUTO_INCREMENT,
    logement_id INT NOT NULL,
    type_usage VARCHAR(50) DEFAULT NULL,
    FOREIGN KEY (logement_id) REFERENCES Logements(id) ON DELETE CASCADE,
    INDEX idx_logement (logement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Table: DemandesReservation (طلبات الحجز)
-- ============================================
CREATE TABLE DemandesReservation (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    annonce_id INT NOT NULL,
    date_arrivee DATE NOT NULL,
    date_depart DATE NOT NULL,
    nombre_invites INT NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'annulee', 'completee') DEFAULT 'en_attente',
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (annonce_id) REFERENCES Annonces(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_annonce (annonce_id),
    INDEX idx_statut (statut),
    INDEX idx_dates (date_arrivee, date_depart)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- Table: AvisSurProprietes (آراء حول الممتلكات)
-- ============================================
CREATE TABLE AvisSurProprietes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    demande_reservation_id INT NOT NULL,
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    date_avis TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (demande_reservation_id) REFERENCES DemandesReservation(id) ON DELETE CASCADE,
    INDEX idx_demande (demande_reservation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- ============================================
-- Table: Favoris (المفضلة)
-- ============================================
CREATE TABLE Favoris (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL, 
    logement_id INT NOT NULL,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favori (utilisateur_id, logement_id),
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (logement_id) REFERENCES Logements(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_logement (logement_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- End of Installation Script
-- ============================================
