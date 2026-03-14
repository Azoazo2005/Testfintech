<?php
require_once __DIR__ . '/../../core/Auth.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/index.php');
    exit;
}

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$fullName = $_POST['full_name'] ?? '';

$auth = new Auth();
$result = $auth->register($username, $email, $password, $fullName);

if ($result['success']) {
    header('Location: ../../public/index.php?success=Account created! Please login.');
} else {
    header('Location: ../../public/index.php?error=' . urlencode($result['message']));
}
exit;
?>
