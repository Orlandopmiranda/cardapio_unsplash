<?php
session_start();
require_once __DIR__ . '/../src/db.php';
$pdo = getPDO();

// Inicializar carrinho da sessão se necessário
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// =======================
//  Adicionar item ao carrinho
// =======================
if (isset($_POST['add_to_cart'])) {
    $dishId = $_POST['id'];
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $image = $_POST['image'];

    if (!empty($_SESSION['user_id'])) {
        // Usuário logado → salvar no banco
        $userId = $_SESSION['user_id'];
        $stmt = $pdo->prepare("
            INSERT INTO cart_items (user_id, dish_id, quantity) 
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE quantity = quantity + 1
        ");
        $stmt->execute([$userId, $dishId]);
    } else {
        // Usuário visitante → salvar na sessão
        $item = ['id'=>$dishId,'name'=>$name,'price'=>$price,'image'=>$image,'qty'=>1];
        $found = false;
        foreach($_SESSION['cart'] as &$cartItem){
            if($cartItem['id'] == $dishId){ $cartItem['qty']++; $found=true; break; }
        }
        if(!$found) $_SESSION['cart'][] = $item;
    }

    header('Location: cart.php');
    exit;
}

// =======================
//  Remover item do carrinho
// =======================
if (isset($_GET['remove'])) {
    $dishId = $_GET['remove'];

    if (!empty($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND dish_id = ?");
        $stmt->execute([$userId, $dishId]);
    } else {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($i) => $i['id'] != $dishId);
    }

    header('Location: cart.php');
    exit;
}

// =======================
//  Finalizar pedido
// =======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    $cart = [];

    if (!empty($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $stmt = $pdo->prepare("
            SELECT ci.quantity as qty, d.*
            FROM cart_items ci
            JOIN dishes d ON d.id = ci.dish_id
            WHERE ci.user_id = ?
        ");
        $stmt->execute([$userId]);
        $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $cart = $_SESSION['cart'] ?? [];
    }

    if (empty($cart)) {
        $_SESSION['error'] = 'Carrinho vazio.';
        header('Location: cart.php');
        exit;
    }

    // Calcular total
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * ($item['qty'] ?? 1);
    }

    // Limpar carrinho
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } else {
        unset($_SESSION['cart']);
    }

    $_SESSION['success'] = 'Pedido finalizado com sucesso! Total: R$ ' . number_format($total, 2, ',', '.');
    header('Location: thank_you.php');
    exit;
}

// =======================
//  Ler carrinho para exibir
// =======================
$cart = [];
if (!empty($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        SELECT ci.quantity as qty, d.*
        FROM cart_items ci
        JOIN dishes d ON d.id = ci.dish_id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$userId]);
    $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $cart = $_SESSION['cart'] ?? [];
}

// Calcular total
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * ($item['qty'] ?? 1);
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Carrinho</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h2>Carrinho de Compras</h2>

    <!-- Mensagens -->
    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <p style="color:green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <!-- Carrinho -->
    <?php if (empty($cart)): ?>
        <p>Seu carrinho está vazio.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
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
                    <td><img src="<?php echo htmlspecialchars($item['image'] ?? $item['image_url']); ?>" width="100"></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>R$ <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                    <td><?php echo $item['qty']; ?></td>
                    <td>R$ <?php echo number_format($item['price'] * $item['qty'], 2, ',', '.'); ?></td>
                    <td><a href="cart.php?remove=<?php echo $item['id']; ?>">Remover</a></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h3>Total Geral: R$ <?php echo number_format($total, 2, ',', '.'); ?></h3>

        <form action="cart.php" method="post">
            <button type="submit" name="finalizar" class="btn btn-primary">Finalizar Pedido</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="index.php">Voltar ao cardápio</a>
</body>
</html>
