<?php
require_once __DIR__ . '/../config/database.php'; 
require_once __DIR__ . '/../config/constants.php'; 
require_once __DIR__ . '/Wallet.php'; 
require_once __DIR__ . '/Auth.php'; 

class Transfer { 
private $db; 
private $wallet; 

public function __construct() { 
$this->db = new Database(); 
$this->wallet = new Wallet(); 
    } 

    // Secure Transfer with atomic updates and fees
    public function sendMoney($fromUserId, $toUserId, $amount, $description = '', $paymentMethod = 'Transfer') 
    { 
        if ($amount <= 0) { 
            return ['success' => false, 'message' => 'Montant invalide']; 
        } 

        $conn = $this->db->getConnection();
        mysqli_begin_transaction($conn);

        try {
            // 1. Calculate fee
            $fee = $amount * BANK_FEE_PERCENT;
            $totalDebit = $amount + $fee;

            // 2. Atomic Debit (check balance in the same query)
            $sqlDebit = "UPDATE accounts SET balance = balance - ? WHERE user_id = ? AND balance >= ?";
            $stmtDebit = $this->db->prepare($sqlDebit);
            $this->db->execute($stmtDebit, [$totalDebit, $fromUserId, $totalDebit], "did");

            if (mysqli_stmt_affected_rows($stmtDebit) === 0) {
                throw new Exception("Solde insuffisant ou erreur de compte");
            }

            // 3. Credit recipient
            $sqlCredit = "UPDATE accounts SET balance = balance + ? WHERE user_id = ?";
            $stmtCredit = $this->db->prepare($sqlCredit);
            $this->db->execute($stmtCredit, [$amount, $toUserId], "di");

            if (mysqli_stmt_affected_rows($stmtCredit) === 0) {
                throw new Exception("Destinataire introuvable");
            }

            // 4. Log Transaction (v2)
            $sqlLog = "INSERT INTO " . TABLE_TRANSACTIONS . " (from_user_id, to_user_id, amount, fee, description, payment_method, status)  
                       VALUES (?, ?, ?, ?, ?, ?, ?)"; 
            $stmtLog = $this->db->prepare($sqlLog);
            $this->db->execute($stmtLog, [$fromUserId, $toUserId, $amount, $fee, $description, $paymentMethod, 'completed'], "iiddsss"); 

            $transactionId = mysqli_insert_id($conn);

            // 5. Audit Log
            $auth = new Auth();
            $auth->logEvent($fromUserId, "TRANSFER_SENT", "Transfer of $amount to User #$toUserId (Fee: $fee)");

            mysqli_commit($conn);
            return [ 
                'success' => true, 
                'transaction_id' => $transactionId, 
                'message' => 'Transfert effectué avec succès',
                'amount' => $amount,
                'subtotal' => $amount,
                'fee' => $fee,
                'total' => $totalDebit,
                'method' => $_POST['method_name'] ?? 'Transfert'
            ]; 
        } catch (Throwable $e) {
            if (isset($conn)) mysqli_rollback($conn);
            return ['success' => false, 'message' => $e->getMessage()]; 
        }
    }
} 
