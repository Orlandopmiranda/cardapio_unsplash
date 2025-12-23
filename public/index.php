<?php
session_start();

require_once __DIR__ . '/../src/db.php';
$pdo = getPDO();

require_once __DIR__ . '/../private/database.php';

// Conexão segura
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    die('<h3 style="color:red;">Erro de conexão segura com o banco de dados.</h3>');
}

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
<?php

// Contador do carrinho (só se logado)
$cartCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total FROM cart_items WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cartCount = (int) $stmt->fetchColumn();
}
?>
<header>
  <h1>🍝 Restaurante Gourmet</h1>
  <nav>
    <a href="index.php">Início</a>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="profile.php">Perfil</a>
      <a href="checkout.php" class="cart-icon">
        <span class="cart-count"><?= $cartCount ?></span>
      </a>
      <a href="logout.php" style="color:#e63946;">Sair</a>
    <?php else: ?>
      <a href="login.php" class="btn">Entrar</a>
    <?php endif; ?>
  </nav>
</header>

<main>
    <h2>Cardápio</h2>

    <div id="categories">
        <button data-category="all">Todos</button>

        <?php foreach($categories as $cat): ?>
            <button data-category="<?php echo $cat['id']; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </button>
        <?php endforeach; ?>
    </div>

    <div id="menu">
        <?php foreach($dishes as $dish): ?>
            <div class="dish" data-category="<?php echo $dish['category_id']; ?>">
                
                <img src="<?php echo htmlspecialchars($dish['image_url']); ?>" width="100">

                <div class="dish-body">
                    <h3><?php echo htmlspecialchars($dish['name']); ?></h3>
                    <p><?php echo htmlspecialchars($dish['description']); ?></p>
                    <div class="price">R$ <?php echo number_format($dish['price'], 2, ',', '.'); ?></div>

                    <form method="post" action="cart.php">
                        <input type="hidden" name="id" value="<?php echo $dish['id']; ?>">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($dish['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo $dish['price']; ?>">
                        <input type="hidden" name="image" value="<?php echo htmlspecialchars($dish['image_url']); ?>">
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
            d.style.display = (cat === 'all' || d.dataset.category === cat)
                ? 'block'
                : 'none';
        });
    });
});
</script>

</body>
</html>
