<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'cardapio_unsplash';
    private $username = 'root';
    private $password = '';
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die('<h3 style="color:red;">Erro de conexão segura com o banco de dados.</h3>');
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>




