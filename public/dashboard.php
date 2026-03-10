<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$db = new Database();
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch user balance from accounts table
$sql_balance = "SELECT balance FROM accounts WHERE user_id = '$user_id'";
$res_balance = $db->query($sql_balance);
$user_data = $db->fetchOne($res_balance);
$balance = $user_data['balance'] ?? 0.00;

// Fetch last transactions using TABLE_TRANSACTIONS constant
$sql_trans = "SELECT * FROM " . TABLE_TRANSACTIONS . " WHERE from_user_id = '$user_id' OR to_user_id = '$user_id' ORDER BY created_at DESC LIMIT 5";
$recent_transactions_res = $db->query($sql_trans);

$currency = CURRENCY; // Use global currency constant
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FinTech Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-pro sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="pro-brand" href="#"><i class="bi bi-snow2 me-2"></i>FINTECH<span class="fw-normal text-pro-muted">_VULNERABLE</span></a>
        <div class="d-flex align-items-center gap-4">
            <a href="lab.php" class="text-pro-muted text-decoration-none small fw-bold tracking-widest"><i class="bi bi-mortarboard me-1"></i> LAB GUIDE</a>
            <span class="text-pro-muted small fw-medium"><i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($username); ?></span>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" class="btn btn-pro-outline btn-sm px-3">
                    <i class="bi bi-shield-check me-1"></i>ADMIN
                </a>
            <?php endif; ?>
            <a href="../api/auth/logout.php" class="text-pro-danger text-decoration-none small fw-bold">DÉCONNEXION</a>
        </div>
    </div>
</nav>

<div class="container py-5 animate-pro-fadein">
    <div class="row g-4">
        <!-- Balance Card -->
        <div class="col-lg-4">
            <div class="pro-card h-100 bg-pro-soft border-0">
                <p class="text-pro-muted text-uppercase small fw-bold mb-4 tracking-widest">SOLDE ACTUEL</p>
                <h2 class="display-3 fw-bold text-pro-primary mb-2"><?php echo number_format($balance, 0, '.', ' '); ?> <span class="fs-4 fw-normal"><?php echo $currency; ?></span></h2>
                <div class="d-flex align-items-center text-success small fw-medium">
                    <i class="bi bi-arrow-up-right me-1"></i>
                    <span>+2.4% cette semaine</span>
                </div>
                <div class="mt-5 d-flex gap-2">
                    <a href="transfer.php" class="btn-pro-link w-100 py-3 text-center text-decoration-none">TRANSFERT</a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="pro-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">HISTORIQUE DES ÉCHANGES</h5>
                    <a href="#" class="btn btn-pro-outline btn-sm">Tout voir</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table-pro">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($recent_transactions_res) {
                                while ($row = $db->fetchOne($recent_transactions_res)): 
                            ?>
                                <tr>
                                    <td class="text-pro-muted small"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                    <td class="fw-medium">
                                        <?php 
                                            $dir = ($row['from_user_id'] == $user_id) ? 'SORTIE' : 'ENTRÉE';
                                            echo "[{$dir}] " . htmlspecialchars($row['description']); 
                                        ?>
                                    </td>
                                    <td class="fw-bold <?php echo ($row['from_user_id'] == $user_id) ? 'text-danger' : 'text-pro-primary'; ?>">
                                        <?php echo ($row['from_user_id'] == $user_id) ? '-' : '+'; ?>
                                        <?php echo number_format($row['amount'], 0, '.', ' '); ?> <?php echo $currency; ?>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-pro-soft text-pro-primary border border-pro px-3"><?php echo ucfirst($row['status']); ?></span>
                                    </td>
                                </tr>
                            <?php endwhile; } else { ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-pro-muted">Aucune transaction récente.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Security Lab Insights -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="pro-card border-pro bg-pro-soft small">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-shield-lock-fill me-2 text-pro-primary"></i>
                    <span class="fw-bold tracking-widest text-uppercase">Security Insights - Training Lab</span>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded border-pro h-100">
                            <h6 class="x-small fw-bold mb-2 text-pro-primary">IDOR VULNERABILITY</h6>
                            <p class="x-small text-pro-muted mb-0">Le système de transfert utilise un ID utilisateur modifiable dans le formulaire. Un attaquant peut usurper l'identité d'un autre nœud.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded border-pro h-100">
                            <h6 class="x-small fw-bold mb-2 text-pro-primary">XSS INJECTION</h6>
                            <p class="x-small text-pro-muted mb-0">Les descriptions de transactions ne sont pas filtrées. Injecter du HTML peut compromettre la session de l'administrateur.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded border-pro h-100">
                            <h6 class="x-small fw-bold mb-2 text-pro-primary">RACE CONDITION</h6>
                            <p class="x-small text-pro-muted mb-0">L'absence de transactions SQL atomiques permet de retirer plus d'argent que le solde réel via des requêtes simultanées.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer mt-auto py-5 border-top border-pro">
    <div class="container text-center">
        <p class="text-pro-muted x-small">&copy; 2026 FINTECH_VULNERABLE SOLUTIONS. TOUS DROITS RÉSERVÉS.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/pro.js"></script>
</body>
</html>
