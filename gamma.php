<?php
$config = require __DIR__ . '/config.php';

try {
    $db = new PDO(
        "pgsql:host={$config['db']['host']};dbname={$config['db']['dbname']}",
        $config['db']['user'],
        $config['db']['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Получаем последние 100 заказов с непустыми датами
    $sql = "SELECT created_at 
            FROM orders
            WHERE created_at IS NOT NULL
            ORDER BY created_at DESC
            LIMIT 100";

    $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $timestamps = [];
    foreach ($rows as $row) {
        if (!empty($row['created_at'])) {
            $timestamps[] = strtotime($row['created_at']);
        }
    }

    if (empty($timestamps)) {
        echo json_encode(['error' => 'Нет данных для расчёта']);
        exit;
    }

    // Находим минимальное и максимальное время
    $min_time = min($timestamps);
    $max_time = max($timestamps);

    echo json_encode([
        'min_purchase' => date('Y-m-d H:i:s', $min_time),
        'max_purchase' => date('Y-m-d H:i:s', $max_time),
        'total_orders' => count($timestamps)
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Ошибка подключения к БД: ' . $e->getMessage()]);
    exit;
}
