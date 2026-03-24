<?php
require __DIR__ . '/config/database.php';
$db = new Database();
$res = $db->query('SELECT username, full_name, phone FROM users');
while($row = mysqli_fetch_assoc($res)) {
    echo "- " . $row['full_name'] . " (" . $row['username'] . "): " . $row['phone'] . "\n";
}
