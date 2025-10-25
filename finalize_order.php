<?php
session_start();

// Se o método não for POST, redireciona
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /cardapio_unsplash/cart.php');
    exit;
}

// Carrinho
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    $_SESSION['error'] = 'Carrinho vazio.';
    header('Location: /cardapio_unsplash/cart.php');
    exit;
}

// Calcular total do pedido
$total = 0;
foreach ($cart as $item) {
    $price = $item['price'] ?? 0;
    $qty = $item['qty'] ?? 1;
    $total += $price * $qty;
}

// Limpar carrinho
unset($_SESSION['cart']);

// Mensagem de sucesso
$_SESSION['success'] = 'Pedido finalizado com sucesso! Total: R$ ' . number_format($total, 2, ',', '.');

// Redireciona para a página de agradecimento
header('Location: /cardapio_unsplash/thank_you.php');
exit;
