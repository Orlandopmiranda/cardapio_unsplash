<?php
session_start();
require_once __DIR__ . '/../private/Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: meus_pedidos.php');
    exit;
}

$orderId = intval($_GET['id']);
$db = new Database();
$pdo = $db->getConnection();

// Busca o pedido
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<p>Pedido não encontrado.</p>";
    exit;
}

$items = json_decode($order['items_json'], true);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Detalhes do Pedido</title>
<link rel="stylesheet" href="assets/css/styles.css">
<style>
.details-container {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  padding: 30px;
  max-width: 700px;
  margin: 30px auto;
}
.details-container h2 {
  color: #e63946;
  text-align: center;
  margin-bottom: 20px;
}
.item {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #eee;
}
.total {
  text-align: right;
  font-size: 18px;
  font-weight: bold;
  margin-top: 10px;
  color: #e63946;
}
</style>
</head>
<body>

<header>
  <h1>🍝 GourmetOnline</h1>
  <nav>
    <a href="meus_pedidos.php">Voltar</a>
    <a href="logout.php">Sair</a>
  </nav>
</header>

<div class="details-container">
  <h2>Detalhes do Pedido #<?php echo $order['id']; ?></h2>
  <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
  <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
  <hr>

  <h3>Itens:</h3>
  <?php foreach($items as $item): ?>
      <div class="item">
          <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
          <span>R$ <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></span>
      </div>
  <?php endforeach; ?>

  <p class="total">Total: R$ <?php echo number_format($order['amount'], 2, ',', '.'); ?></p>
</div>

</body>
</html>
