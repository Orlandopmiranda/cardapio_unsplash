<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($nome, $email, $senha) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) return false; // já existe

            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $email, $hash]);
            return true;
        } catch (PDOException $e) {
            error_log("Erro register: " . $e->getMessage());
            return false;
        }
    }

    public function login($email, $senha) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($senha, $user['senha'])) {
                session_start();
                $_SESSION['usuario'] = $user['nome'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro login: " . $e->getMessage());
            return false;
        }
    }
}
?>
