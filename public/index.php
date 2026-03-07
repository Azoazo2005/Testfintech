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
    <title>FinTech Demo - Connexion</title> 
    <link 
href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
rel="stylesheet"> 
    <link rel="stylesheet" href="assets/css/style.css"> 
</head> 
<body class="bg-gradient-primary"> 
    <div class="container"> 
        <div class="row justify-content-center"> 
            <div class="col-xl-6 col-lg-8 col-md-9"> 
                <div class="card o-hidden border-0 shadow-lg my-5"> 
                    <div class="card-body p-5"> 
                        <div class="text-center"> 
                            <h1 class="h4 text-gray-900 mb-4">FinTech Demo</h1> 
                            <p class="text-muted">Plateforme de démonstration de sécurité</p> 
                        </div> 
                        <div id="message" class="alert" style="display: none;"></div> 
                        <form id="loginForm" class="user"> 
                            <div class="form-group mb-3"> 
                                <input type="text" class="form-control form-control-user" id="username" name="username" placeholder="Nom d'utilisateur" required> 
                            </div> 
                            <div class="form-group mb-3"> 
                                <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Mot de passe" required> 
                            </div> 
                            <button type="submit" class="btn btn-primary btn-user btn-block">Se connecter</button> 
                        </form> 
                        <div class="text-center mt-4"> 
                            <a href="register.php">Créer un compte</a> 
                        </div> 
                        <div class="card mt-4"> 
                            <div class="card-header"> 
                                <strong>Comptes de test</strong> 
                            </div> 
                            <div class="card-body"> 
                                <p><code>alice / password123</code> (5000€)</p> 
                                <p><code>victim / victim123</code> (15000€)</p> 
                                <p><code>admin / admin123</code> (Admin)</p> 
                                <hr> 
                                <p><strong>Test d'injection SQL :</strong></p> 
                                <p><code>admin' OR '1'='1' --</code></p> 
                            </div> 
                        </div> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
    <script src="assets/js/app.js"></script> 
</body> 
</html>
