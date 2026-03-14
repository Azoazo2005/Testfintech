<?php 
session_start(); 
require_once __DIR__ . '/../core/Auth.php'; 
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
    <title>Inscription - FinTech_Vulnerable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="col-md-6 col-lg-5 animate-pro-fadein">
        <div class="text-center mb-5">
            <h1 class="pro-brand justify-content-center mb-2">
                <i class="bi bi-snow2 me-2"></i>FINTECH
            </h1>
            <p class="text-pro-muted small text-uppercase tracking-widest">Rejoignez la révolution FinTech</p>
        </div>

        <div class="pro-card p-5">
            <h4 class="mb-4 text-center">Créer un compte</h4>
            
            <div id="message" class="alert small mb-4" style="display: none;"></div>

            <form id="registerForm">
                <div class="form-group-pro">
                    <label class="form-label-pro">Identifiant Utilisateur</label>
                    <input type="text" id="username" name="username" class="form-control-pro" placeholder="Nom d'utilisateur" required>
                </div>
                <div class="form-group-pro">
                    <label class="form-label-pro">Adresse Email</label>
                    <input type="email" id="email" name="email" class="form-control-pro" placeholder="votre@email.com" required>
                </div>
                <div class="form-group-pro">
                    <label class="form-label-pro">Nom Complet</label>
                    <input type="text" id="full_name" name="full_name" class="form-control-pro" placeholder="Prénom Nom" required>
                </div>
                <div class="form-group-pro mb-4">
                    <label class="form-label-pro">Mot de Passe</label>
                    <input type="password" id="password" name="password" class="form-control-pro" placeholder="Sécurisez votre accès" required>
                </div>
                <button type="submit" class="btn-pro w-100 py-3">
                    <i class="bi bi-person-plus-fill me-2"></i>CRÉER MON COMPTE
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="index.php" class="text-pro-muted text-decoration-none small">Déjà inscrit ? Connectez-vous ici</a>
            </div>
        </div>

        <footer class="text-center mt-5">
            <p class="text-pro-muted x-small">&copy; 2026 FINTECH SOLUTIONS. TOUS DROITS RÉSERVÉS.</p>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script src="assets/js/pro.js"></script>
</body>
</html>
