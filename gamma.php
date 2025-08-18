<?php
$config = require __DIR__ . '/config.php';
require __DIR__ . '/db_utils.php';

try {

    $db_url = getenv('DATABASE_URL') ?: 'pgsql://postgres:ZFdzFouKdUYjnnPCbvCFyxtyZxnNtEcQ@postgres.railway.internal:5432/railway';
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

    $db = new PDO(
        "$scheme:host=$host;port=$port;dbname=$dbname",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // Проверяем наличие таблицы orders
    if (!dbHasTables($db, ['orders'])) {
        echo json_encode(['error' => 'Таблица orders не найдена']);
        exit;
    }

    $sql = '
        SELECT 
    MIN(created_at) as "Время начала", 
    MAX(created_at) as "Время окончания", 
    COUNT(*) as "Записей", 
    SUM(quantity) as "Товаров", 
    name as "Категория"
    FROM (
    SELECT 
        o.created_at, 
        o.quantity, 
        c.name
    FROM orders o
    INNER JOIN products p ON o.product_id = p.id
    INNER JOIN categories c ON p.category_id = c.id
    WHERE o.created_at IS NOT NULL
    ORDER BY o.created_at DESC
    LIMIT 100
    ) t
    GROUP BY name
    ORDER BY name;
    ';

    $stmt = $db->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}