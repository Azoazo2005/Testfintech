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

try {
$fromUserId = $auth->getUserId(); 
$toPhone = $_POST['to_phone'] ?? null; 
$amount = $_POST['amount'] ?? 0; 
$description = $_POST['description'] ?? ''; 

// Résoudre le numéro de téléphone en ID utilisateur
$toUserId = null;
$toUsername = '';
$toFullName = '';
if ($toPhone) {
    $db = new Database();
    $userResult = $db->query("SELECT id, username, full_name FROM users WHERE phone = '$toPhone'");
    $userData = $db->fetchOne($userResult);
    if ($userData) {
        $toUserId = $userData['id'];
        $toUsername = $userData['username'];
        $toFullName = $userData['full_name'];
    }
}

// VULNÉRABILITÉ MAJEURE : Permet de modifier from_user_id
if (isset($_POST['from_user_id'])) { 
    $fromUserId = $_POST['from_user_id'];  
} 

if (!$toUserId || $fromUserId == $toUserId) { 
    echo json_encode(['success' => false, 'message' => 'Destinataire introuvable ou invalide']); 
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
    $result['to_username'] = $toUsername;
    $result['to_full_name'] = $toFullName;
}

    echo json_encode($result); 

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur système : ' . $e->getMessage()]);
} catch (Error $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur fatale : ' . $e->getMessage()]);
}
?>
