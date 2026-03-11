<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
session_start();

// SECURITY: Stricter role check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: dashboard.php');
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

// Fetch Security Logs
$admin_logs_query = "SELECT l.*, u.username FROM admin_logs l JOIN users u ON l.admin_id = u.id ORDER BY created_at DESC LIMIT 10";
$admin_logs = $db->query($admin_logs_query);

$audit_logs_query = "SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 10";
$audit_logs = $db->query($audit_logs_query);

// Statistics
$volume_res = $db->query("SELECT SUM(amount) as total FROM " . TABLE_TRANSACTIONS);
$volume_data = $db->fetchOne($volume_res);
$total_volume = $volume_data['total'] ?? 0;

// Revenue from fees
$revenue_res = $db->query("SELECT SUM(fee) as total_fee FROM " . TABLE_TRANSACTIONS);
$revenue_data = $db->fetchOne($revenue_res);
$total_revenue = $revenue_data['total_fee'] ?? 0;

$user_res = $db->query("SELECT COUNT(*) as count FROM users");
$user_data = $db->fetchOne($user_res);
$user_count = $user_data['count'] ?? 0;

// Chart Data: Transactions per User
$chart_users_res = $db->query("SELECT u.username, COUNT(t.id) as count FROM " . TABLE_TRANSACTIONS . " t JOIN users u ON t.from_user_id = u.id GROUP BY u.id LIMIT 10");
$chart_users_labels = [];
$chart_users_data = [];
if ($chart_users_res) {
    while($row = $db->fetchOne($chart_users_res)) {
        $chart_users_labels[] = $row['username'];
        $chart_users_data[] = $row['count'];
    }
}

// Chart Data: Payment Methods
$chart_methods_res = $db->query("SELECT payment_method, COUNT(*) as count FROM " . TABLE_TRANSACTIONS . " GROUP BY payment_method");
$chart_methods_labels = [];
$chart_methods_data = [];
if ($chart_methods_res) {
    while($row = $db->fetchOne($chart_methods_res)) {
        $chart_methods_labels[] = $row['payment_method'];
        $chart_methods_data[] = $row['count'];
    }
}

$currency = CURRENCY;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Fintech Robuste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-pro sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="pro-brand" href="dashboard.php"><i class="bi bi-shield-check-fill me-2"></i>FINTECH<span class="fw-normal text-pro-muted"> ROBUSTE</span></a>
        <div class="d-flex align-items-center gap-4">
            <div class="d-flex align-items-center me-4">
                <span class="badge bg-success bg-opacity-10 text-success border-success border px-3 py-2">
                    <i class="bi bi-lock-fill me-1"></i> SÉCURITÉ ACTIVE
                </span>
            </div>
            <a href="../api/auth/logout.php" class="text-pro-danger text-decoration-none small fw-bold ms-4">DÉCONNEXION</a>
        </div>
    </div>
</nav>

