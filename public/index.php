<?php
session_start();
<<<<<<< HEAD
require_once __DIR__ . '/../src/db.php';
$pdo = getPDO();
=======
require_once __DIR__ . '/../private/database.php';

// Conexão segura
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    die('<h3 style="color:red;">Erro de conexão segura com o banco de dados.</h3>');
}
>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)

// Buscar categorias
$categoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar pratos
$dishesStmt = $pdo->query("SELECT d.*, c.name AS category_name FROM dishes d LEFT JOIN categories c ON d.category_id=c.id");
$dishes = $dishesStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Restaurante Gourmet</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<header>
    <h1>🍽️ Restaurante Gourmet</h1>
    <nav style="display:flex; align-items:center; gap:15px;">
        <?php if(!empty($_SESSION['user_logged'])): ?>
            <!-- Carrinho com contador -->
            <a href="cart.php" style="position:relative; display:inline-block; font-size:24px;">
                🛒
                <span id="cart-count" style="
                    position:absolute;
                    top:-8px;
                    right:-12px;
                    background:red;
                    color:white;
                    font-size:12px;
                    padding:2px 6px;
                    border-radius:50%;
                ">
                    <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] ?? [] as $c) $total += $c['qty'];
                        echo $total;
                    ?>
                </span>
            </a>

            <span>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="logout.php">Sair</a>
        <?php else: ?>
            <a href="login.php">Entrar</a>
            <a href="register.php">Cadastrar</a>
        <?php endif; ?>
    </nav>
</header>

<script>
// Atualiza o contador do carrinho dinamicamente
function updateCartCount() {
    const el = document.getElementById('cart-count');
    if (!el) return;
    let total = 0;

    // Obter carrinho do sessionStorage ou localStorage se quiser dinamicamente
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    cart.forEach(item => { total += item.qty || 0; });

    el.textContent = total;
}

// Exemplo: chamar updateCartCount() sempre que adicionar ao carrinho
// addToCart() { ...; updateCartCount(); }

document.addEventListener('DOMContentLoaded', updateCartCount);
</script>



<main>
    <h2>Cardápio</h2>

    <div id="categories">
<<<<<<< HEAD
        <button data-category="all">Todos</button>
=======
        <button onclick="window.location.reload()">Todos</button>
>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)
        <?php foreach($categories as $cat): ?>
            <button data-category="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></button>
        <?php endforeach; ?>
    </div>

    <div id="menu">
        <?php foreach($dishes as $dish): ?>
            <div class="dish" data-category="<?php echo $dish['category_id']; ?>">
<<<<<<< HEAD
                <!-- Usando image_url do banco -->
                <img src="<?php echo $dish['image_url']; ?>" width="100">
=======
                <img src="<?php echo htmlspecialchars($dish['image_url']); ?>" width="100">
>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)
                <div class="dish-body">
                    <h3><?php echo htmlspecialchars($dish['name']); ?></h3>
                    <p><?php echo htmlspecialchars($dish['description']); ?></p>
                    <div class="price">R$ <?php echo number_format($dish['price'], 2, ',', '.'); ?></div>
                    <form method="post" action="cart.php">
                        <input type="hidden" name="id" value="<?php echo $dish['id']; ?>">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($dish['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo $dish['price']; ?>">
<<<<<<< HEAD
                        <input type="hidden" name="image" value="<?php echo $dish['image_url']; ?>">
=======
                        <input type="hidden" name="image" value="<?php echo htmlspecialchars($dish['image_url']); ?>">
>>>>>>> ee5f96e (Foi alinhado os botões do cards da tela inicial, foi incluido a parte de checkout)
                        <button type="submit" name="add_to_cart" class="btn">Adicionar ao carrinho</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</main>

<script>
// Filtrar por categoria
const buttons = document.querySelectorAll('#categories button');
const dishes = document.querySelectorAll('.dish');
buttons.forEach(btn => {
    btn.addEventListener('click', () => {
        const cat = btn.dataset.category;
        dishes.forEach(d => {
            if(cat === 'all' || d.dataset.category === cat) d.style.display = 'block';
            else d.style.display = 'none';
        });
    });
});
</script>

</body>
</html>
