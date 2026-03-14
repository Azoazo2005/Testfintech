<?php
require_once '../../config/database.php';
require_once '../../config/constants.php';
session_start();

if (!isset($_SESSION['logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to_account_number = trim($_POST['to_account'] ?? ''); 
    $amount = floatval($_POST['amount'] ?? 0); 
    $description = trim($_POST['description'] ?? 'Transfert Sortant'); 
    $from_user_id = $_SESSION['user_id'];

    if (empty($to_account_number) || $amount <= 0) {
        header('Location: ../../public/dashboard.php?error=Invalid transfer details');
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    // 1. Get sender account
    $senderRes = $db->query("SELECT id, balance FROM accounts WHERE user_id = $from_user_id");
    $senderAcc = $db->fetchOne($senderRes);

    if (!$senderAcc || $senderAcc['balance'] < $amount) {
        header('Location: ../../public/dashboard.php?error=Insufficient balance');
        exit;
    }

    // 2. Get recipient account (Robust lookup)
    $recipientRes = $db->query("SELECT id, user_id FROM accounts WHERE TRIM(account_number) = '$to_account_number'");
    $recipientAcc = $db->fetchOne($recipientRes);

    if (!$recipientAcc) {
        header('Location: ../../public/dashboard.php?error=Recipient account not found');
        exit;
    }

    // 3. Perform transfer
    mysqli_begin_transaction($conn);
    try {
        $senderId = $senderAcc['id'];
        $recipientId = $recipientAcc['id'];
        $to_user_id = $recipientAcc['user_id'];

        // Debit sender
        $db->query("UPDATE accounts SET balance = balance - $amount WHERE id = $senderId");
        
        // Credit recipient
        $db->query("UPDATE accounts SET balance = balance + $amount WHERE id = $recipientId");

        // Log transaction (Standardizing on transactions_v2 columns)
        $logSql = "INSERT INTO " . TABLE_TRANSACTIONS . " (from_user_id, to_user_id, from_account_id, to_account_id, amount, description, status) 
                    VALUES ($from_user_id, $to_user_id, $senderId, $recipientId, $amount, '$description', 'completed')";
        $logResult = $db->query($logSql);

        if (!$logResult) {
            throw new Exception("Erreur lors de l'enregistrement de la transaction");
        }

        mysqli_commit($conn);
        header('Location: ../../public/dashboard.php?success=Transfer completed');
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header('Location: ../../public/dashboard.php?error=Transfer failed');
    }
}
?>
