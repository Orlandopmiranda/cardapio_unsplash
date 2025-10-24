<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../src/db.php';
$input = json_decode(file_get_contents('php://input'), true);
$name = $input['name'] ?? null;
$phone = $input['phone'] ?? null;
$cart = $input['cart'] ?? [];
if(!$cart || !$name){ echo json_encode(['error'=>'Dados incompletos']); exit; }
$pdo = getPDO();
$amount = 0;
foreach($cart as $c){ $amount += ($c['price'] * $c['qty']); }
$items_json = json_encode($cart, JSON_UNESCAPED_UNICODE);
$stmt = $pdo->prepare('INSERT INTO orders (user_name,user_phone,items_json,amount,status) VALUES (?,?,?,?,?)');
$stmt->execute([$name,$phone,$items_json,$amount,'pending']);
$orderId = $pdo->lastInsertId();
echo json_encode(['ok'=>true,'orderId'=>$orderId,'amount'=>$amount]);
