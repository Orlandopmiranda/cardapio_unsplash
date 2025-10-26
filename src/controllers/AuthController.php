<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../private/db.php';

class AuthController {
    private $user;

    public function __construct() {
        $db = new Database();
        $pdo = $db->getConnection();
        $this->user = new User($pdo);
    }

    public function register($nome, $email, $senha) {
        return $this->user->register($nome, $email, $senha);
    }

    public function login($email, $senha) {
        return $this->user->login($email, $senha);
    }
}
?>
