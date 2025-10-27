<?php
session_start();

require_once __DIR__ . '/../src/db.php';
$pdo = getPDO();

require_once __DIR__ . '/../private/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    die('<h3 style="color:red;">Erro de conexão segura com o banco de dados.</h3>');
}
>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)

// Inicializar carrinho se não existir
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ============================
// Adicionar item ao carrinho
// ============================
if (isset($_POST['add_to_cart'])) {
    $dishId = $_POST['id'];
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $image = $_POST['image'];

    if (!empty($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $stmt = $pdo->prepare("
            INSERT INTO cart_items (user_id, dish_id, quantity) 
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE quantity = quantity + 1
        ");
        $stmt->execute([$userId, $dishId]);
    } else {
        $found = false;
        foreach ($_SESSION['cart'] as &$cartItem) {
            if ($cartItem['id'] == $dishId) {
                $cartItem['qty']++;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $dishId,
                'name' => $name,
                'price' => $price,
                'image' => $image,
                'qty' => 1
            ];
        }
    }

    header('Location: cart.php');
=======
require_once __DIR__ . '/../private/Database.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
>>>>>>> ecc5550 (foi atualizado a parte de pagamento)
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

// Remover item do carrinho
if (isset($_GET['remove_id'])) {
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id=? AND dish_id=?");
    $stmt->execute([$_SESSION['user_id'], $_GET['remove_id']]);
    header('Location: cart.php');
    exit;
}

// Pegar itens do carrinho
$stmt = $pdo->prepare("SELECT ci.quantity, d.id, d.name, d.price, d.image_url
                       FROM cart_items ci 
                       JOIN dishes d ON ci.dish_id = d.id 
                       WHERE ci.user_id=?");
$stmt->execute([$_SESSION['user_id']]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular total
$total = 0;
foreach($cart as $item) $total += $item['price'] * $item['quantity'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Carrinho</title>
<link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<header>
    <h1>🛒 Seu Carrinho</h1>
    <a href="index.php" class="btn">Voltar ao Cardápio</a>
</header>

<<<<<<< HEAD
    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <?php if (!empty($_SESSION['success'])): ?>
        <p style="color:green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <p>Seu carrinho está vazio.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Imagem</th>
                <th>Produto</th>
                <th>Preço</th>
                <th>Qtd</th>
                <th>Total</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($cart as $item): ?>
                <tr>
                    <td>
                        <img src="<?php echo !empty($item['image']) ? htmlspecialchars($item['image']) : 'assets/img/no-image.png'; ?>" width="100">
                    </td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                    <td><?php echo $item['quantity'] ?? $item['qty']; ?></td>
                    <td>R$ <?php echo number_format($item['price'] * ($item['quantity'] ?? $item['qty']), 2, ',', '.'); ?></td>
<<<<<<< HEAD
                    <td><a href="cart.php?remove=<?php echo $item['id']; ?>">Remover</a></td>
=======
                    <td>
                        <a href="cart.php?remove=<?php echo $item['id']; ?>" class="remove-btn" title="Remover">
                            🗑️
                        </a>
                    </td>

>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Total Geral: R$ <?php echo number_format($total, 2, ',', '.'); ?></h3>

<<<<<<< HEAD
        <form action="cart.php" method="post">
            <button type="submit" name="finalizar" class="btn">Finalizar Pedido</button>
=======
        <form action="checkout.php" method="get">
        <button type="submit" class="btn">Ir para Pagamento</button>
>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)
        </form>
    <?php endif; ?>

    <br>
    <a href="index.php">Voltar ao cardápio</a>
</body>
</html>
<<<<<<< HEAD
=======


>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)
=======
<?php if(empty($cart)): ?>
    <p>Seu carrinho está vazio.</p>
<?php else: ?>
<table>
    <tr>
        <th>Imagem</th>
        <th>Produto</th>
        <th>Quantidade</th>
        <th>Preço</th>
        <th>Subtotal</th>
        <th>Remover</th>
    </tr>
    <?php foreach($cart as $item): ?>
    <tr>
        <td><img src="<?php echo $item['image_url'] ?: 'assets/img/no-image.png'; ?>" width="60"></td>
        <td><?php echo htmlspecialchars($item['name']); ?></td>
        <td><?php echo $item['quantity']; ?></td>
        <td>R$ <?php echo number_format($item['price'],2,',','.'); ?></td>
        <td>R$ <?php echo number_format($item['price']*$item['quantity'],2,',','.'); ?></td>
        <td><a href="cart.php?remove_id=<?php echo $item['id']; ?>" class="remove-btn">🗑️</a></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="4" style="text-align:right;font-weight:bold;">Total:</td>
        <td colspan="2" style="font-weight:bold;">R$ <?php echo number_format($total,2,',','.'); ?></td>
    </tr>
</table>
<a href="checkout.php" class="btn">Finalizar Pedido</a>
<?php endif; ?>
</body>
</html>
>>>>>>> ecc5550 (foi atualizado a parte de pagamento)
