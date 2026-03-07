-- Données de démonstration pour FinTech Demo (V1)
-- A exécuter après schema.sql

USE fintech_demo;

-- Utilisateurs de test
INSERT INTO users (username, email, password, full_name, is_active) VALUES
('admin', 'admin@fintech.local', 'admin123', 'Administrateur Demo', 1),
('alice', 'alice@fintech.local', 'alice123', 'Alice Martin', 1),
('bob', 'bob@fintech.local', 'bob123', 'Bob Diallo', 1),
('charlie', 'charlie@fintech.local', 'charlie123', 'Charlie Traore', 1)
ON DUPLICATE KEY UPDATE
  email = VALUES(email),
  full_name = VALUES(full_name),
  is_active = VALUES(is_active);

-- Portefeuilles initiaux (EUR)
INSERT INTO wallets (user_id, balance, currency) VALUES
((SELECT id FROM users WHERE username = 'admin'), 5000.00, 'EUR'),
((SELECT id FROM users WHERE username = 'alice'), 1250.50, 'EUR'),
((SELECT id FROM users WHERE username = 'bob'), 980.00, 'EUR'),
((SELECT id FROM users WHERE username = 'charlie'), 300.00, 'EUR')
ON DUPLICATE KEY UPDATE
  balance = VALUES(balance);

-- Transactions de démo
INSERT INTO transactions (sender_user_id, receiver_user_id, amount, reference, status, created_at) VALUES
(
  (SELECT id FROM users WHERE username = 'alice'),
  (SELECT id FROM users WHERE username = 'bob'),
  120.00,
  'Paiement facture #INV-2026-001',
  'completed',
  DATE_SUB(NOW(), INTERVAL 3 DAY)
),
(
  (SELECT id FROM users WHERE username = 'bob'),
  (SELECT id FROM users WHERE username = 'charlie'),
  50.00,
  'Remboursement repas',
  'completed',
  DATE_SUB(NOW(), INTERVAL 2 DAY)
),
(
  (SELECT id FROM users WHERE username = 'admin'),
  (SELECT id FROM users WHERE username = 'alice'),
  300.00,
  'Avance exceptionnelle',
  'completed',
  DATE_SUB(NOW(), INTERVAL 1 DAY)
),
(
  (SELECT id FROM users WHERE username = 'charlie'),
  (SELECT id FROM users WHERE username = 'alice'),
  25.00,
  'Test transfert en attente',
  'pending',
  NOW()
);
