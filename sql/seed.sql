USE fintech_demo;

-- Utilisateurs de test (mots de passe en MD5 - VULNÉRABLE)
INSERT INTO users (username, email, password, full_name, is_admin) VALUES
('admin', 'admin@fintech.com', MD5('admin123'), 'Administrateur', TRUE),
('alice', 'alice@example.com', MD5('password123'), 'Alice Martin', FALSE),
('bob', 'bob@example.com', MD5('password123'), 'Bob Dupont', FALSE),
('victim', 'victim@example.com', MD5('victim123'), 'Victime Riche', FALSE);

-- Portefeuilles avec soldes différents
INSERT INTO wallets (user_id, balance) VALUES
(1, 100000.00),  -- Admin avec beaucoup d'argent
(2, 5000.00),    -- Alice
(3, 2000.00),    -- Bob
(4, 15000.00);   -- Victime avec gros solde

-- Quelques transactions historiques
INSERT INTO transactions (from_user_id, to_user_id, amount, description, status) VALUES
(2, 3, 100.00, 'Remboursement restaurant', 'completed'),
(4, 2, 500.00, 'Cadeau anniversaire', 'completed');
