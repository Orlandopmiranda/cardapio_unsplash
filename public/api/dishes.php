<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../src/db.php';
$cat = isset($_GET['category']) ? intval($_GET['category']) : 0;
$pdo = getPDO();
if($cat){
  $stmt = $pdo->prepare('SELECT d.*, c.name as category_name FROM dishes d LEFT JOIN categories c ON d.category_id=c.id WHERE category_id=? ORDER BY d.created_at DESC');
  $stmt->execute([$cat]);
} else {
  $stmt = $pdo->query('SELECT d.*, c.name as category_name FROM dishes d LEFT JOIN categories c ON d.category_id=c.id ORDER BY d.created_at DESC');
}
echo json_encode($stmt->fetchAll());
