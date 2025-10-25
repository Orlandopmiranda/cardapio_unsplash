<?php
require_once __DIR__ . '/../src/db.php'; // Ajuste o caminho conforme seu projeto
$pdo = getPDO();

// Criar pasta imagens se não existir
$imagesDir = __DIR__ . '/../imagens';
if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0755, true);
}

// Buscar todos os pratos
$stmt = $pdo->query("SELECT id, image_url FROM dishes");
$dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($dishes as $dish) {
    $url = $dish['image_url']; // Link do Unsplash
    $localPath = $imagesDir . '/dish_' . $dish['id'] . '.jpg';

    echo "⬇️ Baixando $url ... ";

    // Baixar a imagem
    $imageData = @file_get_contents($url);
    if ($imageData === false) {
        echo "❌ Erro ao baixar: $url\n";
        continue;
    }

    // Salvar localmente
    if (file_put_contents($localPath, $imageData)) {
        echo "✅ Salvo em: $localPath\n";
    } else {
        echo "❌ Erro ao salvar: $localPath\n";
    }
}

echo "\n✅ Processo concluído!\n";
