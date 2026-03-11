<?php
require_once __DIR__ . '/constants.php';
date_default_timezone_set('Africa/Dakar');

class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'fintech-robuste';
    private $connection;

    public function __construct() {
        $this->connect();
    }

    // Version 1 : Connexion sans gestion d'erreur sécurisée
    private function connect() {
        $this->connection = mysqli_connect($this->host, $this->username, $this->password, $this->database);
        
        if (!$this->connection) {
            // VULNERABILITÉ : Affichage des erreurs de connexion
            die("Erreur de connexion: " . mysqli_connect_error());
        }
        mysqli_set_charset($this->connection, "utf8mb4");
    }

    public function getConnection() {
        return $this->connection;
    }

    // Secure version with prepared statements
    public function prepare($sql) {
        return mysqli_prepare($this->connection, $sql);
    }

    public function execute($stmt, $params = [], $types = "") {
        if (!empty($params)) {
            if (empty($types)) {
                $types = str_repeat('s', count($params));
            }
            try {
                $bind = @mysqli_stmt_bind_param($stmt, $types, ...$params);
                if (!$bind) {
                    throw new Exception("Bind Error: " . mysqli_stmt_error($stmt) . " (Types: $types, Count: " . count($params) . ")");
                }
            } catch (Throwable $t) {
                throw new Exception("Mise à jour du système : Erreur de paramètres (" . $t->getMessage() . "). Veuillez réessayer.");
            }
        }
        
        $execution = mysqli_stmt_execute($stmt);
        if (!$execution && DEBUG_MODE) {
            error_log("SQL Error: " . mysqli_stmt_error($stmt));
        }
        return mysqli_stmt_get_result($stmt) ?: $execution;
    }

    public function query($sql) {
        $result = mysqli_query($this->connection, $sql);
        
        if (!$result && DEBUG_MODE) {
            // Keep for backward compatibility but log instead of echo if possible
            error_log("SQL Error: " . mysqli_error($this->connection));
        }
        return $result;
    }

    public function fetchAll($result) {
        if (!$result) return [];
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function fetchOne($result) {
        if (!$result) return null;
        return mysqli_fetch_assoc($result);
    }
}
