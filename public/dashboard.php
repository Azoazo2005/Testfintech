<?php
session_start();

// Vérification très basique de la session (V1)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>FinTech Demo - Mon Espace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">FinTech Demo</a>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Bienvenue, <?php echo $_SESSION['username'] ?? 'Utilisateur'; ?> !
                </span>
                <a href="../api/auth/logout.php" class="btn btn-outline-danger btn-sm">Déconnexion</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Mon Solde</div>
                    <div class="card-body text-center">
                        <h2 class="card-title">0.00 €</h2>
                        <p class="text-muted small">Solde actuel</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-success text-white">Faire un transfert</div>
                    <div class="card-body">
                        <p class="text-muted text-center">Formulaire en construction...</p>
                    </div>
                </div>

            </div>

            <div class="col-md-8">
                
                <div class="card">
                    <div class="card-header bg-secondary text-white">Historique des transactions</div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Bénéficiaire</th>
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucune transaction à afficher pour le moment.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>