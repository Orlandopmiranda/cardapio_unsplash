<?php
session_start();
<<<<<<< HEAD
require_once __DIR__ . '/../src/db.php';
$pdo = getPDO();
=======
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
    exit;
}

// ============================
// Remover item do carrinho
// ============================
if (isset($_GET['remove'])) {
    $dishId = $_GET['remove'];
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND dish_id = ?");
        $stmt->execute([$_SESSION['user_id'], $dishId]);
    } else {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($i) => $i['id'] != $dishId);
    }
    header('Location: cart.php');
    exit;
}

// ============================
// Finalizar pedido
// ============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    $cart = [];

    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("
            SELECT ci.quantity, d.id, d.name, d.price, d.image_url AS image
            FROM cart_items ci
            JOIN dishes d ON d.id = ci.dish_id
            WHERE ci.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        $cart = $_SESSION['cart'] ?? [];
        unset($_SESSION['cart']);
    }

    if (empty($cart)) {
        $_SESSION['error'] = 'Carrinho vazio.';
        header('Location: cart.php');
        exit;
    }

    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * ($item['quantity'] ?? $item['qty']);
    }

    $_SESSION['success'] = 'Pedido finalizado com sucesso! Total: R$ ' . number_format($total, 2, ',', '.');
    header('Location: thank_you.php');
    exit;
}

// ============================
// Preparar carrinho para exibir
// ============================
$cart = [];
if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT ci.quantity, d.id, d.name, d.price, d.image_url AS image
        FROM cart_items ci
        JOIN dishes d ON d.id = ci.dish_id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $cart = $_SESSION['cart'] ?? [];
}

$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * ($item['quantity'] ?? $item['qty']);
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Carrinho</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { color: darkred; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: darkred; color: white; }
        td img { border-radius: 8px; }
        .btn { padding: 8px 12px; background-color: darkred; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background-color: red; }
        a { color: darkred; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Carrinho de Compras</h2>

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
