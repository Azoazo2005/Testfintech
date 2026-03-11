-- ==========================================================
-- FINTECH_ROBUSTE - BASE DE DONNÉES COMPLÈTE
-- Version consolidée pour déploiement facile
-- ==========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. CRÉATION DE LA BASE
CREATE DATABASE IF NOT EXISTS `fintech-robuste` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `fintech-robuste`;

-- 2. STRUCTURE DES TABLES

-- Table: users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `is_admin` tinyint(1) DEFAULT 0,
  `status` varchar(20) DEFAULT 'active',
  `balance` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: accounts
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_number` (`account_number`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: wallets
DROP TABLE IF EXISTS `wallets`;
CREATE TABLE `wallets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: transactions_v2
DROP TABLE IF EXISTS `transactions_v2`;
CREATE TABLE `transactions_v2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_user_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'completed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `from_user_id` (`from_user_id`),
  KEY `to_user_id` (`to_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for Admin Activity Logs
DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for Audit Logs (System wide)
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `event_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. INSERTION DES DONNÉES

-- Insertion des Utilisateurs
INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `is_admin`, `balance`) VALUES
(1, 'admin', 'admin@fintech.com', 'admin123', 'Administrateur Système', 'admin', 1, 97500.00),
(2, 'alice', 'alice@example.com', 'password', 'Alice Martin', 'user', 0, 7300.00),
(3, 'bob', 'bob@example.com', 'password', 'Bob Dupont', 'user', 0, 2000.00),
(4, 'victim', 'victim@example.com', 'password', 'Victime Riche', 'user', 0, 15200.00);

-- Insertion des Comptes
INSERT INTO `accounts` (`id`, `user_id`, `account_number`, `balance`) VALUES
(1, 1, 'FR76000001', 97500.00),
(2, 2, 'FR76000002', 7300.00),
(3, 3, 'FR76000003', 2000.00),
(4, 4, 'FR76000004', 15200.00);

-- Insertion des Wallets
INSERT INTO `wallets` (`id`, `user_id`, `balance`) VALUES
(1, 1, 97500.00),
(2, 2, 7300.00),
(3, 3, 2000.00),
(4, 4, 15200.00);

-- Historique Initial des Transactions
INSERT INTO `transactions_v2` (`id`, `from_user_id`, `to_user_id`, `amount`, `description`, `created_at`) VALUES
(1, 2, 3, 100.00, 'Remboursement restaurant', '2026-03-02 20:23:13'),
(2, 4, 2, 500.00, 'Cadeau anniversaire', '2026-03-02 20:23:13'),
(3, 2, 4, 200.00, 'nr', '2026-03-02 20:28:20'),
(4, 1, 2, 500.00, 'Frais de maintenance réseau', '2026-03-02 21:15:15');

-- 4. CONTRAINTES
ALTER TABLE `accounts` ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `wallets` ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `transactions_v2` ADD CONSTRAINT `transactions_v2_ibfk_1` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`);
ALTER TABLE `transactions_v2` ADD CONSTRAINT `transactions_v2_ibfk_2` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`);
ALTER TABLE `admin_logs` ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

COMMIT;
