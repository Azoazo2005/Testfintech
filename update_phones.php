<?php
require __DIR__ . '/config/database.php';
$db = new Database();
$res = $db->query("SHOW COLUMNS FROM users");
$cols = [];
while ($row = mysqli_fetch_assoc($res)) {
    $cols[] = $row['Field'];
}
if (!in_array('phone', $cols)) {
    $db->query("ALTER TABLE users ADD COLUMN phone VARCHAR(20) UNIQUE NULL AFTER email");
    echo "Phone column added.\n";
} else {
    echo "Phone column already exists.\n";
}
$usersRes = $db->query("SELECT id FROM users WHERE phone IS NULL OR phone = ''");
$count = 0;
while ($u = mysqli_fetch_assoc($usersRes)) {
    $phone = '+2217' . rand(6,8) . rand(1000000, 9999999);
    $db->query("UPDATE users SET phone = '$phone' WHERE id = {$u['id']}");
    $count++;
}
echo "Updated $count users with Senegalese phone numbers.\n";
