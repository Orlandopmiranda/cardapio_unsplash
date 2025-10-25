<?php
session_start();
require_once __DIR__ . '/../src/db.php';
$pdo = getPDO();

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
    <nav>
        <a href="cart.php">Carrinho (<?php
            if (!empty($_SESSION['user_id'])) {
                $stmt = $pdo->prepare("SELECT SUM(quantity) AS total FROM cart_items WHERE user_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $total = $stmt->fetchColumn();
                echo $total ?: 0;
            } else {
                $total = 0;
                foreach ($_SESSION['cart'] ?? [] as $c) $total += $c['qty'];
                echo $total;
            }
        ?>)</a>
        <?php if(!empty($_SESSION['user_logged'])): ?>
            <span>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="logout.php">Sair</a>
        <?php else: ?>
            <a href="login.php">Entrar</a>
            <a href="register.php">Cadastrar</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <h2>Cardápio</h2>

    <div id="categories">
        <button data-category="all">Todos</button>
        <?php foreach($categories as $cat): ?>
            <button data-category="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></button>
        <?php endforeach; ?>
    </div>

    <div id="menu">
        <?php foreach($dishes as $dish): ?>
            <div class="dish" data-category="<?php echo $dish['category_id']; ?>">
                <!-- Usando image_url do banco -->
                <img src="<?php echo $dish['image_url']; ?>" width="100">
                <div class="dish-body">
                    <h3><?php echo htmlspecialchars($dish['name']); ?></h3>
                    <p><?php echo htmlspecialchars($dish['description']); ?></p>
                    <div class="price">R$ <?php echo number_format($dish['price'], 2, ',', '.'); ?></div>
                    <form method="post" action="cart.php">
                        <input type="hidden" name="id" value="<?php echo $dish['id']; ?>">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($dish['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo $dish['price']; ?>">
                        <input type="hidden" name="image" value="<?php echo $dish['image_url']; ?>">
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
