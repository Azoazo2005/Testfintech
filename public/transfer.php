<?php
session_start();
require_once '../core/Auth.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$userId = $auth->getUserId();
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert - FinTech Demo</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* ===== Transfer Page Specific ===== */
        .transfer-layout {
            max-width: 1060px;
            margin: 0 auto;
            padding: 2.5rem 2rem 2rem;
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 1.8rem;
            align-items: start;
        }

        .page-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .page-subtitle {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-bottom: 1.2rem;
        }

        .transfer-form-card {
            padding: 1.8rem 2rem;
        }

        .form-group {
            margin-bottom: 1.3rem;
        }

        .form-label-custom {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .input-with-icon {
            position: relative;
            max-width: 480px;
        }

        .input-with-icon .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 0.85rem;
            pointer-events: none;
        }

        .input-with-icon input {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 1.5px solid #e8e8f0;
            border-radius: 8px;
            font-family: var(--font);
            font-size: 0.9rem;
            color: var(--text-primary);
            background: #fff;
            transition: var(--transition);
            outline: none;
        }

        .input-with-icon input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(63, 81, 181, 0.1);
        }

        .input-with-icon input::placeholder {
            color: var(--text-muted);
        }

        .input-help {
            margin-top: 5px;
            font-size: 0.72rem;
            color: var(--primary);
            font-weight: 500;
        }

        .textarea-custom {
            width: 100%;
            max-width: 480px;
            padding: 12px;
            border: 1.5px solid #e8e8f0;
            border-radius: 8px;
            font-family: var(--font);
            font-size: 0.9rem;
            color: var(--text-primary);
            background: #fff;
            transition: var(--transition);
            outline: none;
            resize: vertical;
            min-height: 90px;
        }

        .textarea-custom:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(63, 81, 181, 0.1);
        }

        .textarea-custom::placeholder {
            color: var(--text-muted);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 1.3rem;
        }

        .btn-send {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 22px;
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 14px rgba(63, 81, 181, 0.3);
        }

        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(63, 81, 181, 0.4);
        }

        .btn-cancel {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: transparent;
            color: var(--primary);
            border: 1.5px solid var(--primary);
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: rgba(63, 81, 181, 0.05);
        }

        /* Alert message */
        .alert-modern {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 1.2rem;
            display: none;
        }

        .alert-modern.alert-success {
            background: var(--green-light);
            color: #2e7d32;
            border: 1px solid rgba(76, 175, 80, 0.2);
            display: block;
        }

        .alert-modern.alert-danger {
            background: #ffebee;
            color: #c62828;
            border: 1px solid rgba(239, 83, 80, 0.2);
            display: block;
        }

        /* Vuln panel specifics for transfer page */
        .vuln-panel-transfer {
            padding: 1.4rem;
        }

        .vuln-panel-transfer .vuln-header {
            margin-bottom: 1.2rem;
            padding-bottom: 0.8rem;
        }

        .vuln-panel-transfer .vuln-header h3 {
            font-size: 0.75rem;
            letter-spacing: 1.5px;
        }

        .vuln-panel-transfer .vuln-section {
            margin-bottom: 1.1rem;
        }

        .vuln-panel-transfer .vuln-label {
            font-size: 0.78rem;
        }

        .vuln-panel-transfer .vuln-desc {
            font-size: 0.72rem;
        }

        .vuln-step-number {
            font-weight: 700;
        }

        .vuln-code-inline {
            background: rgba(239, 83, 80, 0.15);
            color: var(--red);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.72rem;
        }

        .vuln-panel-transfer .btn-race {
            padding: 10px 16px;
            font-size: 0.78rem;
            border-radius: 8px;
        }

        .vuln-panel-transfer .vuln-footer {
            margin-top: 1rem;
            padding-top: 0.8rem;
            font-size: 0.6rem;
        }

        @media (max-width: 960px) {
            .transfer-layout {
                grid-template-columns: 1fr;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- ===== Top Navbar ===== -->
    <nav class="top-navbar">
        <a href="dashboard.php" class="nav-brand">
            <div class="nav-brand-icon">⬡</div>
            <div class="nav-brand-text"><span>FinTech</span>Demo</div>
        </a>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="transfer.php" class="active">Transfert</a></li>
            <li><a href="#" onclick="logout()">Déconnexion</a></li>
        </ul>
        <div class="nav-user">
            <span><?php echo htmlspecialchars($username); ?> (ID: <?php echo $userId; ?>)</span>
        </div>
    </nav>

    <!-- ===== Main Content ===== -->
    <div class="transfer-layout">
        <!-- Left Column -->
        <div class="left-column">
            <h1 class="page-title">Nouveau transfert</h1>
            <p class="page-subtitle">Effectuez un virement sécurisé vers un autre compte.</p>

            <div class="card-modern transfer-form-card">
                <div id="message" class="alert-modern"></div>

                <form id="transferForm">
                    <div class="form-group">
                        <label class="form-label-custom">ID destinataire</label>
                        <div class="input-with-icon">
                            <span class="input-icon">👤</span>
                            <input type="number" id="to_user_id" name="to_user_id" placeholder="Ex: 2" required>
                        </div>
                        <p class="input-help">IDs disponibles : 1 (admin), 2 (alice), 3 (bob), 4 (victim)</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom">Montant (€)</label>
                        <div class="input-with-icon">
                            <span class="input-icon">€</span>
                            <input type="number" id="amount" name="amount" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label-custom">Description</label>
                        <textarea class="textarea-custom" id="description" name="description" placeholder="Motif du virement..."></textarea>
                    </div>

                    <!-- VULNÉRABILITÉ : Champ caché modifiable -->
                    <input type="hidden" name="from_user_id" value="<?php echo $userId; ?>">

                    <div class="form-actions">
                        <button type="submit" class="btn-send">
                            <span>▶</span> Envoyer
                        </button>
                        <a href="dashboard.php" class="btn-cancel">Annuler</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Vulnerability Lab -->
        <div class="vuln-panel vuln-panel-transfer">
            <div class="vuln-header">
                <span class="shield-icon">🛡</span>
                <h3>Labo Vulnérabilités</h3>
            </div>

            <!-- 1. Montant négatif -->
            <div class="vuln-section">
                <div class="vuln-label">
                    <span class="dot dot-yellow"></span>
                    <span class="label-text text-yellow"><span class="vuln-step-number">1. Montant négatif</span></span>
                </div>
                <p class="vuln-desc">Essayez de saisir <span class="vuln-code-inline">-100</span> comme montant.</p>
            </div>

            <!-- 2. Modifier le compte source -->
            <div class="vuln-section">
                <div class="vuln-label">
                    <span class="dot dot-red"></span>
                    <span class="label-text text-red"><span class="vuln-step-number">2. Modifier le compte source</span></span>
                </div>
                <p class="vuln-desc">Ouvrez DevTools (F12), modifiez le champ caché <span class="vuln-code-inline">from_user_id</span> pour débiter un autre compte.</p>
            </div>

            <!-- 3. Race condition -->
            <div class="vuln-section">
                <div class="vuln-label">
                    <span class="dot dot-green"></span>
                    <span class="label-text text-green"><span class="vuln-step-number">3. Race condition</span></span>
                </div>
                <p class="vuln-desc">Envoie plusieurs transferts simultanés pour dépasser le solde.</p>
                <button class="btn-race" onclick="raceConditionAttack()">
                    ⚡ Attaque race condition
                </button>
            </div>

            <div class="vuln-footer">Environnement de test isolé</div>
        </div>
    </div>

    <script>
        document.getElementById('transferForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const messageDiv = document.getElementById('message');

            try {
                const response = await fetch('../api/transfer/send.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    messageDiv.className = 'alert-modern alert-success';
                    messageDiv.textContent = '✓ ' + result.message;
                    this.reset();
                } else {
                    messageDiv.className = 'alert-modern alert-danger';
                    messageDiv.textContent = '✕ ' + result.message;
                }
            } catch (error) {
                messageDiv.className = 'alert-modern alert-danger';
                messageDiv.textContent = '✕ Erreur lors du transfert';
            }
        });

        function raceConditionAttack() {
            const amount = 500;
            const promises = [];

            for (let i = 0; i < 5; i++) {
                const formData = new FormData();
                formData.append('to_user_id', '2');
                formData.append('amount', amount);
                formData.append('description', `Race condition test ${i + 1}`);
                promises.push(fetch('../api/transfer/send.php', {
                    method: 'POST',
                    body: formData
                }));
            }

            Promise.all(promises).then(results => {
                alert(`${results.length} transferts envoyés simultanément !`);
                setTimeout(() => location.reload(), 2000);
            });
        }

        function logout() {
            fetch('../api/auth/logout.php').then(() => {
                window.location.href = 'index.php';
            });
        }
    </script>
</body>
</html>
