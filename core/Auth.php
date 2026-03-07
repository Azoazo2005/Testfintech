<?php
require_once '../config/database.php'; 

class Auth { 
private $db; 

public function __construct() { 
$this->db = new Database(); 
    } 

// Version 1 - Login vulnérable à l'injection SQL 
public function login($username, $password) { 
// VULNÉRABILITÉ CRITIQUE : Pas de validation des entrées 
        $hashedPassword = md5($password);  // MD5 faible 

// VULNÉRABILITÉ : Requête SQL non préparée 
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$hashedPassword'"; 
        $result = $this->db->query($sql); 

if ($result && mysqli_num_rows($result) > 0) { 
            $user = $this->db->fetchOne($result); 
// Création de session sans sécurité 
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['username'] = $user['username']; 
            $_SESSION['is_admin'] = $user['is_admin']; 
            $_SESSION['logged_in'] = true; 
return ['success' => true, 'user' => $user]; 
        } 
return ['success' => false, 'message' => 'Identifiants incorrects']; 
    } 

// Version 1 - Inscription vulnérable 
public function register($username, $email, $password, $fullName) { 
// Validation minimale 
if (strlen($password) < PASSWORD_MIN_LENGTH) { 
return ['success' => false, 'message' => 'Mot de passe trop court']; 
        } 

// VULNÉRABILITÉ : MD5 + requête non préparée 
        $hashedPassword = md5($password); 
        $sql = "INSERT INTO users (username, email, password, full_name)  
                VALUES ('$username', '$email', '$hashedPassword', '$fullName')"; 
        $result = $this->db->query($sql); 

if ($result) { 
            $userId = mysqli_insert_id($this->db->getConnection()); 
// Création du wallet initial 
            $walletSql = "INSERT INTO wallets (user_id, balance) VALUES ($userId, 1000.00)"; 
            $this->db->query($walletSql); 
            return ['success' => true, 'user_id' => $userId];
        } 

return ['success' => false, 'message' => 'Erreur lors de l\'inscription']; 
    } 

public function isLoggedIn() { 
return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true; 
    } 

public function getUserId() { 
return $_SESSION['user_id'] ?? null; 
    } 

public function logout() { 
        session_destroy(); 
return ['success' => true]; 
    } 
} 
?>