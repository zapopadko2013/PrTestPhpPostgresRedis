<?php

set_time_limit(0);

// --- 1. Настройка и подключение к Redis ---
$redis_url = getenv('REDIS_URL') ?: 'redis://default:VpmXkCxeLayffLqOYkxKJQzvSJRjlEjR@shinkansen.proxy.rlwy.net:23157';

if (empty($redis_url)) {
    http_response_code(500);
    echo json_encode(['error' => 'REDIS_URL is not set']);
    exit;
}

$redis_parts = parse_url($redis_url);

$redis = new Redis();

try {
    $redis->connect($redis_parts['host'], $redis_parts['port']);
    
    if (!empty($redis_parts['pass'])) {
        $redis->auth($redis_parts['pass']);
    }
} catch (RedisException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Redis connection failed: ' . $e->getMessage()]);
    exit;
}

// --- 2. Проверка блокировки ---
if ($redis->get('alpha_running')) {
    echo json_encode(['status' => 'already_running']);
    exit;
}

//$redis->setex('alpha_running', 5, 1); // Устанавливаем блокировку с таймаутом
$redis->set('alpha_running', 1); 

$random_pr = random_int(1, 5);
$random_count = random_int(1, 20);

// --- 3. Настройка и подключение к PostgreSQL ---
$db_url = getenv('DATABASE_URL') ?: 'pgsql://postgres:ZFdzFouKdUYjnnPCbvCFyxtyZxnNtEcQ@postgres.railway.internal:5432/railway';

if (empty($db_url)) {
    http_response_code(500);
    echo json_encode(['error' => 'DATABASE_URL is not set']);
    exit;
}

$db_parts = parse_url($db_url);

try {
    $dsn = sprintf(
        "pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
        $db_parts['host'],
        $db_parts['port'],
        trim($db_parts['path'], '/'),
        $db_parts['user'],
        $db_parts['pass']
    );

    $db = new PDO($dsn, $db_parts['user'], $db_parts['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    $redis->del('alpha_running');
    exit;
}

// --- 4. Обработка запроса ---
try {
    // Безопасная обработка входных данных
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT) ?: $random_pr;
    $quantity   = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?: $random_count;

    $stmt = $db->prepare("INSERT INTO orders (product_id, quantity) VALUES (:product_id, :quantity)");

    $stmt->execute([
        ':product_id' => $product_id,
        ':quantity' => $quantity
    ]);

    echo json_encode(['status' => 'ok', 'product_id' => $product_id, 'quantity' => $quantity]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
} finally {
    // Убираем блокировку
    $redis->del('alpha_running');
}