<?php 
session_start(); 
require_once '../core/Auth.php'; 
$auth = new Auth(); 
if (!$auth->isLoggedIn()) { 
    header('Location: index.php'); 
exit; 
} 
?> 
<!DOCTYPE html> 
<html lang="fr"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Transfert - FinTech Demo</title> 
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
        <div class="row justify-content-center"> 
            <div class="col-md-6"> 
                <div class="card"> 
                    <div class="card-header"> 
                        <h5>Nouveau transfert</h5> 
                    </div> 
                    <div class="card-body"> 
                        <div id="message" class="alert" style="display: none;"></div> 
                        <form id="transferForm"> 
                            <div class="mb-3"> 
                                <label for="to_user_id" class="form-label">ID destinataire</label> 
                                <input type="number" class="form-control" id="to_user_id" name="to_user_id" required> 
                                <div class="form-text">IDs disponibles : 1 (admin), 2 (alice), 3 (bob), 4 (victim)</div> 
                            </div> 
                            <div class="mb-3"> 
                                <label for="amount" class="form-label">Montant (€)</label> 
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" required> 
                            </div> 
                            <div class="mb-3"> 
                                <label for="description" class="form-label">Description</label> 
                                <input type="text" class="form-control" id="description" name="description"> 
                            </div> 
                            <!-- VULNÉRABILITÉ : Champ caché modifiable --> 
                            <input type="hidden" name="from_user_id" value="<?php echo $auth->getUserId(); ?>"> 
                            <button type="submit" class="btn btn-success">Envoyer</button> 
                            <a href="dashboard.php" class="btn btn-secondary">Annuler</a> 
                        </form> 
                    </div> 
                </div> 
            </div> 
            <div class="col-md-6"> 
                <div class="card"> 
                    <div class="card-header"> 
                        <h6>Tests d'exploitation</h6> 
                    </div> 
                    <div class="card-body"> 
                        <h6>1. Montant négatif :</h6> 
                        <p class="small">Essayez de saisir <code>-100</code> comme montant</p> 
                        <h6>2. Modifier le compte source :</h6> 
                        <p class="small">Ouvrez DevTools (F12), modifiez le champ caché <code>from_user_id</code> pour débiter un autre compte</p> 
                        <h6>3. Race condition :</h6> 
                        <button onclick="raceConditionAttack()" class="btn btn-sm btn-danger"> 
                            Attaque race condition 
                        </button> 
                        <p class="small">Envoie plusieurs transferts simultanés</p> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
    <script src="assets/js/app.js"></script> 
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
                    messageDiv.className = 'alert alert-success'; 
                    messageDiv.textContent = result.message; 
                    messageDiv.style.display = 'block'; 
                    this.reset(); 
                } else { 
                    messageDiv.className = 'alert alert-danger'; 
                    messageDiv.textContent = result.message; 
                    messageDiv.style.display = 'block'; 
                } 
            } catch (error) { 
                messageDiv.className = 'alert alert-danger'; 
                messageDiv.textContent = 'Erreur lors du transfert'; 
                messageDiv.style.display = 'block'; 
            } 
        }); 

        function raceConditionAttack() { 
            const amount = 500; // Montant à envoyer 
            const promises = []; 
            for (let i = 0; i < 5; i++) { 
                const formData = new FormData(); 
                formData.append('to_user_id', '2'); 
                formData.append('amount', amount); 
                formData.append('description', `Race condition test ${i+1}`); 
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
