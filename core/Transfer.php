<?php
require_once __DIR__ . '/../config/database.php'; 
require_once __DIR__ . '/../config/constants.php'; 
require_once __DIR__ . '/Wallet.php'; 

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
// VULNÉRABILITÉ 1 : Pas de validation du montant (peut être négatif) 
if ($amount == 0) { 
return ['success' => false, 'message' => 'Montant invalide']; 
        } 

// VULNÉRABILITÉ 2 : Vérification de solde non atomique (race condition) 
        $senderWallet = $this->wallet->getBalance($fromUserId); 
if ($senderWallet && $senderWallet['balance'] < $amount) { 
return ['success' => false, 'message' => 'Solde insuffisant']; 
        } 

// VULNÉRABILITÉ 3 : Pas de transaction SQL (atomicité) 
        $newSenderBalance = $senderWallet['balance'] - $amount; 
        $sql1 = "UPDATE accounts SET balance = $newSenderBalance WHERE user_id = $fromUserId"; 
$this->db->query($sql1); 

        $receiverWallet = $this->wallet->getBalance($toUserId); 
if ($receiverWallet) {
            $newReceiverBalance = $receiverWallet['balance'] + $amount; 
            $sql2 = "UPDATE accounts SET balance = $newReceiverBalance WHERE user_id = $toUserId"; 
            $this->db->query($sql2); 
        }

// Enregistrement de la transaction 
        $senderAcc = $this->wallet->getBalance($fromUserId);
        $receiverAcc = $this->wallet->getBalance($toUserId);
        $senderAccountId = $senderAcc['id'] ?? 0;
        $receiverAccountId = $receiverAcc['id'] ?? 0;

        $sql3 = "INSERT INTO " . TABLE_TRANSACTIONS . " (from_user_id, to_user_id, amount, description, status)  
                 VALUES ($fromUserId, $toUserId, $amount, '$description', 'completed')"; 
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
