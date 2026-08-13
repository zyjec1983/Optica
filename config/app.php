<?php

declare(strict_types=1);

use core\Env;

$env = new Env(dirname(__DIR__) . '/.env');

return [
    'app' => [
        'name'  => $env->get('APP_NAME', 'Opticenter'),
        'env'   => $env->get('APP_ENV', 'production'),
        'debug' => $env->bool('APP_DEBUG', false),
        'url'   => rtrim($env->get('APP_URL', ''), '/'),
    ],
    'database' => [
        'host' => $env->get('DB_HOST', '127.0.0.1'),
        'port' => $env->get('DB_PORT', '3306'),
        'name' => $env->get('DB_NAME', 'optica_db'),
        'user' => $env->get('DB_USER', 'root'),
        'pass' => $env->get('DB_PASS', ''),
        'charset' => 'utf8mb4',
    ],
    'sri' => [
        'mode' => $env->get('SRI_MODE', 'test'),
    ],
];