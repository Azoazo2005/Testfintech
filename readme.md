fintech_App_edu.md
2026-03-02
Guide Complet - Projet Fintech Éducatif : Vulnérabilités
et Sécurisation
Vue d'ensemble du projet
Ce projet vise à créer une application fintech éducative en deux versions pour démontrer concrètement
l'impact des vulnérabilités de sécurité sur un système financier. L'objectif pédagogique est de permettre aux
étudiants de comprendre comment les erreurs de conception peuvent être exploitées et comment les corriger
efficacement.
Principe fondamental : Développer d'abord une version V1 intentionnellement vulnérable, puis une version
V2 entièrement sécurisée. Cette approche permet de visualiser concrètement l'évolution des risques et des
coûts associés aux failles de sécurité.
Architecture Technique du Projet
Structure des dossiers recommandée :
fintech-vulnerable/ 
├── config/ 
│   ├── database.php          # Connexion base de données 
│   └── constants.php         # Constantes globales 
├── core/ 
│   ├── Auth.php              # Gestion authentification 
│   ├── Wallet.php            # Logique portefeuille 
│   ├── Transfer.php          # Logique transferts 
│   ├── Security.php          # Fonctions sécurité (V2) 
│   └── Logger.php            # Système de logs 
├── api/ 
│   ├── auth/ 
│   │   ├── login.php 
│   │   ├── register.php 
│   │   └── logout.php 
│   ├── wallet/ 
│   │   ├── balance.php 
│   │   └── history.php 
│   └── transfer/ 
│       └── send.php 
├── public/ 
│   ├── index.php             # Page connexion 
│   ├── dashboard.php         # Tableau de bord 
│   ├── transfer.php          # Interface transfert 
│   └── assets/ 
│       ├── css/style.css 
│       └── js/app.js 
├── sql/ 
│   ├── schema.sql            # Structure base 
│   └── seed.sql              # Données de test 
1 / 23
fintech_App_edu.md
├── docs/ 
│   ├── vulnerabilities.md    # Documentation failles V1 
│   ├── exploitation.md       # Guide d'exploitation 
│   └── fixes.md              # Corrections V2 
└── logs/ 
    └── transactions.log 
