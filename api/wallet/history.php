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

// VULNÉRABILITÉ IDOR : Accepte user_id en paramètre
$userId = $_GET['user_id'] ?? $auth->getUserId();

$wallet = new Wallet();
$history = $wallet->getTransactionHistory($userId);

echo json_encode(['success' => true, 'data' => $history]);
?>
