<?php
if(!isset($_SESSION['user_id'])) exit; // segurança

$stmt = $pdo->prepare("
    INSERT INTO user_addresses (user_id, street, number, city, state, zip)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([
    $_SESSION['user_id'],
    $_POST['street'],
    $_POST['number'],
    $_POST['city'],
    $_POST['state'],
    $_POST['zip']
]);

$_SESSION['success'] = 'Endereço adicionado!';
header('Location: checkout.php'); // volta para a página de checkout
exit;
