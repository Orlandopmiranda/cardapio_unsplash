<?php
session_start();
require_once __DIR__ . '/../../src/db.php';

if(isset($_POST['user'])){
    $u = $_POST['user'];
    $p = $_POST['pass'];

    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$u]);
    $user = $stmt->fetch();

    if($user && password_verify($p, $user['password'])){
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user'] = $user['username'];
        header('Location: dashboard.php'); 
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
<title>Login Administrativo</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-box">
        <h2>Login Administrativo</h2>

        <?php if(!empty($error)): ?>
            <div class="message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="user" placeholder="Usuário" class="input-field" required>
            <input type="password" name="pass" placeholder="Senha" class="input-field" required>
            <button type="submit" class="btn">Entrar</button>
        </form>
    </div>
</body>
</html>
