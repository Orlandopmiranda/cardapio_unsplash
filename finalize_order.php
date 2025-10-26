<?php
<<<<<<< HEAD
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
=======
require_once __DIR__ . '/../private/Database.php';
require_once __DIR__ . '/../src/models/Cart.php';
require_once __DIR__ . '/../src/models/Order.php';

session_start();
if(!isset($_SESSION['usuario'])){
    header('Location: login.php');
    exit;
}

$db = new Database();
$pdo = $db->getConnection();
$cart = $_SESSION['cart'] ?? null;

if(!$cart){
    echo "Carrinho vazio!";
    exit;
}

// Supondo que usuário tenha ID fixo ou você pegue do login
$userId = 1; // Substituir pelo ID do usuário logado
$order = new Order($pdo, $userId);

foreach($cart->getItems() as $prodId => $qtd){
    $order->addItem($prodId, $qtd);
}

if($order->finalize()){
    unset($_SESSION['cart']);
    echo "<p>Pedido finalizado com sucesso!</p>";
    echo "<a href='index.php'>Voltar ao Cardápio</a>";
} else {
    echo "<p>Erro ao finalizar pedido.</p>";
}
>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)
