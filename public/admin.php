<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
session_start();

// Simplified role check (Vulnerable for training: IDOR/Privilege Escalation)
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$db = new Database();

// Fetch ALL transactions (Admin View) join with sender/receiver
$query = "SELECT t.*, u1.username as sender, u2.username as receiver 
          FROM " . TABLE_TRANSACTIONS . " t 
          JOIN users u1 ON t.from_user_id = u1.id 
          JOIN users u2 ON t.to_user_id = u2.id 
          ORDER BY t.created_at DESC";
$all_transactions = $db->query($query);

// Statistics
$volume_res = $db->query("SELECT SUM(amount) as total FROM " . TABLE_TRANSACTIONS);
$volume_data = $db->fetchOne($volume_res);
$total_volume = $volume_data['total'] ?? 0;

$user_res = $db->query("SELECT COUNT(*) as count FROM users");
$user_data = $db->fetchOne($user_res);
$user_count = $user_data['count'] ?? 0;

$currency = CURRENCY;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - FinTech_Vulnerable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-pro sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="pro-brand" href="dashboard.php"><i class="bi bi-shield-lock-fill me-2"></i>FINTECH</a>
        <div class="d-flex align-items-center gap-4">
            <a href="lab.php" class="text-pro-muted text-decoration-none small fw-bold tracking-widest lab-only"><i class="bi bi-mortarboard me-1"></i> LAB GUIDE</a>
            <a href="transfer.php" class="text-pro-primary text-decoration-none small fw-bold tracking-widest"><i class="bi bi-send-fill me-1"></i> TRANSFERT</a>
            <div class="d-flex align-items-center me-4 lab-only">
                <span class="badge bg-pro-soft text-pro-primary border-pro px-3 py-2">
                    <i class="bi bi-broadcast me-1"></i> SURVEILLANCE ACTIVE
                </span>
            </div>
            <a href="../api/auth/logout.php" class="text-pro-danger text-decoration-none small fw-bold ms-4">DÉCONNEXION</a>
        </div>
    </div>
</nav>

<div class="container py-5 animate-pro-fadein">
    <!-- Admin Hero Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="pro-card border-0 bg-pro-soft p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="text-pro-muted small fw-bold mb-0">VOLUME TOTAL DES ÉCHANGES</p>
                    <i class="bi bi-graph-up-arrow text-pro-primary"></i>
                </div>
                <h2 class="display-5 fw-bold text-pro-primary"><?php echo number_format($total_volume, 0, '.', ' '); ?> <?php echo $currency; ?></h2>
                <p class="text-pro-muted small mb-0 mt-2">Flux monétaire global détecté sur les serveurs FINTECH.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="pro-card border-0 bg-pro-soft p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="text-pro-muted small fw-bold mb-0">OPÉRATEURS ACTIFS</p>
                    <i class="bi bi-people-fill text-pro-primary"></i>
                </div>
                <h2 class="display-5 fw-bold text-pro-primary"><?php echo $user_count; ?></h2>
                <p class="text-pro-muted small mb-0 mt-2">Comptes utilisateurs enregistrés sur le nœud local.</p>
            </div>
        </div>
    </div>

    <div class="pro-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0"><i class="bi bi-activity text-pro-primary me-2"></i>MONITEUR GLOBAL DES TRANSACTIONS</h5>
            <div class="btn-group">
                <button class="btn btn-pro-outline btn-sm">EXPORTER XML</button>
                <button class="btn btn-pro-outline btn-sm">EXPORTER PDF</button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table-pro">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Opérateur</th>
                        <th>Détails</th>
                        <th>Montant</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($all_transactions) {
                        while ($row = $db->fetchOne($all_transactions)): 
                    ?>
                        <tr>
                            <td class="text-pro-muted small"><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                            <td class="fw-bold">
                                <span class="badge bg-white text-pro-muted border-pro text-lowercase">@<?php echo htmlspecialchars($row['sender']); ?></span>
                                <i class="bi bi-arrow-right mx-1 text-pro-primary"></i>
                                <span class="badge bg-white text-pro-muted border-pro text-lowercase">@<?php echo htmlspecialchars($row['receiver']); ?></span>
                            </td>
                            <td class="fw-medium text-pro-muted"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="fw-bold text-pro-primary"><?php echo number_format($row['amount'], 0, '.', ' '); ?> <?php echo $currency; ?></td>
                            <td>
                                <span class="badge rounded-pill bg-pro-soft text-pro-primary border-pro px-2"><?php echo ucfirst($row['status']); ?></span>
                            </td>
                        </tr>
                    <?php endwhile; } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="footer mt-auto py-5 border-top border-pro">
    <div class="container text-center">
        <p class="text-pro-muted x-small">&copy; 2026 FINTECH SOLUTIONS. TOUS DROITS RÉSERVÉS.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/pro.js"></script>
</body>
</html>
