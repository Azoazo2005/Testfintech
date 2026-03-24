<?php
require_once __DIR__ . '/../core/Auth.php';
session_start();

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
        $phone = $_POST['phone'] ?? '';
        
        $result = $auth->register($username, $email, $password, $fullName, $phone);
        
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
    <title>Authentification - FinTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-pro">
<div class="bg-mesh"></div>

<nav class="navbar navbar-pro fixed-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="pro-brand" href="index.php"><i class="bi bi-snow2 me-2"></i>FINTECH</a>
    </div>
</nav>

<div class="container min-vh-100 d-flex flex-column align-items-center justify-content-center py-5">
    <!-- Top Tier: Hero Title -->
    <div class="row w-100 justify-content-center mb-5 animate-pro-fadein">
        <div class="col-lg-10 text-center mt-5">
            <h1 class="hero-title-big">FINTECH</h1>
            <div class="hero-v-line"></div>
            <p class="hero-subtitle-big">THE ELITE CYBERSECURITY LABORATORY</p>
        </div>
    </div>

    <!-- Bottom Tier: Cards -->
    <div class="row w-100 justify-content-center align-items-stretch g-5 py-5">
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
                        <input type="text" name="username" class="form-control-pro" placeholder="Ex: admin" required>
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
                        <label class="form-label-pro">Numéro de Téléphone (Sénégal)</label>
                        <input type="text" name="phone" class="form-control-pro" placeholder="+221770000000" required>
                        <i class="bi bi-telephone input-icon"></i>
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
        </div>

        <!-- Security Lab Column -->
        <div class="col-lg-5 animate-pro-fadein lab-only" style="animation-delay: 0.2s;">
            <div class="pro-card p-4 border-pro bg-pro-soft h-100 shadow-sm border-2">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-white rounded-circle p-2 me-3 border-pro shadow-sm">
                        <i class="bi bi-shield-slash-fill text-pro-primary fs-4"></i>
                    </div>
                    <div>
                        <span class="fw-bold tracking-widest text-uppercase d-block mb-0" style="font-size: 0.8rem;">Security Laboratory</span>
                        <span class="badge bg-pro-primary text-white x-small">EDUCATIONAL MODE ON</span>
                    </div>
                </div>
                
                <p class="text-pro-muted mb-4 small">Ce portail simule des failles réelles. Utilisez ces identifiants pour tester les différents vecteurs d'attaque.</p>
                
                <div class="table-responsive bg-white rounded-3 border-pro shadow-sm p-2 mb-4">
                    <table class="table table-pro table-sm table-borderless mb-0" style="font-size: 0.8rem;">
                        <thead>
                            <tr class="text-pro-muted opacity-75">
                                <th class="ps-3">NŒUD</th>
                                <th>IDENTIFIANT</th>
                                <th class="text-end pe-3">FAILLE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-pro-primary ps-3">@admin</td>
                                <td class="text-pro-muted font-monospace">admin / 123</td>
                                <td class="text-end pe-3"><span class="badge bg-light text-pro-muted border-pro x-small">SQL Injection</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-pro-primary ps-3">@alice</td>
                                <td class="text-pro-muted font-monospace">alice / password</td>
                                <td class="text-end pe-3"><span class="badge bg-light text-pro-muted border-pro x-small">Insecure MD5</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-pro-primary ps-3">@bob</td>
                                <td class="text-pro-muted font-monospace">bob / bob123</td>
                                <td class="text-end pe-3"><span class="badge bg-light text-pro-muted border-pro x-small">Cleartext</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="p-3 bg-white rounded-3 border-pro border-start border-4 border-pro-primary shadow-sm">
                            <h6 class="x-small fw-bold mb-2 text-pro-primary text-uppercase"><i class="bi bi-shield-slash-fill me-1"></i>01. SQL Injection (Bypass)</h6>
                            <p class="x-small text-pro-muted mb-2Leading-relaxed">Ignorez le mot de passe et connectez-vous comme n'importe quel utilisateur :</p>
                            <code class="d-block p-2 bg-light rounded x-small border-pro">admin' OR '1'='1' #</code>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-white rounded-3 border-pro border-start border-4 border-info shadow-sm">
                            <h6 class="x-small fw-bold mb-2 text-info text-uppercase"><i class="bi bi-database-fill-exclamation me-1"></i>02. SQL Injection (Union)</h6>
                            <p class="x-small text-pro-muted mb-2 leading-relaxed">Récupérez des données cachées en fusionnant deux requêtes (11 colonnes requises) :</p>
                            <code class="d-block p-2 bg-light rounded x-small border-pro">admin' UNION SELECT 1,2,3,4,5,6,7,8,9,10,11 #</code>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-white rounded-3 border-pro border-start border-4 border-warning shadow-sm">
                            <h6 class="x-small fw-bold mb-2 text-warning text-uppercase"><i class="bi bi-eye-fill me-1"></i>03. Fuite de Données (Cleartext)</h6>
                            <p class="x-small text-pro-muted mb-0 leading-relaxed">Observez que les mots de passe sont stockés sans chiffrement. Utilisez <strong>@alice</strong> ou <strong>@bob</strong> pour confirmer dans le dashboard.</p>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center opacity-75">
                    <p class="mb-0 x-small fw-bold text-pro-muted"># VULNERABILITY LAB ACTIVE</p>
                    <p class="mb-0 x-small fw-bold text-pro-muted">WEST AFRICA (CFA)</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 pt-4">
        <p class="text-pro-muted x-small">&copy; <?php echo date('Y'); ?> FINTECH SOLUTIONS. TOUS DROITS RÉSERVÉS.</p>
    </footer>
</div>

<script>
function toggleAuthMode() {
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const authTitle = document.getElementById('auth-title');
    const toggleBtn = document.getElementById('toggle-auth');
    
    if (loginForm.style.display === 'none') {
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
        authTitle.innerText = 'Connexion';
        toggleBtn.innerHTML = 'S\'inscrire <i class="bi bi-arrow-right small"></i>';
    } else {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        authTitle.innerText = 'Inscription';
        toggleBtn.innerHTML = '<i class="bi bi-arrow-left small"></i> Déjà un compte ?';
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/pro.js"></script>
</body>
</html>