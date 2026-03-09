<?php
session_start();
header('Content-Type: application/json');
require_once '../../core/Auth.php';

$auth = new Auth();
$result = $auth->logout();

echo json_encode($result);
?>
