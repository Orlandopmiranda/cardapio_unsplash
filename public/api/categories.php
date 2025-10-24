<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../src/db.php';
$pdo = getPDO();
$stmt = $pdo->query('SELECT id,name FROM categories ORDER BY name');
echo json_encode($stmt->fetchAll());
