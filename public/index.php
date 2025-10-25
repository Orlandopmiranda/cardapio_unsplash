<?php
session_start();
require_once __DIR__ . '/../src/db.php';



$pdo = getPDO();

// Buscar categorias
$stmt = $pdo->query("SELECT DISTINCT c.id, c.name 
                     FROM categories c
                     JOIN dishes d ON d.category_id = c.id
                     ORDER BY c.name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);




// Buscar pratos
// Se categoria selecionada
$category_id = $_GET['category'] ?? 0;
if ($category_id) {
    $stmt = $pdo->prepare("SELECT * FROM dishes WHERE category_id = ?");
    $stmt->execute([$category_id]);
} else {
    $stmt = $pdo->query("SELECT * FROM dishes");
}
$dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante - Cardápio</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <div class="logo">
        <h1>Restaurante Gourmet</h1>
    </div>
    <nav>
        <a href="cart.php">Carrinho (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
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
    <section class="hero">
        <h2>Nosso Cardápio</h2>
        <p>Escolha os melhores pratos da nossa cozinha</p>
        <div id="categories">
    <a href="index.php"><button>Todas</button></a>
    <?php foreach ($categories as $cat): ?>
        <a href="index.php?category=<?php echo $cat['id']; ?>">
            <button><?php echo htmlspecialchars($cat['name']); ?></button>
        </a>
    <?php endforeach; ?>
</div>

    </section>

    <section id="menu" class="menu-grid">
        <?php foreach($dishes as $dish): ?>
            <div class="dish-card">
                <!--<img src="<?php echo htmlspecialchars($dish['image']); ?>" alt="<?php echo htmlspecialchars($dish['name']); ?>">-->
                <div class="dish-info">
                    <h3><?php echo htmlspecialchars($dish['name']); ?></h3>
                    <span class="price">R$ <?php echo number_format($dish['price'], 2, ',', '.'); ?></span>
                    <form method="post" action="cart.php">
                        <input type="hidden" name="id" value="<?php echo $dish['id']; ?>">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($dish['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo $dish['price']; ?>">
                        <!--<input type="hidden" name="image" value="<?php echo htmlspecialchars($dish['image']); ?>">-->
                        <button type="submit" name="add_to_cart" class="btn">Adicionar ao carrinho</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
</main>
</body>
</html>

