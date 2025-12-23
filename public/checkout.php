<?php
session_start();
require_once __DIR__ . '/../private/Database.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

// Buscar itens do carrinho
$stmt = $pdo->prepare("
    SELECT ci.quantity, d.id, d.name, d.price 
    FROM cart_items ci 
    JOIN dishes d ON ci.dish_id = d.id 
    WHERE ci.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($cart as $item) $total += $item['price'] * $item['quantity'];

// Buscar endereços
$stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Adicionar novo endereço
if (isset($_POST['add_address'])) {
    if (
        !empty($_POST['street']) && 
        !empty($_POST['number']) && 
        !empty($_POST['city']) && 
        !empty($_POST['state']) && 
        !empty($_POST['zip'])
    ) {
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
    }

    header("Location: checkout.php");
    exit;
}

// Finalizar pedido
if (isset($_POST['finalizar_pedido'])) {
    $addressId = $_POST['address_id'];
    $payment = $_POST['payment_method'];
    $coupon = $_POST['coupon'] ?? '';
    $frete = floatval($_POST['frete'] ?? 0);

    $total_final = $total + $frete;

    $stmt = $pdo->prepare("
        INSERT INTO orders 
        (user_id, address_id, items_json, amount, payment_method, status, coupon, frete) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $addressId,
        json_encode($cart),
        $total_final,
        $payment,
        'pending',
        $coupon,
        $frete
    ]);

    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    header("Location: success.php");
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

<div class="checkout-container">
    <h2>Checkout</h2>

    <!-- Resumo -->
    <div class="checkout-section">
        <h3>Resumo do Pedido</h3>
        <?php foreach($cart as $item): ?>
            <p><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?> 
            - R$ <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></p>
        <?php endforeach; ?>
        <p><strong>Total: R$ <?php echo number_format($total,2,',','.'); ?></strong></p>
    </div>

    <!-- Escolher endereço -->
    <div class="checkout-section">
        <h3>Endereço de Entrega</h3>
        <form method="post">
            <?php foreach($addresses as $addr): ?>
                <label>
                    <input type="radio" name="address_id" value="<?php echo $addr['id']; ?>" required>
                    <?php echo htmlspecialchars("{$addr['street']}, {$addr['number']} - {$addr['city']}/{$addr['state']}"); ?>
                </label><br>
            <?php endforeach; ?>

            <h3>Forma de Pagamento</h3>
            <label><input type="radio" name="payment_method" value="pix" required> Pix</label><br>
            <label><input type="radio" name="payment_method" value="boleto" required> Boleto</label><br>
            <label><input type="radio" name="payment_method" value="cartao" required> Cartão de Crédito</label>

            <div class="payment-card">
                <input type="text" name="card_number" placeholder="Número do Cartão">
                <input type="text" name="card_name" placeholder="Nome no Cartão">
                <input type="text" name="card_exp" placeholder="MM/AA">
                <input type="text" name="card_cvv" placeholder="CVV">
            </div>

            <h4>Cupom e Frete</h4>
            <input type="text" name="coupon" placeholder="Código do Cupom">
            <input type="number" step="0.01" name="frete" placeholder="Valor do Frete" value="0">

            <button type="submit" name="finalizar_pedido" class="btn">Confirmar Pedido</button>
        </form>
    </div>

    <!-- Adicionar novo endereço -->
    <div class="checkout-section">
        <h3>Adicionar Novo Endereço</h3>
        <form method="post">
            <input type="text" name="street" placeholder="Rua" required>
            <input type="text" name="number" placeholder="Número" required>
            <input type="text" name="city" placeholder="Cidade" required>
            <input type="text" name="state" placeholder="Estado" required>
            <input type="text" name="zip" placeholder="CEP" required>

            <button type="submit" name="add_address" class="btn">Adicionar Endereço</button>
        </form>
    </div>
</div>

<div style="display:flex; gap:15px; margin-top:20px;">
    <a href="cart.php" class="btn" style="flex:1;">Voltar ao Carrinho</a>
    <a href="index.php" class="btn" style="flex:1;">Voltar ao Cardápio</a>
</div>

<script>
const cartao = document.querySelector('input[value="cartao"]');
const cardForm = document.querySelector('.payment-card');

document.querySelectorAll('input[name="payment_method"]').forEach(el => {
    el.addEventListener('change', () => {
        if(cartao.checked){
            cardForm.classList.add('active');
        } else {
            cardForm.classList.remove('active');
        }
    });
});
</script>

</body>
</html>
