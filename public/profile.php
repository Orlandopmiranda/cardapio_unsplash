<?php
session_start();
require_once __DIR__ . '/../private/database.php';

if (empty($_SESSION['user_logged'])) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

// Buscar endereços do usuário
$stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Área do Cliente</title>
<link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<div class="checkout-container">
    <h2>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>

    <div class="checkout-section">
        <a href="index.php" class="btn">Voltar ao Cardápio</a>
    </div>

    <div class="checkout-section">
        <h3>Meus Endereços</h3>
        <?php if ($addresses): ?>
            <ul>
            <?php foreach ($addresses as $addr): ?>
                <li>
                    <?php 
                        $street = $addr['street'] ?? '';
                        $number = $addr['number'] ?? '';
                        $city = $addr['city'] ?? '';
                        $state = $addr['state'] ?? '';
                        $zip = $addr['zip'] ?? '';
                        echo htmlspecialchars("$street, $number - $city/$state - CEP: $zip"); 
                    ?>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Nenhum endereço cadastrado.</p>
        <?php endif; ?>
    </div>

    <div class="checkout-section">
        <h3>Adicionar Novo Endereço</h3>
        <form method="post" action="add_address.php">
            <input type="text" name="street" placeholder="Rua" class="input-field" required>
            <input type="text" name="number" placeholder="Número" class="input-field" required>
            <input type="text" name="city" placeholder="Cidade" class="input-field" required>
            <input type="text" name="state" placeholder="Estado" class="input-field" required>
            <input type="text" name="zip" placeholder="CEP" class="input-field" required>
            <button type="submit" class="btn">Adicionar Endereço</button>
        </form>
    </div>

    <div class="checkout-section">
        <a href="checkout.php" class="btn">Ir para Pagamento</a>
    </div>
</div>

</body>
</html>
