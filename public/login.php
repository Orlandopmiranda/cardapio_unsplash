<?php
session_start();

require_once __DIR__ . '/../src/db.php';

require_once __DIR__ . '/../private/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    die('<h3 style="color:red;">Erro de conexão segura com o banco de dados.</h3>');
}

if(isset($_POST['username'])){
    $u = trim($_POST['username']);
    $p = $_POST['password'];


    $pdo = getPDO();

    $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$u]);
    $user = $stmt->fetch();

    if($user && password_verify($p, $user['password'])){
        $_SESSION['user_logged'] = true;
        $_SESSION['user_name'] = $user['username'];

        $_SESSION['user_id'] = $user['id']; // importante para carrinho vinculado
        header('Location: index.php'); 
        exit;
    } else {
        $error = 'Usuário ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div class="login-box">
    <h2>Login</h2>
    <?php if(!empty($error)): ?>
        <div class="message"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Usuário" class="input-field" required>
        <input type="password" name="password" placeholder="Senha" class="input-field" required>
        <button type="submit" class="btn">Entrar</button>
    </form>
    <a href="register.php" class="link">Não tem conta? Cadastre-se</a>

    <a href="index.php" class="link">← Voltar para o Cardápio</a>
</div>
</body>
</html>
