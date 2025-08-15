<?php

////

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

    // Проверка наличия таблиц
    if (!dbHasTables($pdo, ['categories', 'products'])) {
        echo "Таблицы не найдены, выполняем init_db.sql...\n";

        $cmd = sprintf(
            'psql -h %s -U %s -d %s -f %s',
            escapeshellarg($config['db']['host']),
            escapeshellarg($config['db']['user']),
            escapeshellarg($config['db']['dbname']),
            escapeshellarg(__DIR__ . '/init_db.sql')
        );

        putenv("PGPASSWORD={$config['db']['password']}");

        system($cmd, $retval);

        if ($retval === 0) {
            echo "init_db.sql успешно выполнен.\n";
        } else {
            echo "Ошибка выполнения init_db.sql.\n";
        }
    } else {
        echo "База уже инициализирована, таблицы существуют.\n";
    }

} catch (PDOException $e) {
    echo "Ошибка подключения к базе: " . $e->getMessage() . "\n";
}
///////

$n = isset($_GET['n']) ? intval($_GET['n']) : 10;

for ($i = 0; $i < $n; $i++) {
     include __DIR__ . '/alpha.php';
}

echo "Запущено $n заказов.\n";
