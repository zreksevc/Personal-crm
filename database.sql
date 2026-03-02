-- =============================================
-- Personal CRM — Database Schema
-- Jalankan di phpMyAdmin atau MySQL CLI
-- =============================================

CREATE DATABASE IF NOT EXISTS personal_crm
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE personal_crm;

-- =============================================
-- Tabel: users
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Tabel: contacts
-- =============================================
CREATE TABLE IF NOT EXISTS contacts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT           NOT NULL,
    nama       VARCHAR(100)  NOT NULL,
    no_hp      VARCHAR(20),
    email      VARCHAR(150),
    kategori   ENUM('Client','Prospect','Partner','Vendor','Lainnya') DEFAULT 'Lainnya',
    alamat     TEXT,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Index untuk mempercepat search & filter
CREATE INDEX idx_contacts_user  ON contacts (user_id);
CREATE INDEX idx_contacts_nama  ON contacts (nama);
CREATE INDEX idx_contacts_kat   ON contacts (kategori);
