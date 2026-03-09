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
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$fullName = $_POST['full_name'] ?? '';

// VULNÉRABILITÉ : Pas de validation ni d'échappement des entrées
if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
    exit;
}

$auth = new Auth();
$result = $auth->register($username, $email, $password, $fullName);

echo json_encode($result);
?>
