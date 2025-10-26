<?php
session_start();
<<<<<<< HEAD
require_once __DIR__ . '/../src/db.php';
=======
require_once __DIR__ . '/../private/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    die('<h3 style="color:red;">Erro de conexão segura com o banco de dados.</h3>');
}
>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)

if(isset($_POST['username'])){
    $u = trim($_POST['username']);
    $p = $_POST['password'];

<<<<<<< HEAD
    if(strlen($u) < 3 || strlen($p) < 4){
        $error = 'Usuário ou senha muito curto.';
    } else {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$u]);
        if($stmt->fetch()){
            $error = 'Usuário já existe.';
        } else {
            $hash = password_hash($p, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?,?)');
            $stmt->execute([$u, $hash]);
            $_SESSION['user_logged'] = true;
            $_SESSION['user_name'] = $u;
            header('Location: index.php'); 
            exit;
        }
=======
    // Verifica se usuário já existe
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$u]);
    if($stmt->fetch()){
        $error = 'Usuário já existe.';
    } else {
        // Cria usuário com senha hash
        $hash = password_hash($p, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->execute([$u, $hash]);
        $_SESSION['user_logged'] = true;
        $_SESSION['user_name'] = $u;
        $_SESSION['user_id'] = $pdo->lastInsertId();
        header('Location: index.php');
        exit;
>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro</title>
<link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div class="login-box">
    <h2>Cadastro</h2>
    <?php if(!empty($error)): ?>
        <div class="message"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Usuário" class="input-field" required>
        <input type="password" name="password" placeholder="Senha" class="input-field" required>
        <button type="submit" class="btn">Cadastrar</button>
    </form>
<<<<<<< HEAD
    <a href="login.php" class="link">Já tem conta? Entre aqui</a>
=======
    <a href="login.php" class="link">Já tem conta? Faça login</a>
    <a href="index.php" class="link">← Voltar para o Cardápio</a>
>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)
</div>
</body>
</html>
