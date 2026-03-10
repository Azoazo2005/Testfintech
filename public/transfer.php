<?php 
require_once __DIR__ . '/../core/Auth.php'; 
require_once __DIR__ . '/../config/constants.php'; 
session_start(); 
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
    <title>Transfert - FinTech_Vulnerable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-pro sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="pro-brand" href="dashboard.php"><i class="bi bi-snow2 me-2"></i>FINTECH<span class="fw-normal text-pro-muted">_VULNERABLE</span></a>
        <div class="d-flex align-items-center gap-4">
            <a href="dashboard.php" class="text-pro-muted text-decoration-none small fw-bold">DASHBOARD</a>
            <a href="../api/auth/logout.php" class="text-pro-danger text-decoration-none small fw-bold">DÉCONNEXION</a>
        </div>
    </div>
</nav>

<div class="container py-5 animate-pro-fadein">
    <div class="row g-4">
        <!-- New Transfer -->
        <div class="col-lg-6">
            <div class="pro-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-send-fill text-pro-primary me-2"></i>NOUVEAU TRANSFERT</h5>
                
                <div id="message" class="alert small mb-4" style="display: none;"></div>

                <form id="transferForm">
                    <input type="hidden" name="method_name" id="method_name" value="Orange Money">
                    <div class="form-group-pro">
                        <label class="form-label-pro">Méthode de Transfert</label>
                        <div class="d-flex gap-2 flex-wrap mb-3">
                            <input type="radio" class="btn-check" name="method" id="method1" checked onclick="document.getElementById('method_name').value='Orange Money'">
                            <label class="btn btn-pro-outline px-3 py-2 text-pro-muted" for="method1">
                                <i class="bi bi-phone me-1"></i> Orange Money
                            </label>
                            
                            <input type="radio" class="btn-check" name="method" id="method2" onclick="document.getElementById('method_name').value='Wave'">
                            <label class="btn btn-pro-outline px-3 py-2 text-pro-muted" for="method2">
                                <i class="bi bi-water me-1"></i> Wave
                            </label>

                            <input type="radio" class="btn-check" name="method" id="method3" onclick="document.getElementById('method_name').value='Virement'">
                            <label class="btn btn-pro-outline px-3 py-2 text-pro-muted" for="method3">
                                <i class="bi bi-bank me-1"></i> Virement
                            </label>
                        </div>
                    </div>

                    <div class="form-group-pro">
                        <label class="form-label-pro">Opérateur Destinataire (ID)</label>
                        <input type="number" id="to_user_id" name="to_user_id" class="form-control-pro" placeholder="Ex: 2" required>
                        <div class="x-small text-pro-muted mt-2">Nœuds détectés : 1 (admin), 2 (alice), 3 (bob), 4 (victim)</div>
                    </div>
                    
                    <div class="form-group-pro">
                        <label class="form-label-pro">Montant (<?php echo CURRENCY; ?>)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-pro text-pro-muted"><?php echo CURRENCY; ?></span>
                            <input type="number" id="amount" name="amount" step="1" class="form-control-pro" placeholder="0" required>
                        </div>
                    </div>

                    <div class="form-group-pro mb-4">
                        <label class="form-label-pro">Référence de l'Opération</label>
                        <input type="text" id="description" name="description" class="form-control-pro" placeholder="Motif du transfert">
                    </div>

                    <!-- VULNÉRABILITÉ LAB : Champ caché modifiable -->
                    <input type="hidden" name="from_user_id" value="<?php echo $auth->getUserId(); ?>">

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn-pro flex-grow-1 py-3">
                            <i class="bi bi-check-circle-fill me-2"></i>VALIDER LE TRANSFERT
                        </button>
                        <a href="dashboard.php" class="btn btn-pro-outline py-3 px-4">ANNULER</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Exploitation Lab -->
        <div class="col-lg-6">
            <div class="pro-card bg-pro-soft border-0">
                <h6 class="fw-bold mb-4 text-pro-primary uppercase tracking-widest"><i class="bi bi-bug-fill me-2"></i>SCÉNARIOS D'EXPLOITATION</h6>
                
                <div class="mb-4">
                    <h6 class="small fw-bold text-pro-text mb-1"><i class="bi bi-shield-exclamation me-1"></i>GUIDE ÉTUDIANT : IDOR PRÉ-COMPLI</h6>
                    <p class="x-small text-pro-muted">Le champ <code>from_user_id</code> est un champ caché. Un utilisateur malveillant peut changer cette valeur avant de cliquer sur le bouton d'envoi pour <strong>voler de l'argent</strong> d'un autre compte.</p>
                </div>
                
                <div class="mb-4">
                    <h6 class="small fw-bold text-pro-text mb-1">1. Flux Monétaire Négatif</h6>
                    <p class="x-small text-pro-muted">Tentez d'injecter une valeur négative (<code>-500</code>) pour inverser le flux du débit (Ajouter de l'argent à votre compte au lieu d'en envoyer).</p>
                </div>

                <div class="mb-4">
                    <h6 class="small fw-bold text-pro-text mb-1">2. Usurpation de Nœud (IDOR)</h6>
                    <p class="x-small text-pro-muted">Inspectez la page, cherchez <code>&lt;input type="hidden" name="from_user_id" ...&gt;</code> et changez la valeur par <code>4</code> (Victime) pour vider son compte.</p>
                </div>

                <div class="mb-4">
                    <h6 class="small fw-bold text-pro-text mb-1">3. Race Condition</h6>
                    <button onclick="raceConditionAttack()" class="btn btn-sm btn-pro py-2 px-3 bg-pro-danger border-0 mb-2">
                        <i class="bi bi-lightning-fill me-1"></i>TESTER LA RACE CONDITION
                    </button>
                    <p class="x-small text-pro-muted">Le script envoie 5 requêtes ultra-rapides pour tenter de dépasser le solde autorisé.</p>
                </div>

                <div class="mb-4">
                    <h6 class="small fw-bold text-pro-text mb-1">4. Injection de Script (XSS)</h6>
                    <p class="x-small text-pro-muted">Dans la <strong>Description</strong>, injectez : <code>&lt;script&gt;alert('Compte Piraté')&lt;/script&gt;</code>. L'administrateur verra l'alerte sur son panneau de contrôle.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overlays -->
