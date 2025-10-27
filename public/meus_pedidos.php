<?php
session_start();
require_once __DIR__ . '/../private/Database.php';

// Verifica login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

// Busca pedidos do usuário
$stmt = $pdo->prepare("
    SELECT id, amount, status, created_at 
    FROM orders 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Meus Pedidos</title>
<link rel="stylesheet" href="assets/css/styles.css">
<style>
.orders-container {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  padding: 30px;
  max-width: 800px;
  margin: 30px auto;
}
.orders-container h2 {
  color: #e63946;
  text-align: center;
  margin-bottom: 20px;
}
.order-card {
  border: 1px solid #eee;
  border-radius: 10px;
  padding: 15px 20px;
  margin-bottom: 15px;
  background: #fafafa;
  transition: 0.3s;
}
.order-card:hover {
  background: #fff;
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}
.status {
  padding: 4px 10px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: bold;
  color: #fff;
}
.status.pending { background: #f39c12; }
.status.preparing { background: #3498db; }
.status.delivered { background: #2ecc71; }
.status.cancelled { background: #e74c3c; }
.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
}
.btn-details {
  background: #e63946;
  color: #fff;
  padding: 8px 14px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: bold;
  transition: 0.3s;
}
.btn-details:hover {
  background: #d62839;
}
</style>
</head>
<body>

<header>
  <h1>🍝 GourmetOnline</h1>
  <nav>
    <a href="index.php">Cardápio</a>
    <a href="checkout.php">Checkout</a>
    <a href="logout.php">Sair</a>
  </nav>
</header>

<div class="orders-container">
  <h2>🧾 Meus Pedidos</h2>

  <?php if(!$orders): ?>
      <p style="text-align:center; color:#555;">Você ainda não fez nenhum pedido.</p>
  <?php else: ?>
      <?php foreach($orders as $order): ?>
          <div class="order-card">
              <div class="order-header">
                  <strong>Pedido #<?php echo $order['id']; ?></strong>
                  <span class="status <?php echo htmlspecialchars($order['status']); ?>">
                      <?php echo ucfirst($order['status']); ?>
                  </span>
              </div>
              <p><strong>Total:</strong> R$ <?php echo number_format($order['amount'], 2, ',', '.'); ?></p>
              <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
              <div class="order-footer">
                  <a href="pedido_detalhes.php?id=<?php echo $order['id']; ?>" class="btn-details">Ver Detalhes</a>
              </div>
          </div>
      <?php endforeach; ?>
  <?php endif; ?>
</div>

</body>
</html>