<div class="container py-5 animate-pro-fadein">
    <!-- Admin Hero Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="pro-card border-0 bg-pro-soft p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="text-pro-muted small fw-bold mb-0">VOLUME GLOBAL ÉCHANGES</p>
                    <i class="bi bi-graph-up-arrow text-pro-primary"></i>
                </div>
                <h2 class="display-6 fw-bold text-pro-primary"><?php echo number_format($total_volume, 0, '.', ' '); ?> <?php echo $currency; ?></h2>
                <p class="text-pro-muted x-small mb-0 mt-2">Moniteur de flux monétaire.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pro-card border-0 bg-pro-soft p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="text-pro-muted small fw-bold mb-0">REVENUS DES FRAIS (1%)</p>
                    <i class="bi bi-piggy-bank-fill text-success"></i>
                </div>
                <h2 class="display-6 fw-bold text-success"><?php echo number_format($total_revenue, 0, '.', ' '); ?> <?php echo $currency; ?></h2>
                <p class="text-pro-muted x-small mb-0 mt-2 text-success">Gagné via Fintech Robuste.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pro-card border-0 bg-pro-soft p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="text-pro-muted small fw-bold mb-0">OPÉRATEURS ACTIFS</p>
                    <i class="bi bi-people-fill text-pro-primary"></i>
                </div>
                <h2 class="display-6 fw-bold text-pro-primary"><?php echo $user_count; ?></h2>
                <p class="text-pro-muted x-small mb-0 mt-2">Utilisateurs enregistrés.</p>
            </div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="pro-card h-100">
                <h6 class="fw-bold mb-4 small text-uppercase tracking-widest text-pro-muted"><i class="bi bi-bar-chart-fill me-2"></i>Activité par Utilisateur</h6>
                <canvas id="userChart" height="150"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="pro-card h-100">
                <h6 class="fw-bold mb-4 small text-uppercase tracking-widest text-pro-muted"><i class="bi bi-pie-chart-fill me-2"></i>Répartition Paiements</h6>
                <canvas id="methodChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Global Transactions -->
        <div class="col-lg-8">
            <div class="pro-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-activity text-pro-primary me-2"></i>MONITEUR GLOBAL</h5>
                </div>
                
                <div class="table-responsive">
                    <table class="table-pro">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Opérateur</th>
                                <th>Détails</th>
                                <th>Montant</th>
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
                                </tr>
                            <?php endwhile; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Security Logs -->
        <div class="col-lg-4">
            <div class="pro-card bg-pro-soft border-0 mb-4">
                <h6 class="fw-bold mb-4 text-pro-primary uppercase tracking-widest"><i class="bi bi-shield-lock-fill me-2"></i>ACTIVITY LOGS</h6>
                <div class="activity-feed">
                    <?php if ($admin_logs): while ($log = $db->fetchOne($admin_logs)): ?>
                        <div class="activity-item mb-3 pb-3 border-bottom border-pro">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold x-small">@<?php echo htmlspecialchars($log['username']); ?></span>
                                <span class="text-pro-muted x-small"><?php echo date('H:i', strtotime($log['created_at'])); ?></span>
                            </div>
                            <p class="mb-0 x-small text-pro-text"><?php echo htmlspecialchars($log['action']); ?>: <?php echo htmlspecialchars($log['details']); ?></p>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>

            <div class="pro-card bg-white border-pro">
                <h6 class="fw-bold mb-4 text-pro-muted uppercase tracking-widest small"><i class="bi bi-eye-fill me-2"></i>AUDIT SYSTEM</h6>
                <div class="audit-feed">
                    <?php if ($audit_logs): while ($log = $db->fetchOne($audit_logs)): ?>
                        <div class="mb-2">
                            <span class="badge bg-light text-dark x-small"><?php echo $log['event_type']; ?></span>
                            <div class="text-pro-muted x-small mt-1"><?php echo htmlspecialchars($log['description']); ?></div>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer mt-auto py-5 border-top border-pro">
    <div class="container text-center">
        <p class="text-pro-muted x-small">&copy; 2026 FINTECH ROBUSTE SOLUTIONS. TOUS DROITS RÉSERVÉS.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/pro.js"></script>
<script>
    // Activity Chart (Bar)
    const userCtx = document.getElementById('userChart').getContext('2d');
    new Chart(userCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart_users_labels); ?>,
            datasets: [{
                label: 'Transactions',
                data: <?php echo json_encode($chart_users_data); ?>,
                backgroundColor: 'rgba(52, 152, 219, 0.2)',
                borderColor: '#3498db',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } }, x: { grid: { display: false } } }
        }
    });

    // Payment Methods Chart (Doughnut)
    const methodCtx = document.getElementById('methodChart').getContext('2d');
    new Chart(methodCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($chart_methods_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($chart_methods_data); ?>,
                backgroundColor: ['#2ecc71', '#3498db', '#f1c40f', '#e74c3c'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 10 } } }
            },
            cutout: '75%'
        }
    });
</script>
</body>
</html>
