<?php
session_start();
require_once __DIR__ . '/../src/db.php';
$pdo = getPDO();

$error = '';

if (isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Buscar usuário no banco
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Usuário autenticado
        $_SESSION['user_logged'] = true;
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];

        // ===========================
        // Migrar carrinho da sessão
        // ===========================
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $stmt = $pdo->prepare("
                    INSERT INTO cart_items (user_id, dish_id, quantity)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE quantity = quantity + ?
                ");
                $stmt->execute([
                    $user['id'],
                    $item['id'],
                    $item['qty'],
                    $item['qty']
                ]);
            }
            // Limpar carrinho da sessão após migrar
            unset($_SESSION['cart']);
        }

        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuário ou senha incorretos.';
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h2>Login</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Usuário: <input type="text" name="username" required></label><br>
        <label>Senha: <input type="password" name="password" required></label><br>
        <button type="submit">Entrar</button>
    </form>

    <p>Não tem conta? <a href="register.php">Cadastre-se</a></p>
</body>
</html>
