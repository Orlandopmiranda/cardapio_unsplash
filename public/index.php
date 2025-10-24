<?php session_start(); ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cardápio - Restaurante</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
    <h1>Nosso Cardápio</h1>
    <nav>
      <a href="cart.php">Carrinho (<span id="cart-count">0</span>)</a>
      <?php if(!empty($_SESSION['user_logged'])): ?>
        <span>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="logout.php">Sair</a>
      <?php else: ?>
        <a href="login.php">Entrar</a>
        <a href="register.php">Cadastrar</a>
      <?php endif; ?>
      <a href="admin/login.php">Acesso Admin</a>
    </nav>
  </header>
  <aside id="categories"></aside>
  <main id="menu"></main>
  <script src="assets/js/app.js"></script>
</body>
</html>
