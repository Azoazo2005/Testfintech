<?php 
session_start(); 
ob_start();
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
// SECURITY: Only use the session user ID, never from POST
$fromUserId = $auth->getUserId(); 
$phone = trim($_POST['phone'] ?? ''); 
$amount = $_POST['amount'] ?? 0; 
$description = $_POST['description'] ?? ''; 

error_log("TRANSFER REQUEST: phone=$phone, amount=$amount, from=$fromUserId");

if (!$phone) { 
    echo json_encode(['success' => false, 'message' => 'Numéro de destinataire invalide']); 
    exit; 
} 

require_once __DIR__ . '/../../config/database.php';
$db = new Database();
$stmt = $db->prepare("SELECT id, full_name, username FROM users WHERE phone = ?");
$res = $db->execute($stmt, [$phone], "s");
$recipient = $db->fetchOne($res);

if (!$recipient) {
    echo json_encode(['success' => false, 'message' => 'Seuls les numéros déjà inscrits peuvent recevoir de l\'argent']);
    exit;
}

$toUserId = $recipient['id'];

if ($fromUserId == $toUserId) { 
    echo json_encode(['success' => false, 'message' => 'Vous ne pouvez pas envoyer de l\'argent à vous-même']); 
    exit; 
} 
$method = $_POST['method_name'] ?? 'Transfer';
$transfer = new Transfer(); 
$result = $transfer->sendMoney($fromUserId, $toUserId, $amount, $description, $method); 
if (!$result['success'] && $result['message'] === 'Destinataire introuvable') {
    $result['message'] .= " (Debugger info: to=$toUserId, amount=$amount, from=$fromUserId, type_to=" . gettype($toUserId) . ", type_amount=" . gettype($amount) . ")";
}


// Enrichir la réponse avec les frais (Si non déjà fait par sendMoney)
if ($result['success'] && !isset($result['subtotal'])) {
    $fee = $amount * BANK_FEE_PERCENT;
    $result['subtotal'] = $amount;
    $result['fee'] = $fee;
    $result['total'] = $amount + $fee;
    $result['method'] = $_POST['method_name'] ?? 'Virement';
}

if ($result['success'] && isset($recipient)) {
    $result['recipient_name'] = $recipient['full_name'] . ' (' . $recipient['username'] . ')';
}

ob_clean();
echo json_encode($result); 
