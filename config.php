<?php
$dbUrl = getenv('DATABASE_URL');
$redisUrl = getenv('REDIS_URL');
$dbParts = parse_url($dbUrl);
$redisParts = parse_url($redisUrl);

return [
    'db' => [
        'host'     => $dbParts['host'],
        'dbname'   => trim($dbParts['path'], '/'),
        'user'     => $dbParts['user'],
        'password' => $dbParts['pass'],
        'port'     => $dbParts['port']
    ],
    'redis' => [
        'host' => $redisParts['host'],
        'port' => $redisParts['port'],
        'pass' => $redisParts['pass']
    ]
];