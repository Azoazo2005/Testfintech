<?php
require_once __DIR__ . '/../config/database.php'; 
require_once __DIR__ . '/../config/constants.php'; 

class Wallet { 
private $db; 

public function __construct() { 
$this->db = new Database(); 
    } 

// Version 1 - VULNÉRABILITÉ IDOR 
public function getBalance($userId) { 
        $sql = "SELECT a.*, u.username, u.full_name  
                FROM accounts a  
                JOIN users u ON a.user_id = u.id  
                WHERE a.user_id = $userId"; 
        $result = $this->db->query($sql); 
if ($result && mysqli_num_rows($result) > 0) { 
return $this->db->fetchOne($result); 
        } 
return null; 
    } 

// Version 1 - VULNÉRABILITÉ IDOR sur l'historique 
public function getTransactionHistory($userId, $limit = 20) { 
        $sql = "SELECT t.*,  
                       u1.username as from_username,  
                       u2.username as to_username 
                FROM " . TABLE_TRANSACTIONS . " t 
                LEFT JOIN users u1 ON t.from_user_id = u1.id 
                LEFT JOIN users u2 ON t.to_user_id = u2.id 
                WHERE t.from_user_id = $userId OR t.to_user_id = $userId 
                ORDER BY t.created_at DESC 
                LIMIT $limit"; 
        $result = $this->db->query($sql); 
return $this->db->fetchAll($result); 
    } 

public function updateBalance($userId, $newBalance) { 
        $sql = "UPDATE accounts SET balance = $newBalance WHERE user_id = $userId"; 
return $this->db->query($sql); 
    } 
} 
?>
