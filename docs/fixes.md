# Corrections V2 - Guide de Sécurisation

## 1. Injection SQL → Requêtes préparées

**Avant (V1) :**
```php
$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$hashedPassword'";
$result = $this->db->query($sql);
```

**Après (V2) :**
```php
$stmt = $this->db->getConnection()->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $hashedPassword);
$stmt->execute();
$result = $stmt->get_result();
```

---

## 2. MD5 → password_hash() / password_verify()

**Avant (V1) :**
```php
$hashedPassword = md5($password);
```

**Après (V2) :**
```php
// Inscription
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Connexion
if (password_verify($password, $user['password'])) {
    // Connexion réussie
}
```

---

## 3. IDOR → Vérification d'autorisation

**Avant (V1) :**
```php
$userId = $_GET['user_id'] ?? $auth->getUserId();
```

**Après (V2) :**
```php
// Toujours utiliser l'ID de session, jamais un paramètre client
$userId = $auth->getUserId();
```

---

## 4. Manipulation from_user_id → Ignorer le paramètre client

**Avant (V1) :**
```php
if (isset($_POST['from_user_id'])) {
    $fromUserId = $_POST['from_user_id'];
}
```

**Après (V2) :**
```php
// Toujours utiliser l'ID de session
$fromUserId = $auth->getUserId();
// Ignorer complètement $_POST['from_user_id']
```

---

## 5. Race Condition → Transactions SQL

**Avant (V1) :**
```php
$senderWallet = $this->wallet->getBalance($fromUserId);
// ... mises à jour séparées
```

**Après (V2) :**
```php
$conn = $this->db->getConnection();
mysqli_begin_transaction($conn);

try {
    // Verrouillage de la ligne avec SELECT ... FOR UPDATE
    $sql = "SELECT balance FROM wallets WHERE user_id = ? FOR UPDATE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $fromUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    $sender = $result->fetch_assoc();

    if ($sender['balance'] < $amount) {
        mysqli_rollback($conn);
        return ['success' => false, 'message' => 'Solde insuffisant'];
    }

    // Mises à jour atomiques
    $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE user_id = ?")->execute([$amount, $fromUserId]);
    $conn->prepare("UPDATE wallets SET balance = balance + ? WHERE user_id = ?")->execute([$amount, $toUserId]);

    mysqli_commit($conn);
} catch (Exception $e) {
    mysqli_rollback($conn);
    return ['success' => false, 'message' => 'Erreur lors du transfert'];
}
```

---

## 6. XSS → Échappement HTML

**Avant (V1) :**
```php
echo $transaction['description'];
```

**Après (V2) :**
```php
echo htmlspecialchars($transaction['description'], ENT_QUOTES, 'UTF-8');
```

---

## 7. Configuration sécurisée

**Avant (V1) :**
```php
define('DEBUG_MODE', true);
define('PASSWORD_MIN_LENGTH', 3);
define('ENABLE_LOGGING', false);
```

**Après (V2) :**
```php
define('DEBUG_MODE', false);
define('PASSWORD_MIN_LENGTH', 12);
define('ENABLE_LOGGING', true);
```

---

## 8. Protection CSRF

**Ajout V2 :**
```php
// Générer un token CSRF
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Dans le formulaire
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// Vérification côté serveur
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die('Token CSRF invalide');
}
```
