<?php
require_once __DIR__ . '/../config/database.php'; 
require_once __DIR__ . '/../config/constants.php'; 

class Wallet { 
private $db; 

public function __construct() { 
$this->db = new Database(); 
    } 

    // Secure version with prepared statements
    public function getBalance($userId) { 
        $sql = "SELECT a.*, u.username, u.full_name  
                FROM accounts a  
                JOIN users u ON a.user_id = u.id  
                WHERE a.user_id = ?"; 
        $stmt = $this->db->prepare($sql);
        $result = $this->db->execute($stmt, [$userId], "i");
        
        if ($result && mysqli_num_rows($result) > 0) { 
            return $this->db->fetchOne($result); 
        } 
        return null; 
    } 

    // Secure version with prepared statements
    public function getTransactionHistory($userId, $limit = 20) { 
        $sql = "SELECT t.*,  
                       u1.username as from_username,  
                       u2.username as to_username 
                FROM " . TABLE_TRANSACTIONS . " t 
                LEFT JOIN users u1 ON t.from_user_id = u1.id 
                LEFT JOIN users u2 ON t.to_user_id = u2.id 
                WHERE t.from_user_id = ? OR t.to_user_id = ? 
                ORDER BY t.created_at DESC 
                LIMIT ?"; 
        $stmt = $this->db->prepare($sql);
        $result = $this->db->execute($stmt, [$userId, $userId, $limit], "iii");
        return $this->db->fetchAll($result); 
    } 

    public function updateBalance($userId, $newBalance) { 
        $sql = "UPDATE accounts SET balance = ? WHERE user_id = ?"; 
        $stmt = $this->db->prepare($sql);
        return $this->db->execute($stmt, [$newBalance, $userId], "di");
    } 
} 
