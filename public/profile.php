<?php
session_start();
require_once __DIR__ . '/../private/database.php';

if(empty($_SESSION['user_logged'])){
    header('Location: login.php');
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

// Buscar endereços do usuário
$stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Área do Cliente</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<h2>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
<a href="index.php" class="btn">Voltar ao Cardápio</a>
<h3>Meus Endereços</h3>
<ul>
<?php foreach($addresses as $addr): ?>
    <li><?php echo htmlspecialchars($addr['address'] . ', ' . $addr['city'] . ' - ' . $addr['zip']); ?></li>
<?php endforeach; ?>
</ul>

<h3>Adicionar Endereço</h3>
<form method="post" action="add_address.php">
    <input type="text" name="address" placeholder="Endereço completo" required>
    <input type="text" name="city" placeholder="Cidade" required>
    <input type="text" name="zip" placeholder="CEP" required>
    <button type="submit" class="btn">Adicionar</button>
</form>

<a href="checkout.php" class="btn">Ir para Pagamento</a>
</body>
</html>
