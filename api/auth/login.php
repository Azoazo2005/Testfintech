<?php 
require_once __DIR__ . '/../../core/Auth.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/index.php');
    exit;
}

$username = $_POST['username'] ?? ''; 
$password = $_POST['password'] ?? ''; 

$auth = new Auth(); 
$result = $auth->login($username, $password); 

if ($result['success']) {
    header('Location: ../../public/dashboard.php');
} else {
    header('Location: ../../public/index.php?error=' . urlencode($result['message']));
}
exit;
?>
