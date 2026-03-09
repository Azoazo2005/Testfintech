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
