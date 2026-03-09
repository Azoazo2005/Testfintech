<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/constants.php';
require_once '../../core/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$auth = new Auth();
$result = $auth->login($username, $password);

echo json_encode($result);
?>
