<?php
session_start();
require_once __DIR__ . '/../private/Database.php';

$db = new Database();
$pdo = $db->getConnection();

// ====================
// Adicionar endereço
// ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_address'])) {
    $stmt = $pdo->prepare("
        INSERT INTO user_addresses (user_id, street, number, city, state, zip)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['street'],
        $_POST['number'],
        $_POST['city'],
        $_POST['state'],
        $_POST['zip']
    ]);
    $_SESSION['success'] = 'Endereço adicionado!';
    header('Location: checkout.php');
    exit;
}

// ====================
// Pegar carrinho
// ====================
$stmt = $pdo->prepare("
    SELECT ci.quantity, d.id, d.name, d.price 
    FROM cart_items ci 
    JOIN dishes d ON ci.dish_id=d.id 
    WHERE ci.user_id=?
");
$stmt->execute([$_SESSION['user_id']]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ====================
// Pegar endereços
// ====================
$stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id=?");
$stmt->execute([$_SESSION['user_id']]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ====================
// Finalizar pedido
// ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address_id'])) {
    $addressId = $_POST['address_id'];
    $total = 0;
    foreach ($cart as $item) $total += $item['price'] * $item['quantity'];

    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, address_id, items_json, amount, status) 
        VALUES (?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $addressId,
        json_encode($cart),
        $total
    ]);

    // limpar carrinho
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id=?");
    $stmt->execute([$_SESSION['user_id']]);

    header('Location: success.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<h2>Checkout</h2>

<?php if(!empty($_SESSION['success'])): ?>
    <p style="color:green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
<?php endif; ?>

<h3>Adicionar novo endereço</h3>
<form method="post">
    <input type="text" name="street" placeholder="Rua" required>
    <input type="text" name="number" placeholder="Número" required>
    <input type="text" name="city" placeholder="Cidade" required>
    <input type="text" name="state" placeholder="Estado" required>
    <input type="text" name="zip" placeholder="CEP" required>
    <button type="submit" name="add_address" class="btn">Adicionar Endereço</button>
</form>

<h3>Escolher endereço para entrega</h3>
<form method="post">
    <?php foreach($addresses as $addr): ?>
        <div>
            <input type="radio" name="address_id" value="<?php echo $addr['id']; ?>" required>
            <?php echo htmlspecialchars("{$addr['street']}, {$addr['number']} - {$addr['city']}/{$addr['state']} - CEP: {$addr['zip']}"); ?>
        </div>
    <?php endforeach; ?>
    <?php if($addresses): ?>
        
    <?php endif; ?>
</form>

<form id="payment-form" method="post">
    <label>Forma de pagamento:</label><br>
    <label><input type="radio" name="payment_method" value="boleto" required> Boleto</label><br>
    <label><input type="radio" name="payment_method" value="pix" required> Pix</label><br>
    <label><input type="radio" name="payment_method" value="Cartão de Crédito" required> Cartão de Crédito</label><br>
    <div id="card-element"><!-- Stripe Card Element aqui --></div>
    <button type="submit" name="finalizar_pedido" class="btn">Finalizar Pedido</button>
</form>


<a href="index.php" class="btn">Voltar para o Cardápio</a>
</body>
</html>
