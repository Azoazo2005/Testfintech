-- Schéma SQL de base pour FinTech Demo (V1)
-- Compatible MySQL / MariaDB (XAMPP)

CREATE DATABASE IF NOT EXISTS fintech_demo
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE fintech_demo;

-- Utilisateurs
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Portefeuilles
CREATE TABLE IF NOT EXISTS wallets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  balance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  currency CHAR(3) NOT NULL DEFAULT 'EUR',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_wallet_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  UNIQUE KEY uq_wallet_user_currency (user_id, currency)
) ENGINE=InnoDB;

-- Transactions (historique de transferts)
CREATE TABLE IF NOT EXISTS transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sender_user_id INT UNSIGNED NOT NULL,
  receiver_user_id INT UNSIGNED NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  reference VARCHAR(150) DEFAULT NULL,
  status ENUM('pending', 'completed', 'failed', 'cancelled') NOT NULL DEFAULT 'completed',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_tx_sender
    FOREIGN KEY (sender_user_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_tx_receiver
    FOREIGN KEY (receiver_user_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  INDEX idx_tx_sender_date (sender_user_id, created_at),
  INDEX idx_tx_receiver_date (receiver_user_id, created_at)
) ENGINE=InnoDB;
