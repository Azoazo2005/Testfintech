<?php
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Version 1 - Login vulnérable à l'injection SQL
    public function login($username, $password) {
        // VULNÉRABILITÉ CRITIQUE : Pas de validation des entrées
        $hashedPassword = $password;  // Plain text for educational purposes
        
        // VULNÉRABILITÉ : Requête SQL non préparée
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$hashedPassword'";
        $result = $this->db->query($sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = $this->db->fetchOne($result);
            
            // Création de session sans sécurité
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
            // Map role for premium UI compatibility
            $_SESSION['role'] = (isset($user['role']) && $user['role'] === 'admin') || (isset($user['is_admin']) && $user['is_admin'] == 1) ? 'admin' : 'user';
            $_SESSION['logged_in'] = true;
            
            return ['success' => true, 'user' => $user];
        }
        return ['success' => false, 'message' => 'Identifiants incorrects'];
    }

    // Version 1 - Inscription vulnérable
    public function register($username, $email, $password, $fullName, $phone = null) {
        // Validation minimale
        if (strlen($password) < (defined('PASSWORD_MIN_LENGTH') ? PASSWORD_MIN_LENGTH : 3)) {
            return ['success' => false, 'message' => 'Mot de passe trop court'];
        }

        // VÉRIFICATION : L'utilisateur existe-t-il déjà ?
        $checkSql = "SELECT id FROM users WHERE username = '$username' OR email = '$email' OR phone = '$phone'";
        $checkResult = $this->db->query($checkSql);
        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            return ['success' => false, 'message' => 'L\'utilisateur, l\'email ou le numéro de téléphone existe déjà.'];
        }
        
        // VULNÉRABILITÉ : MD5 + requête non préparée
        $hashedPassword = $password; // Plain text for educational purposes
        $sql = "INSERT INTO users (username, email, phone, password, full_name, role, is_admin) 
                VALUES ('$username', '$email', '$phone', '$hashedPassword', '$fullName', 'user', 0)";
        $result = $this->db->query($sql);
        
        if ($result) {
            $userId = mysqli_insert_id($this->db->getConnection());
            
            // Création du wallet initial
            $walletSql = "INSERT INTO wallets (user_id, balance) VALUES ($userId, 1000.00)";
            $this->db->query($walletSql);
            
            // Également dans accounts pour compatibilité
            $accSql = "INSERT INTO accounts (user_id, account_number, balance) 
                       VALUES ($userId, CONCAT('FR76', LPAD($userId, 6, '0')), 1000.00)";
            $this->db->query($accSql);

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