Technologies imposées :
PHP (sans framework pour la simplicité)
MySQL/MariaDB avec mysqli (pas PDO pour faciliter les injections SQL en V1)
JavaScript vanilla (pas de frameworks)
GitHub pour le versioning
Workflow Git Obligatoire
Structure des branches :
Branche
main
develop
Description
Version stable finale
Branche d'intégration
Utilisation
Merge uniquement depuis release/*
Merge des features terminées
feature/nom-etudiant Développement individuel Une par étudiant
release/v1
release/v2
Version vulnérable stable
Version sécurisée stable
Processus de travail pour chaque étudiant :
Pour la démonstration des failles
Pour la démonstration des corrections
1. Créer sa branche : git checkout -b feature/prenom
2. Développer son module en commits réguliers
3. Pousser et créer une Pull Request vers develop
4. Le Lead Projet revoit et merge après validation
5. Répéter le processus pour la V2
ÉTUDIANT 1 - Lead Projet & Infrastructure
Responsabilités principales :
2026-03-02
Coordination générale du projet, mise en place de l'infrastructure de base, et intégration des modules
développés par les autres étudiants.
Tâches détaillées V1 :
Configuration initiale :
2 / 23
Créer le fichier config/constants.php :
fintech_App_edu.md
2026-03-02
<?php
// Version 1 - Configuration volontairement faible 
define('APP_NAME', 'FinTech Demo'); 
define('APP_VERSION', '1.0'); 
define('DEBUG_MODE', true);  // Affiche les erreurs SQL 
// Sécurité volontairement faible 
define('PASSWORD_MIN_LENGTH', 3); 
define('SESSION_TIMEOUT', 86400); 
define('ENABLE_LOGGING', false);  // Pas de logs en V1 
// Limites de transaction 
define('MAX_TRANSFER_AMOUNT', 999999); 
define('MIN_TRANSFER_AMOUNT', 0.01); 
?>
Créer le fichier config/database.php :
<?php
class Database { 
private $host = 'localhost'; 
private $username = 'root'; 
private $password = ''; 
private $database = 'fintech_demo'; 
private $connection; 
public function __construct() { 
$this->connect(); 
    } 
// Version 1 - Connexion sans gestion d'erreur sécurisée 
private function connect() { 
$this->connection = mysqli_connect( 
$this->host, $this->username, $this->password, $this->database 
        ); 
if (!$this->connection) { 
// VULNÉRABILITÉ : Affichage des erreurs de connexion 
die("Erreur de connexion: " . mysqli_connect_error()); 
        } 
        mysqli_set_charset($this->connection, "utf8mb4"); 
    } 
public function getConnection() { 
return $this->connection; 
    } 
// Version 1 - Query sans protection 
public function query($sql) { 
        $result = mysqli_query($this->connection, $sql); 
3 / 23
fintech_App_edu.md
if (!$result && DEBUG_MODE) { 
// VULNÉRABILITÉ : Affichage des erreurs SQL 
echo "Erreur SQL: " . mysqli_error($this->connection); 
        } 
return $result; 
    } 
public function fetchAll($result) { 
        $rows = []; 
while ($row = mysqli_fetch_assoc($result)) { 
            $rows[] = $row; 
        } 
return $rows; 
    } 
public function fetchOne($result) { 
return mysqli_fetch_assoc($result); 
    } 
} 
?>
Tâches V2 :
2026-03-02
Sécuriser la configuration, masquer les erreurs en production, et implémenter un système de logging robuste.
ÉTUDIANT 2 - Base de Données
Responsabilités principales :
Conception et implémentation de la structure de données, création des données de test permettant de
démontrer les vulnérabilités.
Tâches détaillées V1 :
Fichier sql/schema.sql :
CREATE DATABASE IF NOT EXISTS fintech_demo CHARACTER SET utf8mb4 COLLATE 
utf8mb4_unicode_ci; 
USE fintech_demo; -- Table des utilisateurs
CREATE TABLE users ( 
id INT AUTO_INCREMENT PRIMARY KEY, 
    username VARCHAR(50) NOT NULL UNIQUE, 
    email VARCHAR(100) NOT NULL UNIQUE, 
password VARCHAR(255) NOT NULL,  -- Stockage en MD5 pour V1 
    full_name VARCHAR(100) NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    is_admin BOOLEAN DEFAULT FALSE, 
4 / 23
fintech_App_edu.md
INDEX idx_username (username) 
) ENGINE=InnoDB; 
2026-03-02-- Table des comptes (wallets)
CREATE TABLE wallets ( 
id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL, 
    balance DECIMAL(15, 2) DEFAULT 0.00, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE 
) ENGINE=InnoDB; -- Table des transactions
CREATE TABLE transactions ( 
id INT AUTO_INCREMENT PRIMARY KEY, 
    from_user_id INT NOT NULL, 
    to_user_id INT NOT NULL, 
    amount DECIMAL(15, 2) NOT NULL, 
    description TEXT, 
status ENUM('pending', 'completed', 'failed') DEFAULT 'pending', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
FOREIGN KEY (from_user_id) REFERENCES users(id), 
FOREIGN KEY (to_user_id) REFERENCES users(id) 
) ENGINE=InnoDB; 
Fichier sql/seed.sql :
USE fintech_demo; -- Utilisateurs de test (mots de passe en MD5 - VULNÉRABLE)
INSERT INTO users (username, email, password, full_name, is_admin) VALUES 
('admin', 'admin@fintech.com', MD5('admin123'), 'Administrateur', TRUE), 
('alice', 'alice@example.com', MD5('password123'), 'Alice Martin', FALSE), 
('bob', 'bob@example.com', MD5('password123'), 'Bob Dupont', FALSE), 
('victim', 'victim@example.com', MD5('victim123'), 'Victime Riche', FALSE); -- Portefeuilles avec soldes différents
INSERT INTO wallets (user_id, balance) VALUES 
(1, 100000.00),  -- Admin avec beaucoup d'argent 
(2, 5000.00),    -- Alice 
(3, 2000.00),    
(4, 15000.00);   -- Bob   -- Victime avec gros solde -- Quelques transactions historiques
INSERT INTO transactions (from_user_id, to_user_id, amount, description, status) 
VALUES 
(2, 3, 100.00, 'Remboursement restaurant', 'completed'), 
(4, 2, 500.00, 'Cadeau anniversaire', 'completed'); 
5 / 23
fintech_App_edu.md
2026-03-02
Tâches V2 :
Modifier le schéma pour supporter le hachage sécurisé des mots de passe, ajouter des contraintes de sécurité
et des index pour les performances.
ÉTUDIANT 3 - Authentification
Responsabilités principales :
Créer un système d'authentification fonctionnel mais volontairement vulnérable aux injections SQL et autres
attaques.
Tâches détaillées V1 :
Fichier core/Auth.php :
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
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = 
'$hashedPassword'"; 
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
6 / 23
    } 
fintech_App_edu.md
2026-03-02
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
            $walletSql = "INSERT INTO wallets (user_id, balance) VALUES ($userId, 
1000.00)"; 
        } 
$this->db->query($walletSql); 
return ['success' => true, 'user_id' => $userId]; 
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
Fichier api/auth/login.php :
<?php 
session_start(); 
header('Content-Type: application/json'); 
require_once '../../core/Auth.php'; 
7 / 23
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
fintech_App_edu.md
2026-03-02
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']); 
exit; 
} 
$username = $_POST['username'] ?? ''; 
$password = $_POST['password'] ?? ''; 
$auth = new Auth(); 
$result = $auth->login($username, $password); 
echo json_encode($result); 
?>
Vulnérabilités à documenter :
Injection SQL possible avec : admin' OR '1'='1' -
Mots de passe en MD5 (facilement cassables)
Absence de protection contre le brute force
Sessions non sécurisées
Tâches V2 :
Implémenter les requêtes préparées, utiliser password_hash() et password_verify(), ajouter la protection
CSRF et la limitation des tentatives de connexion.
ÉTUDIANT 4 - Wallet (Portefeuille)
Responsabilités principales :
Développer les fonctionnalités d'affichage du solde et de l'historique des transactions avec des vulnérabilités
IDOR (Insecure Direct Object Reference).
Tâches détaillées V1 :
Fichier core/Wallet.php :
<?php
require_once '../config/database.php'; 
class Wallet { 
private $db; 
public function __construct() { 
$this->db = new Database(); 
    } 
// Version 1 - VULNÉRABILITÉ IDOR 
public function getBalance($userId) { 
// PAS de vérification si l'utilisateur connecté peut accéder à ce wallet 
        $sql = "SELECT w.*, u.username, u.full_name  
8 / 23
fintech_App_edu.md
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
                FROM transactions t 
                JOIN users u1 ON t.from_user_id = u1.id 
                JOIN users u2 ON t.to_user_id = u2.id 
                WHERE t.from_user_id = $userId OR t.to_user_id = $userId 
                ORDER BY t.created_at DESC 
                LIMIT $limit"; 
        $result = $this->db->query($sql); 
return $this->db->fetchAll($result); 
    } 
2026-03-02
                FROM wallets w  
                JOIN users u ON w.user_id = u.id  
                WHERE w.user_id = $userId"; 
public function updateBalance($userId, $newBalance) { 
        $sql = "UPDATE wallets SET balance = $newBalance WHERE user_id = $userId"; 
return $this->db->query($sql); 
    } 
} 
?>
Fichier api/wallet/balance.php :
<?php 
session_start(); 
header('Content-Type: application/json'); 
require_once '../../core/Auth.php'; 
require_once '../../core/Wallet.php'; 
$auth = new Auth(); 
if (!$auth->isLoggedIn()) { 
echo json_encode(['success' => false, 'message' => 'Non authentifié']); 
exit; 
} 
9 / 23
fintech_App_edu.md
// VULNÉRABILITÉ CRITIQUE : Accepte user_id en paramètre 
$userId = $_GET['user_id'] ?? $auth->getUserId(); 
$wallet = new Wallet(); 
$balance = $wallet->getBalance($userId); 
if ($balance) { 
echo json_encode(['success' => true, 'data' => $balance]); 
} else { 
echo json_encode(['success' => false, 'message' => 'Wallet non trouvé']); 
} 
?>
Démonstration de la vulnérabilité :
Un utilisateur connecté peut consulter le solde de n'importe qui en modifiant l'URL :
/api/wallet/balance.php?user_id=4
Tâches V2 :
2026-03-02
Supprimer le paramètre user_id des URLs et utiliser uniquement l'ID de session. Ajouter des vérifications
d'autorisation strictes.
ÉTUDIANT 5 - Transferts
Responsabilités principales :
Implémenter la fonctionnalité critique de transfert d'argent avec plusieurs vulnérabilités majeures : race
conditions, montants négatifs, et manipulation des comptes source.
Tâches détaillées V1 :
Fichier core/Transfer.php :
<?php
require_once '../config/database.php'; 
require_once '../core/Wallet.php'; 
class Transfer { 
private $db; 
private $wallet; 
public function __construct() { 
$this->db = new Database(); 
$this->wallet = new Wallet(); 
    } 
// Version 1 - Transfert avec multiples vulnérabilités 
public function sendMoney($fromUserId, $toUserId, $amount, $description = '') 
{ 
10 / 23
// VULNÉRABILITÉ 1 : Pas de validation du montant (peut être négatif) 
fintech_App_edu.md
if ($amount == 0) { 
return ['success' => false, 'message' => 'Montant invalide']; 
        } 
2026-03-02
// VULNÉRABILITÉ 2 : Vérification de solde non atomique (race condition) 
        $senderWallet = $this->wallet->getBalance($fromUserId); 
if ($senderWallet && $senderWallet['balance'] < $amount) { 
return ['success' => false, 'message' => 'Solde insuffisant']; 
        } 
// VULNÉRABILITÉ 3 : Pas de transaction SQL (atomicité) 
// Si le processus s'interrompt entre ces requêtes, l'argent disparaît ou 
se duplique 
        $newSenderBalance = $senderWallet['balance'] - $amount; 
        $sql1 = "UPDATE wallets SET balance = $newSenderBalance WHERE user_id = 
$fromUserId"; 
$this->db->query($sql1); 
        $receiverWallet = $this->wallet->getBalance($toUserId); 
        $newReceiverBalance = $receiverWallet['balance'] + $amount; 
        $sql2 = "UPDATE wallets SET balance = $newReceiverBalance WHERE user_id = 
$toUserId"; 
$this->db->query($sql2); 
// Enregistrement de la transaction 
        $sql3 = "INSERT INTO transactions (from_user_id, to_user_id, amount, 
description, status)  
                 VALUES ($fromUserId, $toUserId, $amount, '$description', 
'completed')"; 
        $result = $this->db->query($sql3); 
if ($result) { 
return [ 
'success' => true, 
'transaction_id' => mysqli_insert_id($this->db->getConnection()), 
'message' => 'Transfert effectué' 
            ]; 
        } 
return ['success' => false, 'message' => 'Erreur lors du transfert']; 
    } 
} 
?>
Fichier api/transfer/send.php :
<?php 
session_start(); 
header('Content-Type: application/json'); 
11 / 23
fintech_App_edu.md
require_once '../../core/Auth.php'; 
require_once '../../core/Transfer.php'; 
$auth = new Auth(); 
if (!$auth->isLoggedIn()) { 
echo json_encode(['success' => false, 'message' => 'Non authentifié']); 
exit; 
} 
2026-03-02
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']); 
exit; 
} 
$fromUserId = $auth->getUserId(); 
$toUserId = $_POST['to_user_id'] ?? null; 
$amount = $_POST['amount'] ?? 0; 
$description = $_POST['description'] ?? ''; 
// VULNÉRABILITÉ MAJEURE : Permet de modifier from_user_id
if (isset($_POST['from_user_id'])) { 
    $fromUserId = $_POST['from_user_id'];  // Un attaquant peut débiter n'importe 
quel compte ! 
} 
if (!$toUserId || $fromUserId == $toUserId) { 
echo json_encode(['success' => false, 'message' => 'Destinataire invalide']); 
exit; 
} 
$transfer = new Transfer(); 
$result = $transfer->sendMoney($fromUserId, $toUserId, $amount, $description); 
echo json_encode($result); 
?>
Vulnérabilités critiques à démontrer :
1. Montant négatif : Envoyer -100 euros pour recevoir 100 euros
2. Manipulation du compte source : Modifier from_user_id pour débiter le compte de quelqu'un
d'autre
3. Race condition : Envoyer plusieurs transferts simultanés pour dépasser le solde
4. Absence de CSRF : Forcer des transferts depuis un site externe
Script d'exploitation pour race condition :
// À exécuter dans la console du navigateur
const amount = 1000; // Solde complet
const promises = []; 
12 / 23
fintech_App_edu.md
for (let i = 0; i < 5; i++) { 
const formData = new FormData(); 
    formData.append('to_user_id', '2'); 
    formData.append('amount', amount); 
    promises.push(fetch('../api/transfer/send.php', { 
method: 'POST', 
body: formData 
    })); 
} 
Promise.all(promises).then(results => { 
console.log('Transferts simultanés envoyés:', results.length); 
}); 
Tâches V2 :
2026-03-02
Implémenter les transactions SQL avec BEGIN, COMMIT, ROLLBACK. Valider strictement les montants. Supprimer
la possibilité de modifier from_user_id. Ajouter la protection CSRF.
ÉTUDIANT 6 - Interface Utilisateur & Documentation Sécurité
Responsabilités principales :
Créer une interface utilisateur fonctionnelle et documenter toutes les vulnérabilités avec des guides
d'exploitation pratiques.
Tâches détaillées V1 :
Fichier public/index.php :
<?php 
session_start(); 
require_once '../core/Auth.php'; 
$auth = new Auth(); 
if ($auth->isLoggedIn()) { 
    header('Location: dashboard.php'); 
exit; 
} 
?> 
<!DOCTYPE html> 
<html lang="fr"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>FinTech Demo - Connexion</title> 
    <link 
href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
rel="stylesheet"> 
    <link rel="stylesheet" href="assets/css/style.css"> 
13 / 23
fintech_App_edu.md
</head> 
<body class="bg-gradient-primary"> 
    <div class="container"> 
        <div class="row justify-content-center"> 
            <div 
class="col-xl-6 col-lg-8 col-md-9"> 
                < div 
class="card o-hidden border-0 shadow-lg my-5"> 
                    < div 
class="card-body p-5"> 
                        < div 
class="text-center"> 
                            < h1 
2026-03-02
class="h4 text-gray-900 mb-4">FinTech Demo</h1> 
                            < p 
sécurité</p> 
                        </ div> 
                        < div 
class="text-muted">Plateforme de démonstration de 
id="message" class="alert" style="display: none;">
</div> 
control-user"  
                        < form 
id="loginForm" class="user"> 
                            < div 
class="form-group mb-3"> 
                                < input 
type="text" class="form-control form
id="username" name="username" 
placeholder="Nom d'utilisateur" required> 
                            </ div> 
                            < div 
class="form-group mb-3"> 
                                < input 
type="password" class="form-control form
control-user"  
placeholder="Mot de passe" required> 
                            </ div> 
                            < button 
btn-block"> 
id="password" name="password" 
type="submit" class="btn btn-primary btn-user 
Se connecter 
                            </ button> 
                        </ form> 
                        < div 
class="text-center mt-4"> 
                            < a 
href="register.php">Créer un compte</a> 
                        </ div> 
                        < div 
class="card mt-4"> 
                            < div 
class="card-header"> 
                                < strong>Comptes 
                            </ div> 
                            < div 
de test</strong> 
class="card-body"> 
                                < p><code>alice
 / 
                                < p><code>victim
                                < p><code>admin
                                < hr> 
                                < p><strong>Test 
                                < p><code>admin' 
                            </ div> 
                        </ div> 
                    </div> 
                </div> 
14 / 23
 / 
password123</code> (5000€)</p> 
 / 
victim123</code> (15000€)</p> 
admin123</code> (Admin)</p> 
d'injection SQL :</strong></p> 
OR '1'='1' --</code></p> 
fintech_App_edu.md
            </div> 
        </div> 
    </div> 
    <script src="assets/js/app.js"></script> 
</body> 
</html> 
Fichier public/dashboard.php :
<?php 
session_start(); 
require_once '../core/Auth.php'; 
require_once '../core/Wallet.php'; 
$auth = new Auth(); 
if (!$auth->isLoggedIn()) { 
    header('Location: index.php'); 
exit; 
} 
$userId = $auth->getUserId(); 
$wallet = new Wallet(); 
$balance = $wallet->getBalance($userId); 
$history = $wallet->getTransactionHistory($userId, 10); 
?> 
<!DOCTYPE html> 
<html lang="fr"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Dashboard - FinTech Demo</title> 
    <link 
href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
rel="stylesheet"> 
</head> 
<body> 
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary"> 
        <div class="container"> 
            <a 
class="navbar-brand" href="#">FinTech Demo</a> 
            <div 
class="navbar-nav ms-auto"> 
                < a 
class="nav-link" href="dashboard.php">Dashboard</a> 
                < a 
                < a 
class="nav-link" href="transfer.php">Transfert</a> 
2026-03-02
class="nav-link" href="#" onclick="logout()">Déconnexion</a> 
            </div> 
        </div> 
    </nav> 
    <div class="container mt-4"> 
        <div class="row"> 
            <div 
class="col-md-8"> 
                < div 
class="card"> 
15 / 23
fintech_App_edu.md
class="card-header"> 
                        < h5>Bienvenue, <?php echo 
htmlspecialchars($_SESSION['username']); ?></h5> 
                        < small 
2026-03-02
                    < div 
</small> 
                    </div> 
                    < div 
class="text-muted">User ID: <?php echo $userId; ?>
class="card-body"> 
                        < h2 
class="text-primary"> 
                            <? php 
echo number_format($balance['balance'], 2); ?> € 
                        </ h2> 
                        < p 
class="text-muted">Solde actuel</p> 
                        < a 
transfert</a> 
                    </div> 
                </div> 
                < div 
href="transfer.php" class="btn btn-success">Nouveau 
class="card mt-4"> 
                    < div 
class="card-header"> 
                        < h6>Historique des transactions</h6> 
                    </div> 
                    < div 
class="card-body"> 
                        < div 
class="table-responsive"> 
                            < table 
class="table"> 
                                < thead> 
                                    < tr> 
                                        < th>Date</th> 
                                        < th>De</th> 
                                        < th>Vers</th> 
                                        < th>Montant</th> 
                                        < th>Description</th> 
                                    </ tr> 
                                </ thead> 
                                < tbody> 
                                    <? php 
foreach ($history as $transaction): ?> 
                                    < tr> 
                                        < td><?php 
strtotime($transaction['created_at'])); ?></td> 
                                        < td><?php 
echo date('d/m/Y H:i', 
echo 
htmlspecialchars($transaction['from_username']); ?></td> 
                                        < td><?php 
echo 
htmlspecialchars($transaction['to_username']); ?></td> 
                                        < td><?php 
echo 
number_format($transaction['amount'], 2); ?> €</td> 
                                        < td> 
                                            <? php  
                                            // 
VULNÉRABILITÉ XSS : Pas 
d'échappement HTML 
echo $transaction['description'];  
                                            ?> 
                                        </ td> 
                                    </ tr> 
                                    <? php 
endforeach; ?> 
16 / 23
                                </ tbody> 
fintech_App_edu.md
2026-03-02
                            </ table> 
                        </ div> 
                    </div> 
                </div> 
            </div> 
            <div 
class="col-md-4"> 
                < div 
class="card"> 
                    < div 
class="card-header"> 
                        < h6>Test 
des vulnérabilités</h6> 
                    </div> 
                    < div 
class="card-body"> 
                        < h6>IDOR
 - 
                        < div 
Voir le solde d'autres utilisateurs :</h6> 
class="btn-group-vertical d-grid gap-2"> 
                            < button 
onclick="viewBalance(1)" class="btn btn-sm 
btn-outline-danger">Admin (ID: 1)</button> 
                            < button 
onclick="viewBalance(4)" class="btn btn-sm 
btn-outline-danger">Victime (ID: 4)</button> 
                        </ div> 
                        < hr> 
                        < h6>XSS 
                        < p 
Test :</h6> 
class="small">Essayez un transfert avec comme 
description :</p> 
                        < code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
    <script src="assets/js/app.js"></script> 
    <script> 
function viewBalance(userId) { 
            fetch(`../api/wallet/balance.php?user_id=${userId}`) 
                .then(r => r.json()) 
                .then(data => { 
if (data.success) { 
                        alert(`Solde de ${data.data.full_name}: 
${data.data.balance}€`); 
                    } 
                }); 
        } 
function logout() { 
            fetch('../api/auth/logout.php').then(() => { 
                window.location.href = 'index.php'; 
            }); 
        } 
    </script> 
</body> 
</html> 
17 / 23
fintech_App_edu.md
Fichier public/transfer.php :
<?php 
session_start(); 
require_once '../core/Auth.php'; 
$auth = new Auth(); 
if (!$auth->isLoggedIn()) { 
    header('Location: index.php'); 
exit; 
} 
?> 
<!DOCTYPE html> 
<html lang="fr"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Transfert - FinTech Demo</title> 
    <link 
href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
rel="stylesheet"> 
</head> 
<body> 
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary"> 
        <div class="container"> 
            <a 
class="navbar-brand" href="#">FinTech Demo</a> 
            <div 
class="navbar-nav ms-auto"> 
                < a 
class="nav-link" href="dashboard.php">Dashboard</a> 
                < a 
                < a 
class="nav-link" href="transfer.php">Transfert</a> 
2026-03-02
class="nav-link" href="#" onclick="logout()">Déconnexion</a> 
            </div> 
        </div> 
    </nav> 
    <div class="container mt-4"> 
        <div class="row justify-content-center"> 
            <div 
class="col-md-6"> 
                < div 
class="card"> 
                    < div 
class="card-header"> 
                        < h5>Nouveau 
transfert</h5> 
                    </div> 
                    < div 
class="card-body"> 
                        < div 
id="message" class="alert" style="display: none;">
</div> 
destinataire</label> 
                        < form 
id="transferForm"> 
                            < div 
class="mb-3"> 
                                < label 
for="to_user_id" class="form-label">ID 
                                < input 
type="number" class="form-control" 
id="to_user_id" name="to_user_id" required> 
                                < div 
class="form-text">IDs disponibles : 1 
18 / 23
fintech_App_edu.md
(admin), 2 (alice), 3 (bob), 4 (victim)</div> 
                            </ div> 
                            < div 
class="mb-3"> 
                                < label 
2026-03-02
for="amount" class="form-label">Montant (€)
</label> 
                                < input 
type="number" class="form-control" 
id="amount" name="amount" step="0.01" required> 
                            </ div> 
                            < div 
class="mb-3"> 
                                < label 
for="description" class="form
label">Description</label> 
                                < input 
id="description" name="description"> 
                            </ div> 
                            <!-- 
type="text" class="form-control" 
VULNÉRABILITÉ : Champ caché modifiable --> 
                            < input 
type="hidden" name="from_user_id" value="<?php 
echo $auth->getUserId(); ?>"> 
                            < button 
success">Envoyer</button> 
                            < a 
type="submit" class="btn btn
href="dashboard.php" class="btn btn
secondary">Annuler</a> 
                        </ form> 
                    </div> 
                </div> 
            </div> 
            <div 
class="col-md-6"> 
                < div 
class="card"> 
                    < div 
class="card-header"> 
                        < h6>Tests 
                    </div> 
                    < div 
d'exploitation</h6> 
class="card-body"> 
                        < h6>1. 
Montant négatif :</h6> 
                        < p 
montant</p> 
class="small">Essayez de saisir <code>-100</code> comme 
                        < h6>2. 
                        < p 
Modifier le compte source :</h6> 
class="small">Ouvrez DevTools (F12), modifiez le champ 
caché <code>from_user_id</code> pour débiter un autre compte</p> 
                        < h6>3. 
                        < button 
btn-danger"> 
Race condition :</h6> 
onclick="raceConditionAttack()" class="btn btn-sm 
Attaque race condition 
                        </ button> 
                        < p 
class="small">Envoie plusieurs transferts 
simultanés</p> 
                    </div> 
                </div> 
            </div> 
19 / 23
fintech_App_edu.md
        </div> 
    </div> 
2026-03-02
    <script src="assets/js/app.js"></script> 
    <script> 
document.getElementById('transferForm').addEventListener('submit', async 
function(e) { 
            e.preventDefault(); 
const formData = new FormData(this); 
const messageDiv = document.getElementById('message'); 
try { 
const response = await fetch('../api/transfer/send.php', { 
                    method: 
'POST', 
                    body: formData 
                }); 
const result = await response.json(); 
if (result.success) { 
                    messageDiv.className = 'alert alert-success'; 
                    messageDiv.textContent = result.message; 
                    messageDiv.style.display = 'block'; 
                    this.reset(); 
                } 
else { 
                    messageDiv.className = 'alert alert-danger'; 
                    messageDiv.textContent = result.message; 
                    messageDiv.style.display = 'block'; 
                } 
            } 
            } 
        }); 
catch (error) { 
                messageDiv.className = 'alert alert-danger'; 
                messageDiv.textContent = 'Erreur lors du transfert'; 
                messageDiv.style.display = 'block'; 
function raceConditionAttack() { 
const amount = 500; // Montant à envoyer 
const promises = []; 
for (let i = 0; i < 5; i++) { 
const formData = new FormData(); 
                formData.append('to_user_id', '2'); 
                formData.append('amount', amount); 
                formData.append('description', `Race condition test ${i+1}`); 
                promises.push(fetch('../api/transfer/send.php', { 
                    method: 
'POST', 
                    body: formData 
                })); 
            } 
20 / 23
            Promise.all(promises).then(results => { 
fintech_App_edu.md
        } 
function logout() { 
            fetch('../api/auth/logout.php').then(() => { 
                window.location.href = 'index.php'; 
            }); 
        } 
    </script> 
</body> 
</html> 
Documentation des vulnérabilités - Fichier docs/vulnerabilities.md :
# Documentation des Vulnérabilités - Version 1 
## 1. Injection SQL (Critique) 
**Localisation :** `core/Auth.php`, ligne 15 
**Impact :** Accès non autorisé aux comptes, vol de données 
**Exploitation :**
Username: admin' OR '1'='1' -
Password: anything
**Coût estimé :**  - Amendes RGPD : jusqu'à 4% du CA - Perte de confiance client : -30% de revenus - Coûts légaux : 500k€ - 2M€ 
## 2. IDOR - Accès non autorisé aux données (Élevé) 
**Localisation :** `api/wallet/balance.php` 
**Impact :** Vol d'informations financières 
**Exploitation :** 
GET /api/wallet/balance.php?user_id=4
**Coût estimé :** - Violation de confidentialité : 50€ - 500€ par client impacté - Amendes : 10M€ ou 2% du CA 
2026-03-02
                alert(`${results.length} transferts envoyés simultanément !`); 
                setTimeout(() => location.reload(), 2000); 
            }); 
21 / 23
fintech_App_edu.md
## 3. Manipulation du compte source (Critique) 
**Localisation :** `api/transfer/send.php` 
**Impact :** Vol direct d'argent 
**Exploitation :** 
Modifier le champ caché `from_user_id` dans le formulaire 
**Coût estimé :** - Pertes directes : illimitées - Fermeture possible de la plateforme 
## 4. Race Condition (Élevé) 
**Localisation :** `core/Transfer.php` 
**Impact :** Création d'argent fictif 
**Script d'exploitation :** 
```javascript 
// Code fourni dans l'interface 
Coût estimé :
Pertes : potentiellement illimitées
Insolvabilité de la plateforme
5. XSS - Cross-Site Scripting (Moyen)
Localisation : public/dashboard.php, affichage des descriptions
Impact : Vol de sessions, phishing
Exploitation :
Description de transfert : <script>alert('XSS')</script>
6. Mots de passe faibles (Élevé)
Localisation : core/Auth.php, utilisation de MD5
Impact : Compromission des comptes
Coût estimé :
Coût de notification : 1€ - 5€ par client
Réinitialisation des mots de passe : coûts opérationnels élevés
2026-03-02
Guide d'Exploitation pour la Démonstration
Scénario 1 : Injection SQL
1. Aller sur la page de connexion
2. Saisir : admin' OR '1'='1' -
22 / 23
fintech_App_edu.md
2026-03-02
3. Mot de passe quelconque
4. → Connexion réussie en tant qu'admin
Scénario 2 : Vol de données via IDOR
1. Se connecter normalement
2. Modifier l'URL : dashboard.php → ../api/wallet/balance.php?user_id=4
3. → Affichage du solde de la victime
Scénario 3 : Transfert frauduleux
1. Aller sur la page transfert
2. DevTools (F12) → Modifier from_user_id à 4
3. Effectuer un transfert vers son compte
4. → Argent débité du compte de la victime
Scénario 4 : Race Condition
1. Utiliser le bouton "Attaque race condition"
2. → 5 transferts de 500€ effectués avec un solde de 1000€
Planning de Développement (7 jours)
Jour 1 : Configuration (Étudiants 1 & 2)
Jour 2 : Authentification (Étudiant 3)
Jour 3 : Wallet (Étudiant 4)
Jour 4 : Transferts (Étudiant 5)
Jour 5 : Interface & Tests (Étudiant 6)
Jour 6 : Démonstration V1 et documentation des failles
Jour 7 : Sécurisation pour V2
Critères d'Évaluation
Fonctionnalité (25%) : Application opérationnelle avec toutes les fonctionnalités
Vulnérabilités V1 (25%) : Failles exploitables et bien documentées
Corrections V2 (25%) : Sécurisation effective des vulnérabilités
Workflow Git (15%) : Utilisation correcte des branches et Pull Requests
Documentation (10%) : Qualité de la documentation technique et des guides d'exploitation
23 / 23