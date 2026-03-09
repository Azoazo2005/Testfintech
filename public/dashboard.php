<?php
session_start();
require_once '../core/Auth.php';
require_once '../core/Wallet.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$userId = $auth->getUserId();
$username = $_SESSION['username'];
$wallet = new Wallet();
$balance = $wallet->getBalance($userId);
$history = $wallet->getTransactionHistory($userId, 10);

// Helper function for avatar class
function getAvatarClass($name) {
    $map = [
        'alice' => 'avatar-alice',
        'bob' => 'avatar-bob',
        'victim' => 'avatar-victim',
        'admin' => 'avatar-admin'
    ];
    return $map[strtolower($name)] ?? 'avatar-default';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FinTech Demo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- ===== Top Navbar ===== -->
    <nav class="top-navbar">
        <a href="dashboard.php" class="nav-brand">
            <div class="nav-brand-icon">⬡</div>
            <div class="nav-brand-text"><span>FinTech</span>Demo</div>
        </a>
        <ul class="nav-links">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="transfer.php">Transfert</a></li>
            <li><a href="#" onclick="logout()">Déconnexion</a></li>
        </ul>
        <div class="nav-user">
            <span><?php echo htmlspecialchars($username); ?> (ID: <?php echo $userId; ?>)</span>

        </div>
    </nav>

    <!-- ===== Main Content ===== -->
    <div class="main-content">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Welcome / Balance Card -->
            <div class="card-modern welcome-card">
                <div class="welcome-header">
                    <div>
                        <h1 class="welcome-title">Bienvenue, <?php echo htmlspecialchars($username); ?></h1>
                        <p class="welcome-userid">User ID: <?php echo $userId; ?></p>
                    </div>
                    <div class="account-badge">Compte Actif</div>
                </div>
                <div class="wallet-icon-float">
                    <div class="wallet-cards">
                        <div class="wc"></div>
                        <div class="wc"></div>
                    </div>
                </div>
                <div class="balance-section">
                    <p class="balance-label">Solde actuel</p>
                    <p class="balance-amount">
                        <?php echo number_format($balance['balance'], 2, '.', ','); ?>
                        <span class="currency">€</span>
                    </p>
                </div>
                <a href="transfer.php" class="btn-transfer">
                    <span>+</span> Nouveau transfert
                </a>
            </div>

            <!-- Transactions Card -->
            <div class="card-modern transactions-card" style="margin-top: 1.5rem;">
                <div class="transactions-header">
                    <div class="transactions-title">
                        <span class="icon">⏱</span>
                        Historique des transactions
                    </div>
                    <a href="#" class="link-view-all">Voir tout</a>
                </div>
                <table class="tx-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>De</th>
                            <th>Vers</th>
                            <th>Montant</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                Aucune transaction
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($history as $tx): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($tx['created_at'])); ?></td>
                            <td>
                                <div class="user-avatar">
                                    <div class="avatar-circle <?php echo getAvatarClass($tx['from_username']); ?>">
                                        <?php echo strtoupper(substr($tx['from_username'], 0, 1)); ?>
                                    </div>
                                    <?php echo htmlspecialchars($tx['from_username']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($tx['to_username']); ?></td>
                            <td>
                                <?php
                                $isIncoming = ($tx['to_user_id'] == $userId);
                                $amountClass = $isIncoming ? 'amount-positive' : 'amount-neutral';
                                $prefix = $isIncoming ? '+' : '';
                                ?>
                                <span class="<?php echo $amountClass; ?>">
                                    <?php echo $prefix . number_format($tx['amount'], 2); ?> €
                                </span>
                            </td>
                            <td class="tx-description">
                                <?php
                                // VULNÉRABILITÉ XSS : Pas d'échappement HTML
                                echo $tx['description'];
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Column: Vulnerability Lab -->
        <div class="vuln-panel">
            <div class="vuln-header">
                <span class="shield-icon">🛡</span>
                <h3>Labo Vulnérabilités</h3>
            </div>

            <!-- IDOR Section -->
            <div class="vuln-section">
                <div class="vuln-label">
                    <span class="dot dot-red"></span>
                    <span class="label-text text-red">IDOR - Insecure Direct Object Reference</span>
                </div>
                <p class="vuln-desc">Tentative d'accès aux soldes d'autres utilisateurs via manipulation d'ID.</p>
                <div class="vuln-buttons">
                    <button class="btn-vuln btn-vuln-green" onclick="viewBalance(1)">Admin (ID: 1)</button>
                    <button class="btn-vuln btn-vuln-red" onclick="viewBalance(4)">Victime (ID: 4)</button>
                </div>
            </div>

            <!-- XSS Section -->
            <div class="vuln-section">
                <div class="vuln-label">
                    <span class="dot dot-yellow"></span>
                    <span class="label-text text-yellow">XSS - Cross-Site Scripting</span>
                </div>
                <p class="vuln-desc">Injectez un script dans la description du transfert pour tester la sanitization.</p>
                <div class="code-block">&lt;script&gt;alert('XSS')&lt;/script&gt;</div>
            </div>

            <!-- Race Condition Section -->
            <div class="vuln-section">
                <div class="vuln-label">
                    <span class="dot dot-green"></span>
                    <span class="label-text text-green">Logique Métier & Race Condition</span>
                </div>
                <p class="vuln-desc">Tests avancés: montants négatifs ou requêtes concurrentes.</p>
                <button class="btn-race" onclick="raceConditionAttack()">
                    ⟳ Attaque Race Condition
                </button>
            </div>

            <div class="vuln-footer">Environnement de test isolé</div>
        </div>
    </div>

    <script>
        function viewBalance(userId) {
            fetch(`../api/wallet/balance.php?user_id=${userId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        alert(`Solde de ${data.data.full_name}: ${data.data.balance}€`);
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(() => alert('Erreur de connexion'));
        }

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
