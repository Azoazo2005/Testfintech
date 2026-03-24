<?php 
require_once __DIR__ . '/../core/Auth.php'; 
require_once __DIR__ . '/../config/constants.php'; 
require_once __DIR__ . '/../config/database.php';
session_start(); 
$auth = new Auth(); 
if (!$auth->isLoggedIn()) { 
    header('Location: index.php'); 
    exit; 
} 

// The user selection dropdown is replaced by a phone number input
?> 
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert - Fintech Robuste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-pro sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="pro-brand" href="dashboard.php"><i class="bi bi-shield-check-fill me-2"></i>FINTECH</a>
        <div class="d-flex align-items-center gap-4">
            <a href="lab.php" class="btn btn-primary btn-sm px-3 shadow-sm border-0 lab-only" style="background: #2ecc71;">
                <i class="bi bi-mortarboard-fill me-1"></i> LAB GUIDE
            </a>
            <a href="dashboard.php" class="text-pro-muted text-decoration-none small fw-bold">DASHBOARD</a>
            <a href="../api/auth/logout.php" class="text-pro-danger text-decoration-none small fw-bold">DÉCONNEXION</a>
        </div>
    </div>
</nav>

<div class="container py-5 animate-pro-fadein">
    <div class="row g-4 justify-content-center">
        <!-- New Transfer -->
        <div class="col-lg-7">
            <div class="pro-card h-100">
                <h5 class="fw-bold mb-4"><i class="bi bi-send-fill text-pro-primary me-2"></i>OPÉRATION DE TRANSFERT SÉCURISÉE</h5>
                
                <div id="message" class="alert small mb-4" style="display: none;"></div>

                <form id="transferForm">
                    <input type="hidden" name="method_name" id="method_name" value="Orange Money">
                    <div class="form-group-pro">
                        <label class="form-label-pro">Méthode de Paiement</label>
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
                                <i class="bi bi-bank me-1"></i> Transfert Bancaire
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-pro">
                                <label class="form-label-pro">NUMÉRO DU DESTINATAIRE</label>
                                <input type="tel" id="phone" name="phone" class="form-control-pro" placeholder="+221770000000" pattern="^\+?[0-9]{9,15}$" required>
                                <i class="bi bi-phone input-icon text-pro-muted"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-pro">
                                <label class="form-label-pro">Montant (<?php echo CURRENCY; ?>)</label>
                                <input type="number" id="amount" name="amount" step="1" class="form-control-pro has-prefix" placeholder="0" required oninput="updateFeeDisplay()">
                                <span class="input-prefix"><?php echo CURRENCY; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-pro mb-4">
                        <label class="form-label-pro">Motif de la Transaction</label>
                        <input type="text" id="description" name="description" class="form-control-pro" placeholder="Description courte">
                    </div>

                    <div class="bg-pro-soft p-3 rounded-pro mb-4">
                        <div class="d-flex justify-content-between x-small mb-1">
                            <span>Frais de service (1%):</span>
                            <span id="fee_display">0 <?php echo CURRENCY; ?></span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold small">
                            <span>Total à débiter:</span>
                            <span id="total_display" class="text-pro-primary">0 <?php echo CURRENCY; ?></span>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn-pro flex-grow-1 py-3">
                            <i class="bi bi-shield-lock-fill me-2"></i>CONFIRMER L'ENVOI SÉCURISÉ
                        </button>
                        <a href="dashboard.php" class="btn btn-pro-outline py-3 px-4">RETOUR</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- LAB Integration Column -->
        <div class="col-lg-4 lab-only">
            <div class="pro-card border-2 border-primary border-dashed bg-pro-soft h-100 p-4">
                <h6 class="fw-bold text-pro-primary mb-4"><i class="bi bi-mortarboard-fill me-2"></i>LAB: VÉRIFICATION ROBUSTE</h6>
                
                <div class="mb-4">
                    <label class="x-small fw-bold text-uppercase tracking-widest text-pro-muted d-block mb-2">1. Test IDOR (Manipulation d'ID)</label>
                    <p class="x-small text-pro-muted leading-relaxed">Tentez de débiter le compte #1 (Admin) en injectant un champ caché. Robuste ignore les entrées client pour l'ID source.</p>
                    <button class="btn btn-pro-outline btn-sm w-100" onclick="injectIDOR()">
                        <i class="bi bi-bug me-1"></i> INJECTER CHAMP `from_user_id`
                    </button>
                </div>

                <div class="mb-4">
                    <label class="x-small fw-bold text-uppercase tracking-widest text-pro-muted d-block mb-2">2. Test Race Condition</label>
                    <p class="x-small text-pro-muted leading-relaxed">Envoie 5 requêtes simultanées de 10k <?php echo CURRENCY; ?>. Robuste utilise des verrous atomiques pour empêcher le solde négatif.</p>
                    <button class="btn btn-pro-outline btn-sm w-100" id="raceBtn" onclick="runRaceCondition()">
                        <i class="bi bi-lightning-charge me-1"></i> TESTER DÉBIT MULTIPLE
                    </button>
                </div>

                <div class="mt-auto p-3 bg-white rounded border-pro opacity-75 text-center">
                    <a href="lab.php" class="text-pro-primary x-small fw-bold text-decoration-none"><i class="bi bi-arrow-right-circle me-1"></i>VOIR TOUS LES TESTS DU LAB</a>
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

<div id="receiptOverlay" class="pro-overlay" style="display: none; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px);">
    <div class="pro-card bg-white p-5 text-center" style="max-width: 450px; width: 90%;">
        <div class="mb-4">
            <i class="bi bi-patch-check-fill text-success" style="font-size: 4rem;"></i>
        </div>
        <h3 class="fw-bold text-pro-text mb-2">TRANSFERT RÉUSSI</h3>
        <p class="text-pro-muted small mb-4">Votre argent a été envoyé en toute sécurité.</p>
        
        <div id="receiptContent" class="text-start bg-pro-soft p-4 rounded-pro mb-4">
            <!-- Dynamic Content -->
        </div>

        <button onclick="location.reload()" class="btn-pro w-100 py-3">OK, COMPRIS</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/pro.js"></script>
<script>
    const BANK_FEE_PERCENT = <?php echo BANK_FEE_PERCENT; ?>;
    const CURRENCY = '<?php echo CURRENCY; ?>';

    function updateFeeDisplay() {
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const fee = amount * BANK_FEE_PERCENT;
        const total = amount + fee;
        
        document.getElementById('fee_display').textContent = `${fee.toLocaleString()} ${CURRENCY}`;
        document.getElementById('total_display').textContent = `${total.toLocaleString()} ${CURRENCY}`;
    }



    document.getElementById('transferForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const msgDiv = document.getElementById('message');
        msgDiv.style.display = 'none';

        const overlay = document.getElementById('paymentOverlay');
        overlay.style.display = 'flex';

        try {
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
                <span class="text-pro-muted">Destinataire</span>
                <span class="fw-bold">${data.recipient_name || 'N/A'}</span>
            </div>
            <div class="receipt-line">
                <span class="text-pro-muted">Numéro du Destinataire</span>
                <span class="fw-bold">${document.getElementById('phone').value}</span>
            </div>
            <div class="receipt-line">
                <span class="text-pro-muted">Référence</span>
                <span class="fw-bold">${document.getElementById('description').value || 'N/A'}</span>
            </div>
            <div class="receipt-total">
                <div class="receipt-line pb-2">
                    <span class="text-pro-muted">Sous-total</span>
                    <span>${data.subtotal.toLocaleString()} ${CURRENCY}</span>
                </div>
                <div class="receipt-line pb-2">
                    <span class="text-pro-muted">Frais Bancaires (1%)</span>
                    <span class="text-danger">${data.fee.toLocaleString()} ${CURRENCY}</span>
                </div>
                <div class="receipt-line pt-2 border-top">
                    <span class="fw-bold">Total Déduit</span>
                    <span class="text-pro-primary fw-bold" style="font-size: 1.3rem;">${data.total.toLocaleString()} ${CURRENCY}</span>
                </div>
            </div>
            <div class="mt-3 text-center">
                <p class="x-small text-pro-muted fst-italic">Transaction ID: ${data.transaction_id || 'ROBUSTE-' + Math.random().toString(36).substr(2, 9).toUpperCase()}</p>
            </div>
        `;
        
        document.getElementById('receiptOverlay').style.display = 'flex';
    }
    function injectIDOR() {
        const form = document.getElementById('transferForm');
        let input = document.getElementById('idor_input');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'from_user_id';
            input.id = 'idor_input';
            form.appendChild(input);
        }
        input.value = '1'; // Target Admin Account
        
        const msgDiv = document.getElementById('message');
        msgDiv.className = 'alert alert-primary border-0 small';
        msgDiv.textContent = 'Payload IDOR Injecté (from_user_id=1). Testez l\'envoi.';
        msgDiv.style.display = 'block';
    }

    async function runRaceCondition() {
        const raceBtn = document.getElementById('raceBtn');
        raceBtn.disabled = true;
        const amount = 10000;
        const promises = [];
        
        for (let i = 0; i < 5; i++) {
            const formData = new FormData();
            formData.append('phone', '+221770000000');
            formData.append('amount', amount);
            formData.append('description', 'Race Condition Test');
            
            promises.push(fetch('../api/transfer/send.php', {
                method: 'POST',
                body: formData
            }).then(r => r.json()));
        }

        const results = await Promise.all(promises);
        console.table(results);
        
        const successCount = results.filter(r => r.success).length;
        const msgDiv = document.getElementById('message');
        msgDiv.className = successCount > 1 ? 'alert alert-danger' : 'alert alert-info';
        msgDiv.textContent = `Race Condition : ${successCount} transaction(s) réussie(s) sur 5. (Si 1 seule, le système est ROBUSTE)`;
        msgDiv.style.display = 'block';
        raceBtn.disabled = false;
    }
</script> 
</body>
</html>
