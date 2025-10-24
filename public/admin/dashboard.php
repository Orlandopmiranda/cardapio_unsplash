<?php
session_start();
if(empty($_SESSION['admin_logged'])){ header('Location: login.php'); exit; }
?><!doctype html><html><head><meta charset="utf-8"><title>Admin</title></head><body>
<h2>Painel Administrativo</h2>
<p>Logado como: <?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'admin'); ?></p>
<p><a href="logout.php">Sair</a></p>
</body></html>