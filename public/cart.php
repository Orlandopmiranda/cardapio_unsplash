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

// Inicializar carrinho local caso usuário não esteja logado
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
    exit;
}

// Se não estiver logado, mostrar carrinho da sessão
if (empty($_SESSION['user_id'])) {
    $cart = $_SESSION['cart'];
} else {
    $stmt = $pdo->prepare("SELECT ci.quantity, d.id, d.name, d.price, d.image_url
                            FROM cart_items ci 
                            JOIN dishes d ON ci.dish_id = d.id 
                            WHERE ci.user_id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Remover item
if (isset($_GET['remove_id'])) {
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id=? AND dish_id=?");
        $stmt->execute([$_SESSION['user_id'], $_GET['remove_id']]);
    } else {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) {
            return $item['id'] != $_GET['remove_id'];
        });
    }
    header('Location: cart.php');
    exit;
}

// Calcular total
$total = 0;
foreach ($cart as $item) {
    $qty = $item['quantity'] ?? $item['qty'];
    $total += $item['price'] * $qty;
}
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

<?php if (empty($cart)): ?>
    <p>Seu carrinho está vazio.</p>
<?php else: ?>
<table>
    <tr>
        <th>Imagem</th>
        <th>Produto</th>
        <th>Qtd</th>
        <th>Preço</th>
        <th>Subtotal</th>
        <th>Remover</th>
    </tr>

    <?php foreach ($cart as $item): 
        $qty = $item['quantity'] ?? $item['qty'];
        ?>
        <tr>
            <td><img src="<?php echo $item['image_url'] ?? $item['image'] ?? 'assets/img/no-image.png'; ?>" width="80"></td>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo $qty; ?></td>
            <td>R$ <?php echo number_format($item['price'],2,',','.'); ?></td>
            <td>R$ <?php echo number_format($item['price'] * $qty,2,',','.'); ?></td>
            <td><a href="cart.php?remove_id=<?php echo $item['id']; ?>">🗑️</a></td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="4" style="text-align:right;font-weight:bold;">Total:</td>
        <td colspan="2" style="font-weight:bold;">R$ <?php echo number_format($total,2,',','.'); ?></td>
    </tr>
</table>

<br>
<a class="btn" href="checkout.php">Ir para Pagamento</a>
<?php endif; ?>

<br><br>
<a href="index.php">Continuar comprando</a>

</body>
</html>
