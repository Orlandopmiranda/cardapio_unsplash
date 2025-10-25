<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Obrigado pelo pedido</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h2>Pedido Finalizado!</h2>

    <?php if (!empty($_SESSION['success'])): ?>
        <p style="color:green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <a href="index.php">Voltar ao cardápio</a>
</body>
</html>
