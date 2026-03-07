<?php
session_start();
header('Content-Type: application/json');

require_once '../../core/Auth.php';
require_once '../../core/Transfer.php';

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

// VULNÉRABILITÉ MAJEURE : Permet de modifier l'expéditeur via une requête POST
if (isset($_POST['from_user_id'])) {
    $fromUserId = $_POST['from_user_id']; // Un attaquant peut débiter n'importe quel compte ! [cite: 462]
}

if (!$toUserId || $fromUserId == $toUserId) {
    echo json_encode(['success' => false, 'message' => 'Destinataire invalide']);
    exit;
}

$transfer = new Transfer();
$result = $transfer->sendMoney($fromUserId, $toUserId, $amount, $description);

echo json_encode($result);
?>