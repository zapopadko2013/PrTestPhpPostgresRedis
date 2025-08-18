<?php

$config = require __DIR__ . '/config.php';
require __DIR__ . '/db_utils.php';

try {

    $db_url = getenv('DATABASE_URL') ?: 'postgresql://postgres:ZFdzFouKdUYjnnPCbvCFyxtyZxnNtEcQ@postgres.railway.internal:5432/railway';
    if ($db_url === false) {
        die("Ошибка: переменная окружения DATABASE_URL не найдена\n");
    }

    $parsed = parse_url($db_url);
    if ($parsed === false) {
      die("Ошибка: не удалось разобрать DATABASE_URL\n");
    }

    $scheme = $parsed['scheme']; // postgres
    $host   = $parsed['host'] ?? 'localhost';
    $port   = $parsed['port'] ?? 5432;
    $user   = $parsed['user'] ?? null;
    $pass   = $parsed['pass'] ?? null;
    $dbname = ltrim($parsed['path'], '/'); // /dbname → dbname

    $pdo = new PDO(
        "$scheme:host=$host;port=$port;dbname=$dbname",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    if (!dbHasTables($pdo, ['categories', 'products'])) {
        echo "Таблицы не найдены, выполняем init_db.sql...\n";

        try {
            $sql = file_get_contents(__DIR__ . '/init_db.sql');
            if ($sql === false) {
                throw new RuntimeException("Не удалось прочитать init_db.sql");
            }
            $pdo->exec($sql);
            echo "init_db.sql успешно выполнен.\n";
        } catch (PDOException $e) {
            echo "Ошибка выполнения init_db.sql: " . $e->getMessage() . "\n";
        } catch (RuntimeException $e) {
            echo "Ошибка: " . $e->getMessage() . "\n";
        }
    } else {
        echo "База уже инициализирована, таблицы существуют.\n";
    }
} catch (PDOException $e) {
    echo "Ошибка подключения к базе: " . $e->getMessage() . "\n";
}

$n = isset($_GET['n']) ? intval($_GET['n']) : 10;
for ($i = 0; $i < $n; $i++) {
     include __DIR__ . '/alpha.php';
}
echo "Запущено $n заказов.\n";