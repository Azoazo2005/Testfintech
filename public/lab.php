<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../config/constants.php';
session_start();

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$title = "Student Lab Guide";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - FinTech_Vulnerable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .guide-section {
            background: white;
            border-radius: var(--pro-radius);
            border: 1px solid var(--pro-border);
            padding: 30px;
            margin-bottom: 30px;
        }
        .payload-box {
            background: var(--pro-primary-soft);
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', Courier, monospace;
            border-left: 4px solid var(--pro-primary);
            margin: 15px 0;
            color: var(--pro-primary);
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-pro-arctic d-flex flex-column min-vh-100">

<nav class="navbar-pro">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="dashboard.php" class="pro-brand">
            <i class="bi bi-snow2 me-2"></i>FINTECH
        </a>
        <div class="d-flex align-items-center gap-4">
            <a href="dashboard.php" class="text-pro-muted text-decoration-none small fw-bold tracking-widest">DASHBOARD</a>
            <a href="transfer.php" class="text-pro-muted text-decoration-none small fw-bold tracking-widest">TRANSFERT</a>
            <span class="badge bg-pro-primary-soft text-pro-primary px-3 py-2">
                <i class="bi bi-mortarboard-fill me-1"></i> LAB STUDENT
            </span>
            <a href="../api/auth/logout.php" class="text-pro-primary text-decoration-none small fw-bold tracking-widest">DÉCONNEXION</a>
        </div>
    </div>
</nav>

<div class="container py-5 animate-pro-fadein">
    <header class="mb-5 text-center">
        <h6 class="text-pro-primary text-uppercase tracking-widest fw-bold mb-3">Centre de Formation Sécurité</h6>
        <h1 class="display-5 fw-bold">Lab Guide & Exploitation Plan</h1>
        <p class="text-pro-muted mx-auto" style="max-width: 600px;">Bienvenue dans l'environnement de test. Utilisez ces guides pour comprendre et exploiter les failles de l'application Arctic Pro.</p>
    </header>

    <div class="row">
        <div class="col-lg-4">
            <div class="list-group list-group-flush pro-card p-0 overflow-hidden sticky-top" style="top: 100px;">
                <a href="#sqli" class="list-group-item list-group-item-action p-4 border-pro">1. SQL Injection Bypass</a>
                <a href="#idor" class="list-group-item list-group-item-action p-4 border-pro">2. IDOR (Vol de Fonds)</a>
                <a href="#xss" class="list-group-item list-group-item-action p-4 border-pro">3. Persistent XSS</a>
                <a href="#race" class="list-group-item list-group-item-action p-4 border-pro">4. Race Condition</a>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div id="sqli" class="guide-section animate-pro-fadein">
                <h3 class="fw-bold mb-4">1. Authentification Bypass</h3>
                <p class="text-pro-muted">L'input du nom d'utilisateur n'est pas filtré avant d'être concaténé dans la requête SQL.</p>
                <div class="payload-box">' OR '1'='1' <span class="text-danger">#</span></div>
                <h6 class="fw-bold mt-4">Étapes :</h6>
                <ul class="text-pro-muted small">
                    <li>Allez sur la page de connexion.</li>
                    <li>Utilisez le payload ci-dessus (inclure le <strong>#</strong> pour commenter le mot de passe).</li>
                    <li>Le système ignorera la vérification du secret et vous connectera directement.</li>
                </ul>
            </div>

            <div id="idor" class="guide-section">
                <h3 class="fw-bold mb-4">2. IDOR (Manipulation d'ID)</h3>
                <p class="text-pro-muted">Le formulaire de transfert utilise un identifiant d'expéditeur caché dans le DOM.</p>
                <div class="payload-box"><input type="hidden" name="from_user_id" value="1"></div>
                <h6 class="fw-bold mt-4">Étapes :</h6>
                <ul class="text-pro-muted small">
                    <li>Allez sur la page Transfert.</li>
                    <li>Faites un clic droit sur la page -> "Inspecter".</li>
                    <li>Cherchez l'élément <code>from_user_id</code>.</li>
                    <li>Changez la valeur de <code>1</code> à <code>4</code>.</li>
                    <li>Validez le transfert : l'argent sera débité du compte #4.</li>
                </ul>
            </div>

            <div id="xss" class="guide-section">
                <h3 class="fw-bold mb-4">3. Cross-Site Scripting (XSS)</h3>
                <p class="text-pro-muted">Le champ de description des transactions est affiché sans encodage HTML.</p>
                <div class="payload-box">&lt;script&gt;alert('Session Stolen')&lt;/script&gt;</div>
                <h6 class="fw-bold mt-4">Scénario :</h6>
                <p class="text-pro-muted small">Une fois injecté, tout utilisateur (y compris l'administrateur) visualisant l'historique exécutera ce code dans son navigateur.</p>
            </div>

            <div id="race" class="guide-section">
                <h3 class="fw-bold mb-4">4. Race Condition</h3>
                <p class="text-pro-muted">Le solde n'est pas verrouillé pendant la transaction, permettant des débits multiples simultanés.</p>
                <p class="text-pro-muted small">Utilisez le bouton dédié sur la page de transfert pour automatiser l'envoi de 5 requêtes rapides.</p>
            </div>
        </div>
    </div>
</div>

<footer class="footer mt-auto py-5 border-top border-pro">
    <div class="container text-center">
        <p class="text-pro-muted x-small">&copy; 2026 FINTECH SOLUTIONS. EDUCATION LAB.</p>
    </div>
</footer>

<script src="assets/js/pro.js"></script>
</body>
</html>
