<?php 
session_start(); 
header('Content-Type: application/json'); 
require_once __DIR__ . '/../../core/Auth.php'; 
require_once __DIR__ . '/../../core/Transfer.php'; 
$auth = new Auth(); 
if (!$auth->isLoggedIn()) { 
echo json_encode(['success' => false, 'message' => 'Non authentifié']); 
exit; 
} 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']); 
exit; 
} 
$fromUserId = $auth->getUserId(); 
$toUserId = $_POST['to_user_id'] ?? null; 
$amount = $_POST['amount'] ?? 0; 
$description = $_POST['description'] ?? ''; 
// VULNÉRABILITÉ MAJEURE : Permet de modifier from_user_id
if (isset($_POST['from_user_id'])) { 
    $fromUserId = $_POST['from_user_id'];  // Un attaquant peut débiter n'importe 
// quel compte ! 
} 
if (!$toUserId || $fromUserId == $toUserId) { 
echo json_encode(['success' => false, 'message' => 'Destinataire invalide']); 
exit; 
} 
$transfer = new Transfer(); 
$result = $transfer->sendMoney($fromUserId, $toUserId, $amount, $description); 

// Enrichir la réponse avec les frais
if ($result['success']) {
    $fee = $amount * BANK_FEE_PERCENT;
    $result['subtotal'] = $amount;
    $result['fee'] = $fee;
    $result['total'] = $amount + $fee;
    $result['method'] = $_POST['method_name'] ?? 'Virement';
}

echo json_encode($result); 
?>