<div id="paymentOverlay" class="pro-overlay">
    <div>
        <span class="loader-pro mb-4"></span>
        <h4 class="fw-bold text-pro-primary">Traitement en cours...</h4>
        <p class="text-pro-muted small">Sécurisation de la transaction avec votre opérateur</p>
    </div>
</div>

<div id="receiptOverlay" class="pro-overlay">
    <div class="pro-receipt-card animate-pro-fadein">
        <div class="text-center mb-4">
            <div class="pro-brand justify-content-center mb-2"><i class="bi bi-snow2 me-2"></i>FINTECH<span class="fw-normal text-pro-muted">_VULNERABLE</span></div>
            <h5 class="fw-bold text-uppercase tracking-widest small">Reçu de Transaction</h5>
        </div>
        
        <div id="receiptContent">
            <!-- Dynamic Content -->
        </div>

        <div class="mt-4 pt-3 border-top border-pro d-flex gap-2">
            <button onclick="window.print()" class="btn btn-pro-outline flex-grow-1"><i class="bi bi-printer me-2"></i>Imprimer</button>
            <button onclick="location.href='dashboard.php'" class="btn-pro flex-grow-1">Terminer</button>
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
<script>
    document.getElementById('transferForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const method = document.getElementById('method_name').value;
        const msgDiv = document.getElementById('message');
        msgDiv.style.display = 'none';

        // 1. Démarrer la simulation
        const overlay = document.getElementById('paymentOverlay');
        overlay.style.display = 'flex';

        try {
            // Simulation d'attente opérateur (1.5s)
            await new Promise(resolve => setTimeout(resolve, 1500));

            const response = await fetch('../api/transfer/send.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            overlay.style.display = 'none';

            if (result.success) {
                showReceipt(result);
            } else {
                msgDiv.className = 'alert alert-danger border-0 small';
                msgDiv.textContent = result.message;
                msgDiv.style.display = 'block';
                window.scrollTo({top: 0, behavior: 'smooth'});
            }
        } catch (error) {
            overlay.style.display = 'none';
            msgDiv.className = 'alert alert-danger border-0 small';
            msgDiv.textContent = 'Erreur critique lors du transfert réseau.';
            msgDiv.style.display = 'block';
        }
    });

    function showReceipt(data) {
        const content = document.getElementById('receiptContent');
        const now = new Date().toLocaleString('fr-FR');
        
        content.innerHTML = `
            <div class="receipt-line">
                <span class="text-pro-muted">Date</span>
                <span class="fw-bold">${now}</span>
            </div>
            <div class="receipt-line">
                <span class="text-pro-muted">Méthode</span>
                <span class="fw-bold">${data.method}</span>
            </div>
            <div class="receipt-line">
                <span class="text-pro-muted">Destinataire (ID)</span>
                <span class="fw-bold">Node #${document.getElementById('to_user_id').value}</span>
            </div>
            <div class="receipt-line">
                <span class="text-pro-muted">Référence</span>
                <span class="fw-bold">${document.getElementById('description').value || 'N/A'}</span>
            </div>
            <div class="receipt-total">
                <div class="receipt-line pb-2">
                    <span class="text-pro-muted">Sous-total</span>
                    <span>${data.subtotal.toLocaleString()} FCFA</span>
                </div>
                <div class="receipt-line pb-2">
                    <span class="text-pro-muted">Frais Bancaires (1%)</span>
                    <span class="text-danger">${data.fee.toLocaleString()} FCFA</span>
                </div>
                <div class="receipt-line pt-2 border-top">
                    <span class="fw-bold">Total Déduit</span>
                    <span class="text-pro-primary fw-bold" style="font-size: 1.3rem;">${data.total.toLocaleString()} FCFA</span>
                </div>
            </div>
            <div class="mt-3 text-center">
                <p class="x-small text-pro-muted fst-italic">Transaction ID: ${data.transaction_id || 'ARCTIC-' + Math.random().toString(36).substr(2, 9).toUpperCase()}</p>
            </div>
        `;
        
        document.getElementById('receiptOverlay').style.display = 'flex';
    }

    function raceConditionAttack() {
        const amount = 500;
        const promises = [];
        for (let i = 0; i < 5; i++) {
            const formData = new FormData();
            formData.append('to_user_id', '2');
            formData.append('amount', amount);
            formData.append('description', `Race condition test ${i+1}`);
            formData.append('from_user_id', '<?php echo $auth->getUserId(); ?>');
            formData.append('method_name', 'Virement');
            promises.push(fetch('../api/transfer/send.php', {
                method: 'POST',
                body: formData
            }));
        }
        Promise.all(promises).then(results => {
            alert(`${results.length} transferts envoyés simultanément ! Vérifiez le solde.`);
            setTimeout(() => location.reload(), 2000);
        });
    }
</script> 
</body>
</html>
