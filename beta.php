<?php

$config = require __DIR__ . '/config.php';

// Функция проверки, есть ли обе таблицы
function dbHasTables(PDO $pdo, array $tables): bool {
    $placeholders = str_repeat('?,', count($tables) - 1) . '?';
    $sql = "SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
              AND table_name IN ($placeholders)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($tables);
    $foundTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return count($foundTables) === count($tables);
}

try {
    // Подключаемся к базе
    $pdo = new PDO(
        "pgsql:host={$config['db']['host']};dbname={$config['db']['dbname']}",
        $config['db']['user'],
        $config['db']['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Проверка наличия таблиц
    if (!dbHasTables($pdo, ['categories', 'products'])) {
        echo "Таблицы не найдены, выполняем init_db.sql...\n";

        $sql = file_get_contents(__DIR__ . '/init_db.sql');
        $pdo->exec($sql);

        echo "init_db.sql успешно выполнен.\n";
    } else {
        echo "База уже инициализирована, таблицы существуют.\n";
    }

} catch (PDOException $e) {
    echo "Ошибка подключения или инициализации БД: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// Параметр n
$n = isset($_GET['n']) ? intval($_GET['n']) : 10;

// Запускаем alpha.php n раз
for ($i = 0; $i < $n; $i++) {
    include __DIR__ . '/alpha.php';
}

echo "Запущено $n заказов.\n";
