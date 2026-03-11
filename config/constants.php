<?php
date_default_timezone_set('Africa/Dakar');
// Version 1 - Configuration volontairement faible 
define('APP_NAME', 'Fintech Robuste'); 
define('APP_VERSION', '1.0-Secured'); 
define('DEBUG_MODE', false);  // Désactivé en prod
// Sécurité renforcée
define('PASSWORD_MIN_LENGTH', 8); 
define('SESSION_TIMEOUT', 3600); 
define('ENABLE_LOGGING', true);  // Logs activés
// Limites de transaction 
define('MAX_TRANSFER_AMOUNT', 999999); 
define('MIN_TRANSFER_AMOUNT', 0.01); 

// Noms des tables (pour contourner les corruptions InnoDB)
define('TABLE_TRANSACTIONS', 'transactions_v2');
define('CURRENCY', 'FCFA');
define('BANK_FEE_PERCENT', 0.01); // 1% simulated bank fee
?>
