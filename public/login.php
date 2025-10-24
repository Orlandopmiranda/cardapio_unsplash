<?php
session_start();
require_once __DIR__ . '/../src/db.php';
if(isset($_POST['username'])){
  $u = $_POST['username']; $p = $_POST['password'];
  $pdo = getPDO();
  $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
  $stmt->execute([$u]);
  $user = $stmt->fetch();
  if($user && password_verify($p, $user['password'])){
    $_SESSION['user_logged'] = true;
    $_SESSION['user_name'] = $user['username'];
    header('Location: index.php'); exit;
  } else {
    $error = 'Usuário ou senha incorretos.';
  }
}
?><!doctype html><html><head><meta charset="utf-8"><title>Login</title></head><body>
<h2>Login</h2>
<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
  <label>Usuário: <input name="username" required></label><br>
  <label>Senha: <input name="password" type="password" required></label><br>
  <button type="submit">Entrar</button>
</form>
<p>Não tem conta? <a href="register.php">Cadastre-se</a></p>
</body></html>