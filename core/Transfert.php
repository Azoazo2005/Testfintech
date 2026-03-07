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

    // Version 1 : Transfert avec multiples vulnérabilités
    public function sendMoney($fromUserId, $toUserId, $amount, $description = '') {
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
        // Si le processus s'interrompt entre ces requêtes, l'argent disparaît ou se duplique
        $newSenderBalance = $senderWallet['balance'] - $amount;
        $sql1 = "UPDATE wallets SET balance = $newSenderBalance WHERE user_id = $fromUserId";
        $this->db->query($sql1);

        $receiverWallet = $this->wallet->getBalance($toUserId);
        $newReceiverBalance = $receiverWallet['balance'] + $amount;
        $sql2 = "UPDATE wallets SET balance = $newReceiverBalance WHERE user_id = $toUserId";
        $this->db->query($sql2);

        // Enregistrement de la transaction
        $sql3 = "INSERT INTO transactions (from_user_id, to_user_id, amount, description, status) 
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