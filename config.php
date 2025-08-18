<?php

$databaseUrl = getenv('DATABASE_URL') ?: 'postgresql://postgres:ZFdzFouKdUYjnnPCbvCFyxtyZxnNtEcQ@postgres.railway.internal:5432/railway';
$redisUrl = getenv('REDIS_URL') ?: 'redis://default:VpmXkCxeLayffLqOYkxKJQzvSJRjlEjR@shinkansen.proxy.rlwy.net:23157';;

// Добавляем проверки на существование переменных
if (empty($databaseUrl)) {
    throw new Exception("Переменная окружения DATABASE_URL не установлена.");
}
if (empty($redisUrl)) {
    throw new Exception("Переменная окружения REDIS_URL не установлена.");
}

$dbParts = parse_url($databaseUrl);
$redisParts = parse_url($redisUrl);

// Используем оператор объединения с null для безопасного доступа к ключам
return [
    'db' => [
        'host'     => $dbParts['host']     ?? 'localhost',
        'dbname'   => trim($dbParts['path'], '/') ?? '',
        'user'     => $dbParts['user']     ?? 'postgres',
        'password' => $dbParts['pass']     ?? '',
        'port'     => $dbParts['port']     ?? 5432
    ],
    'redis' => [
        'host' => $redisParts['host'] ?? 'localhost',
        'port' => $redisParts['port'] ?? 6379,
        'pass' => $redisParts['pass'] ?? null
    ]
];