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
$sql_balance = "SELECT balance FROM accounts WHERE user_id = ?";
$stmt_balance = $db->prepare($sql_balance);
$res_balance = $db->execute($stmt_balance, [$user_id], "i");
$user_data = $db->fetchOne($res_balance);
$balance = $user_data['balance'] ?? 0.00;

// Fetch last transactions using TABLE_TRANSACTIONS constant
$sql_trans = "SELECT * FROM " . TABLE_TRANSACTIONS . " WHERE from_user_id = ? OR to_user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt_trans = $db->prepare($sql_trans);
$recent_transactions_res = $db->execute($stmt_trans, [$user_id, $user_id], "ii");

$currency = CURRENCY; // Use global currency constant
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Fintech Robuste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-pro sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="pro-brand" href="dashboard.php"><i class="bi bi-shield-check-fill me-2"></i>FINTECH</a>
        <div class="d-flex align-items-center gap-4">
            <span class="text-pro-muted small fw-medium"><i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($username); ?></span>
            <a href="lab.php" class="btn btn-primary btn-sm px-3 shadow-sm border-0 lab-only" style="background: #2ecc71;">
                <i class="bi bi-mortarboard-fill me-1"></i> LAB GUIDE
            </a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" class="btn btn-pro-outline btn-sm px-3">
                    <i class="bi bi-shield-lock me-1"></i>ADMIN
                </a>
            <?php endif; ?>
            <a href="transfer.php" class="text-pro-muted text-decoration-none small fw-bold">TRANSFERT</a>
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
    <!-- System & Lab Status -->
    <div class="row mt-4 g-4">
        <div class="col-lg-8">
            <div class="pro-card border-pro bg-pro-soft small py-3 px-4 h-100 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-patch-check-fill me-2 text-success"></i>
                    <span class="fw-bold tracking-widest text-uppercase">Statut de Sécurité : OPTIMAL</span>
                </div>
                <span class="text-pro-muted x-small">Dernière vérification : <?php echo date('H:i'); ?></span>
            </div>
        </div>
        <div class="col-lg-4 lab-only">
            <div class="pro-card border-2 border-primary border-dashed bg-white p-4 h-100">
                <h6 class="fw-bold text-pro-primary mb-3 small"><i class="bi bi-mortarboard-fill me-2"></i>LAB: TEST PERSISTENT XSS</h6>
                <p class="x-small text-pro-muted mb-2">Les descriptions de transactions sont désormais encodées. Injectez un payload pour vérifier.</p>
                <div class="d-flex gap-2">
                    <code class="p-2 border rounded bg-pro-soft x-small flex-grow-1" id="xss_payload">&lt;script&gt;alert('Session Stolen')&lt;/script&gt;</code>
                    <button class="btn btn-pro btn-sm" onclick="copyXSS()">COPIER LAB</button>
                </div>
                <div class="mt-3 text-center">
                    <a href="lab.php" class="text-pro-primary x-small fw-bold text-decoration-none"><i class="bi bi-arrow-right-circle me-1"></i>DÉCOUVRIR LE LAB COMPLET</a>
                </div>
                <p class="x-small text-pro-muted mt-2 fst-italic">Allez sur la page transfert et utilisez ce motif.</p>
            </div>
        </div>
    </div>
</div>

<footer class="footer mt-auto py-5 border-top border-pro">
    <div class="container text-center">
        <p class="text-pro-muted x-small">&copy; 2026 FINTECH SOLUTIONS. TOUS DROITS RÉSERVÉS.</p>
    </div>
</footer>

<script>
    function copyXSS() {
        const payload = document.getElementById('xss_payload').innerText;
        navigator.clipboard.writeText(payload);
        alert('Payload XSS copié ! Utilisez-le dans la description d\'un transfert.');
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/pro.js"></script>
</body>
</html>
