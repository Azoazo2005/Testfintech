<?php
require_once __DIR__ . '/../config/database.php';

class Auth {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Secure Login
    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $result = $this->db->execute($stmt, [$username], "s");
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = $this->db->fetchOne($result);
            
            // SECURITY: Verify hashed password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
                $_SESSION['role'] = (isset($user['role']) && $user['role'] === 'admin') || (isset($user['is_admin']) && $user['is_admin'] == 1) ? 'admin' : 'user';
                $_SESSION['logged_in'] = true;
                
                return ['success' => true, 'user' => $user];
            }
        }
        
        // Audit log for failed login (Optional but recommended)
        $this->logEvent(null, "FAILED_LOGIN", "Failed login attempt for username: $username");
        
        return ['success' => false, 'message' => 'Identifiants incorrects'];
    }

    // Secure Registration
    public function register($username, $email, $password, $fullName) {
        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Mot de passe trop court (min 8 caractères)'];
        }
        
        // SECURITY: Hash password with Bcrypt
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO users (username, email, password, full_name, role, is_admin) VALUES (?, ?, ?, ?, 'user', 0)";
        $stmt = $this->db->prepare($sql);
        $result = $this->db->execute($stmt, [$username, $email, $hashedPassword, $fullName], "ssss");
        
        if ($result) {
            $userId = mysqli_insert_id($this->db->getConnection());
            
            // Create initial accounts
            $this->db->query("INSERT INTO wallets (user_id, balance) VALUES ($userId, 1000.00)");
            $accNum = 'FR76' . str_pad($userId, 6, '0', STR_PAD_LEFT);
            $this->db->query("INSERT INTO accounts (user_id, account_number, balance) VALUES ($userId, '$accNum', 1000.00)");

            $this->logEvent($userId, "ACCOUNT_CREATED", "New account registered: $username");

            return ['success' => true, 'user_id' => $userId];
        }
        return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
    }

    public function logEvent($userId, $type, $desc) {
        $sql = "INSERT INTO audit_logs (user_id, event_type, description) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $this->db->execute($stmt, [$userId, $type, $desc], "iss");
    }

    public function logAdminAction($adminId, $action, $details) {
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) return;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $sql = "INSERT INTO admin_logs (admin_id, action, details, ip_address) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $this->db->execute($stmt, [$adminId, $action, $details, $ip], "isss");
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
