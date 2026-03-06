<?php
// Version 1 : Configuration volontairement faible
define('APP_NAME', 'FinTech Demo');
define('APP_VERSION', '1.0');
define('DEBUG_MODE', true); // Affiche les erreurs SQL

// Sécurité volontairement faible
define('PASSWORD_MIN_LENGTH', 3);
define('SESSION_TIMEOUT', 86400);
define('ENABLE_LOGGING', false); // Pas de logs en V1

// Limites de transaction
define('MAX_TRANSFER_AMOUNT', 999999);
define('MIN_TRANSFER_AMOUNT', 0.01);
?>