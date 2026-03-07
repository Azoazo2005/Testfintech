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
$wallet = new Wallet(); 
$balance = $wallet->getBalance($userId); 
$history = $wallet->getTransactionHistory($userId, 10); 
?> 
<!DOCTYPE html> 
<html lang="fr"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Dashboard - FinTech Demo</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"> 
</head> 
<body> 
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary"> 
        <div class="container"> 
            <a class="navbar-brand" href="#">FinTech Demo</a> 
            <div class="navbar-nav ms-auto"> 
                <a class="nav-link" href="dashboard.php">Dashboard</a> 
                <a class="nav-link" href="transfer.php">Transfert</a> 
                <a class="nav-link" href="#" onclick="logout()">Déconnexion</a> 
            </div> 
        </div> 
    </nav> 
    <div class="container mt-4"> 
        <div class="row"> 
            <div class="col-md-8"> 
                <div class="card"> 
                    <div class="card-header"> 
                        <h5>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></h5> 
                        <small class="text-muted">User ID: <?php echo $userId; ?></small> 
                    </div> 
                    <div class="card-body"> 
                        <h2 class="text-primary"> 
                            <?php echo number_format($balance['balance'], 2); ?> € 
                        </h2> 
                        <p class="text-muted">Solde actuel</p> 
                        <a href="transfer.php" class="btn btn-success">Nouveau transfert</a> 
                    </div> 
                </div> 
                <div class="card mt-4"> 
                    <div class="card-header"> 
                        <h6>Historique des transactions</h6> 
                    </div> 
                    <div class="card-body"> 
                        <div class="table-responsive"> 
                            <table class="table"> 
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
                                    <?php foreach ($history as $transaction): ?> 
                                    <tr> 
                                        <td><?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></td> 
                                        <td><?php echo htmlspecialchars($transaction['from_username']); ?></td> 
                                        <td><?php echo htmlspecialchars($transaction['to_username']); ?></td> 
                                        <td><?php echo number_format($transaction['amount'], 2); ?> €</td> 
                                        <td> 
                                            <?php  
                                            // VULNÉRABILITÉ XSS : Pas d'échappement HTML 
                                            echo $transaction['description'];  
                                            ?> 
                                        </td> 
                                    </tr> 
                                    <?php endforeach; ?> 
                                </tbody> 
                            </table> 
                        </div> 
                    </div> 
                </div> 
            </div> 
            <div class="col-md-4"> 
                <div class="card"> 
                    <div class="card-header"> 
                        <h6>Test des vulnérabilités</h6> 
                    </div> 
                    <div class="card-body"> 
                        <h6>IDOR - Voir le solde d'autres utilisateurs :</h6> 
                        <div class="btn-group-vertical d-grid gap-2"> 
                            <button onclick="viewBalance(1)" class="btn btn-sm btn-outline-danger">Admin (ID: 1)</button> 
                            <button onclick="viewBalance(4)" class="btn btn-sm btn-outline-danger">Victime (ID: 4)</button> 
                        </div> 
                        <hr> 
                        <h6>XSS Test :</h6> 
                        <p class="small">Essayez un transfert avec comme description :</p> 
                        <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
    <script src="assets/js/app.js"></script> 
    <script> 
        function viewBalance(userId) { 
            fetch(`../api/wallet/balance.php?user_id=${userId}`) 
                .then(r => r.json()) 
                .then(data => { 
                    if (data.success) { 
                        alert(`Solde de ${data.data.full_name}: ${data.data.balance}€`); 
                    } 
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