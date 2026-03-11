<?php
require_once __DIR__ . '/../core/Auth.php';
session_start();

$auth = new Auth();
if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = $_GET['success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
    $auth = new Auth();

    if ($action === 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $result = $auth->login($username, $password);
        
        if ($result['success']) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    } elseif ($action === 'register') {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $fullName = $_POST['full_name'] ?? '';
        
        $result = $auth->register($username, $email, $password, $fullName);
        
        if ($result['success']) {
            header('Location: index.php?success=Compte créé avec succès ! Connectez-vous.');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Fintech Robuste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-pro">
<div class="bg-mesh"></div>

<nav class="navbar navbar-pro sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="pro-brand" href="index.php"><i class="bi bi-shield-check-fill me-2"></i>FINTECH<span class="fw-normal text-pro-muted"> ROBUSTE</span></a>
        <div>
            <a href="lab.php" class="btn btn-primary btn-sm px-4 shadow-sm border-0" style="background: #2ecc71; font-weight: 700;">
                <i class="bi bi-mortarboard-fill me-1"></i> SECURITY LAB
            </a>
        </div>
    </div>
</nav>

<div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center py-5">
    <!-- Top Tier: Hero Title -->
    <div class="row w-100 justify-content-center mb-5 animate-pro-fadein">
        <div class="col-lg-10 text-center mt-5">
            <h1 class="hero-title-big">FINTECH<span style="color: #2ecc71;"> ROBUSTE</span></h1>
            <div class="hero-v-line"></div>
            <p class="hero-subtitle-big">VOTRE PLATEFORME FINANCIÈRE SÉCURISÉE</p>
        </div>
    </div>

    <!-- Bottom Tier: Cards -->
    <div class="row w-100 justify-content-center align-items-stretch g-5 py-5">
        <div class="col-lg-1 animate-d-none animate-lg-block"></div>
        
        <!-- Auth Column -->
        <div class="col-lg-5 animate-pro-fadein" style="animation-delay: 0.1s;">
            <div class="pro-card pt-5 px-5 pb-3 shadow-sm" id="auth-card">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <h4 class="mb-0 fw-bold" id="auth-title">Connexion</h4>
                    <button class="btn btn-link text-pro-primary p-0 text-decoration-none small fw-bold" id="toggle-auth" onclick="toggleAuthMode()">S'inscrire <i class="bi bi-arrow-right small"></i></button>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger border-0 small mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success border-0 small mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form id="login-form" method="POST" action="index.php">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group-pro">
                        <label class="form-label-pro">Identifiant Utilisateur</label>
                        <input type="text" name="username" id="login_username" class="form-control-pro" placeholder="Ex: admin" required>
                        <i class="bi bi-person input-icon"></i>
                    </div>

                    <div class="form-group-pro">
                        <label class="form-label-pro">Code d'Accès</label>
                        <input type="password" name="password" class="form-control-pro" placeholder="••••••••" required>
                        <i class="bi bi-shield-lock input-icon"></i>
                    </div>

                    <button type="submit" class="btn-pro w-100 py-3 mb-2">
                        <i class="bi bi-shield-check me-2"></i>S'AUTHENTIFIER
                    </button>
                </form>

                <!-- Register Form (Hidden by default) -->
                <form id="register-form" method="POST" action="index.php" style="display: none;">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="form-group-pro">
                        <label class="form-label-pro">Nom Complet</label>
                        <input type="text" name="full_name" class="form-control-pro" placeholder="Votre Nom" required>
                        <i class="bi bi-person-badge input-icon"></i>
                    </div>

                    <div class="form-group-pro">
                        <label class="form-label-pro">Email Professionnel</label>
                        <input type="email" name="email" class="form-control-pro" placeholder="nom@exemple.com" required>
                        <i class="bi bi-envelope input-icon"></i>
                    </div>

                    <div class="form-group-pro">
                        <label class="form-label-pro">Identifiant</label>
                        <input type="text" name="username" class="form-control-pro" placeholder="Pseudo" required>
                        <i class="bi bi-at input-icon"></i>
                    </div>

                    <div class="form-group-pro">
                        <label class="form-label-pro">Code d'Accès</label>
                        <input type="password" name="password" class="form-control-pro" placeholder="Minimum 8 caractères" required>
                        <i class="bi bi-key input-icon"></i>
                    </div>

                    <button type="submit" class="btn-pro w-100 py-3 mb-2">
                        <i class="bi bi-person-plus me-2"></i>CRÉER LE COMPTE
                    </button>
                </form>

                <div class="text-center">
                    <a href="#" class="text-pro-muted small text-decoration-none opacity-50">Besoin d'aide pour accéder ?</a>
                </div>
            </div>
            
            <!-- LAB INTEGRATION: LOGIN SQLi -->
            <div class="mt-4 pro-card no-parallax p-4 border-2 border-primary border-dashed bg-pro-soft animate-pro-fadein" style="animation-delay: 0.3s;">
                <h6 class="fw-bold text-pro-primary mb-3"><i class="bi bi-mortarboard-fill me-2"></i>LAB: TENTATIVE D'INJECTION SQL</h6>
                <p class="x-small text-pro-muted mb-3">Tentez de contourner l'authentification avec un payload classique.</p>
                <div class="d-flex gap-2 mb-3">
                    <code class="p-2 border rounded bg-white x-small flex-grow-1" id="sqli_payload">admin' OR '1'='1' --</code>
                    <button class="btn btn-pro btn-sm" onclick="applyPayload('login_username', 'sqli_payload')">TESTER</button>
                </div>
                <div class="text-center mt-4">
                    <p class="x-small text-pro-muted mb-2">Besoin d'aller plus loin ?</p>
                    <a href="lab.php" class="btn btn-outline-success w-100 py-3 fw-bold" style="border-width: 2px;">
                        <i class="bi bi-shield-shaded me-1"></i> ACCÉDER AU SECURITY LAB
                    </a>
                </div>
            </div>
        </div>

        <!-- Security Info Column -->
        <div class="col-lg-5 animate-pro-fadein" style="animation-delay: 0.2s;">
            <div class="pro-card p-4 border-pro bg-pro-soft h-100 shadow-sm border-2">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-white rounded-circle p-2 me-3 border-pro shadow-sm">
                        <i class="bi bi-shield-check-fill text-success fs-4"></i>
                    </div>
                    <div>
                        <span class="fw-bold tracking-widest text-uppercase d-block mb-0" style="font-size: 0.8rem;">Architecture Sécurisée</span>
                        <span class="badge bg-success text-white x-small">ROBUSTE MODE ACTIVE</span>
                    </div>
                </div>
                
                <p class="text-pro-muted mb-4 small">Cette plateforme utilise les standards de sécurité les plus élevés pour protéger vos fonds et vos données.</p>
                
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="p-3 bg-white rounded-3 border-pro border-start border-4 border-success shadow-sm">
                            <h6 class="x-small fw-bold mb-2 text-success text-uppercase"><i class="bi bi-shield-lock-fill me-1"></i>Hachage Bcrypt</h6>
                            <p class="x-small text-pro-muted mb-0 leading-relaxed">Vos identifiants ne sont jamais stockés en clair. Nous utilisons Bcrypt pour garantir une protection maximale.</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-white rounded-3 border-pro border-start border-4 border-primary shadow-sm">
                            <h6 class="x-small fw-bold mb-2 text-primary text-uppercase"><i class="bi bi-code-square me-1"></i>Requêtes Préparées</h6>
                            <p class="x-small text-pro-muted mb-0 leading-relaxed">Toutes les interactions avec la base de données sont protégées contre les injections SQL via des Prepared Statements.</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-white rounded-3 border-pro border-start border-4 border-info shadow-sm">
                            <h6 class="x-small fw-bold mb-2 text-info text-uppercase"><i class="bi bi-file-earmark-check me-1"></i>Audit & Logs</h6>
                            <p class="x-small text-pro-muted mb-0 leading-relaxed">Chaque opération sensible est tracée dans nos journaux d'audit pour une surveillance en temps réel.</p>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center opacity-75">
                    <p class="mb-0 x-small fw-bold text-pro-muted"># FINTECH ROBUSTE SYSTEM</p>
                    <p class="mb-0 x-small fw-bold text-pro-muted">SÉCURITÉ GARANTIE</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 pt-4">
        <p class="text-pro-muted x-small">&copy; <?php echo date('Y'); ?> FINTECH ROBUSTE SOLUTIONS. TOUS DROITS RÉSERVÉS.</p>
    </footer>
</div>

<script>
function applyPayload(inputId, payloadId) {
    const payload = document.getElementById(payloadId).innerText;
    document.getElementById(inputId).value = payload;
    
    // Highlight effect
    const input = document.getElementById(inputId);
    input.classList.add('border-primary');
    setTimeout(() => input.classList.remove('border-primary'), 1000);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/pro.js"></script>
</body>
</html>