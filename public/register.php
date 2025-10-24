<?php
session_start();
require_once __DIR__ . '/../src/db.php';
if(isset($_POST['username'])){
  $u = trim($_POST['username']);
  $p = $_POST['password'];
  if(strlen($u) < 3 || strlen($p) < 4){
    $error = 'Usuário ou senha muito curto.';
  } else {
    $pdo = getPDO();
    // check existing
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
      header('Location: index.php'); exit;
    }
  }
}
?><!doctype html><html><head><meta charset="utf-8"><title>Cadastro</title></head><body>
<h2>Cadastro de usuário</h2>
<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
  <label>Usuário: <input name="username" required></label><br>
  <label>Senha: <input name="password" type="password" required></label><br>
  <button type="submit">Cadastrar</button>
</form>
<p>Já tem conta? <a href="login.php">Entrar</a></p>
</body></html>