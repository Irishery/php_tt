<?php
require_once __DIR__ . '/Core/Database.php';

$pdo = Database::connect();
echo'ASDASD';

// Создаём таблицу для отслеживания миграций
$pdo->exec("CREATE TABLE IF NOT EXISTS migrations (name VARCHAR(255) PRIMARY KEY, migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

$applied = $pdo->query("SELECT name FROM migrations")->fetchAll(PDO::FETCH_COLUMN) ?: [];

$files = glob(__DIR__ . '/migrations/*.sql');
sort($files);

foreach ($files as $file) {
    $name = basename($file);
    if (in_array($name, $applied)) {
        echo "✅ Пропущено: $name\n";
        continue;
    }

    echo "🚀 Применяется миграция: $name\n";
    $sql = file_get_contents($file);
    $pdo->exec($sql);
    $stmt = $pdo->prepare("INSERT INTO migrations (name) VALUES (?)");
    $stmt->execute([$name]);
}

echo "🎉 Все миграции применены.\n";
