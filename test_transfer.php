<?php
require_once __DIR__ . '/core/Transfer.php';

$transfer = new Transfer();
$res = $transfer->sendMoney("1", "2", "100", 'Test transfer', 'Orange Money');
print_r($res);
?>
