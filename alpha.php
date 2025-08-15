<?php

set_time_limit(0);

$config = require __DIR__ . '/config.php';

// Получаем переменные окружения, которые предоставляет Railway
$redis_url = getenv('REDIS_URL');

// Разбираем URL для получения хоста, порта и пароля
$redis_host = parse_url($redis_url, PHP_URL_HOST);
$redis_port = parse_url($redis_url, PHP_URL_PORT);
$redis_pass = parse_url($redis_url, PHP_URL_PASS);

$redis = new Redis();

// Подключаемся к хосту и порту
$redis->connect($redis_host, $redis_port);

// Проверяем, есть ли пароль, и если есть, аутентифицируемся
if ($redis_pass) {
    $redis->auth($redis_pass);
}

// Проверка блокировки
 if ($redis->get('alpha_running')) {
    echo json_encode(['status' => 'already_running']);
    exit;
}

// Устанавливаем ключ на 5 секунд, чтобы заблокировать повторный запуск
//$redis->setex('alpha_running', 5, 1); 
$redis->set('alpha_running', 1);

try {
    $db = new PDO(
        "pgsql:host={$config['db']['host']};dbname={$config['db']['dbname']}",
        $config['db']['user'],
        $config['db']['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка подключения к БД: ' . $e->getMessage()]);
    exit;
}

// Получаем данные из запроса
$product_id = $_POST['product_id'] ?? 1; // по умолчанию 1
$quantity   = $_POST['quantity'] ?? 1;   // по умолчанию 1

try {
    $now = date('Y-m-d H:i:s');

    $stmt = $db->prepare("
        INSERT INTO orders (product_id, quantity) 
        VALUES (:product_id, :quantity)
    ");

    $stmt->execute([
        ':product_id' => $product_id,
        ':quantity' => $quantity
    ]);

    echo json_encode(['status' => 'ok']);

} catch (PDOException $e) {
    http_response_code(500);
   // file_put_contents(__DIR__ . '/db_errors.log', $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Убираем блокировку
   $redis->del('alpha_running');
}