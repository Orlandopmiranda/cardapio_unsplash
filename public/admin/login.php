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
    header('Location: dashboard.php'); exit;
  } else {
    $error = 'Usuário ou senha incorretos.';
  }
}
?><!doctype html><html><head><meta charset="utf-8"><title>Login Admin</title></head><body>
<h2>Login Administrativo</h2>
<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
  <label>Usuário: <input name="user" required></label><br>
  <label>Senha: <input name="pass" type="password" required></label><br>
  <button type="submit">Entrar</button>
</form>
</body></html>