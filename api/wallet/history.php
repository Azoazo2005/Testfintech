<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/constants.php';
require_once '../../core/Auth.php';
require_once '../../core/Wallet.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

// VULNÉRABILITÉ IDOR : Accepte user_id en paramètre
$userId = $_GET['user_id'] ?? $auth->getUserId();
$limit = $_GET['limit'] ?? 20;

$wallet = new Wallet();
$history = $wallet->getTransactionHistory($userId, $limit);

echo json_encode(['success' => true, 'data' => $history]);
?>
