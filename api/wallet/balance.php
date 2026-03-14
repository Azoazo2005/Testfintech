<?php 
session_start(); 
header('Content-Type: application/json'); 
require_once __DIR__ . '/../../core/Auth.php'; 
require_once __DIR__ . '/../../core/Wallet.php'; 
$auth = new Auth(); 
if (!$auth->isLoggedIn()) { 
echo json_encode(['success' => false, 'message' => 'Non authentifié']); 
exit; 
} 
// VULNÉRABILITÉ CRITIQUE : Accepte user_id en paramètre 
$userId = $_GET['user_id'] ?? $auth->getUserId(); 
$wallet = new Wallet(); 
$balance = $wallet->getBalance($userId); 
if ($balance) { 
echo json_encode(['success' => true, 'data' => $balance]); 
} else { 
echo json_encode(['success' => false, 'message' => 'Wallet non trouvé']); 
} 
?>
