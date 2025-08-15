<?php
return [
    'db' => [
        'host' => getenv('PGHOST'),
        'dbname' => getenv('PGDATABASE'),
        'user' => getenv('PGUSER'),
        'password' => getenv('PGPASSWORD')
    ],
    'redis' => [
        'host' => getenv('REDIS_HOST'),
        'port' => getenv('REDIS_PORT')
    ]
];
