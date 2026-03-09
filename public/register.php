<?php
session_start();
require_once '../core/Auth.php';

$auth = new Auth();
if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - FinTech Demo</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-card {
            width: 100%;
            max-width: 440px;
        }

        .register-card .card-inner {
            padding: 2.5rem 2rem;
        }

        .register-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 4px;
        }

        .register-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 1.8rem;
        }

        .reg-group {
            margin-bottom: 1.1rem;
        }

        .reg-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .reg-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e8e8f0;
            border-radius: 8px;
            font-family: var(--font);
            font-size: 0.9rem;
            color: var(--text-primary);
            background: #fff;
            outline: none;
            transition: var(--transition);
        }

        .reg-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(63, 81, 181, 0.1);
        }

        .reg-input::placeholder {
            color: var(--text-muted);
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 16px rgba(63, 81, 181, 0.3);
            margin-top: 0.5rem;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(63, 81, 181, 0.4);
        }

        .register-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .register-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }

        .alert-msg {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1rem;
            display: none;
        }

        .alert-msg.show-success {
            display: block;
            background: var(--green-light);
            color: #2e7d32;
            border: 1px solid rgba(76, 175, 80, 0.2);
        }

        .alert-msg.show-error {
            display: block;
            background: #ffebee;
            color: #c62828;
            border: 1px solid rgba(239, 83, 80, 0.2);
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <div class="card-modern register-card">
            <div class="card-inner">
                <h1 class="register-title">Créer un compte</h1>
                <p class="register-subtitle">Rejoignez la plateforme FinTech Demo</p>

                <div id="message" class="alert-msg"></div>

                <form id="registerForm">
                    <div class="reg-group">
                        <label class="reg-label">Nom complet</label>
                        <input type="text" class="reg-input" id="full_name" name="full_name" placeholder="Jean Dupont" required>
                    </div>
                    <div class="reg-group">
                        <label class="reg-label">Nom d'utilisateur</label>
                        <input type="text" class="reg-input" id="username" name="username" placeholder="jean.dupont" required>
                    </div>
                    <div class="reg-group">
                        <label class="reg-label">Email</label>
                        <input type="email" class="reg-input" id="email" name="email" placeholder="jean@example.com" required>
                    </div>
                    <div class="reg-group">
                        <label class="reg-label">Mot de passe</label>
                        <input type="password" class="reg-input" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn-register">S'inscrire</button>
                </form>

                <div class="register-footer">
                    Déjà un compte ? <a href="index.php">Se connecter</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const msg = document.getElementById('message');

            try {
                const res = await fetch('../api/auth/register.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    msg.className = 'alert-msg show-success';
                    msg.textContent = '✓ Compte créé ! Redirection...';
                    setTimeout(() => window.location.href = 'index.php', 1500);
                } else {
                    msg.className = 'alert-msg show-error';
                    msg.textContent = '✕ ' + data.message;
                }
            } catch (err) {
                msg.className = 'alert-msg show-error';
                msg.textContent = '✕ Erreur de connexion au serveur';
            }
        });
    </script>
</body>
</html>
