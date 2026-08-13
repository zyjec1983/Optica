<?php

/**
 * ==================================================================
 *  ARCHIVO: config/app.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Configuración central del proyecto (portable vía .env).
 * ==================================================================
 */

declare(strict_types=1);

use core\Env;

/**
 * Punto único de configuración del proyecto.
 *
 * Todas las variables (URI, base de datos, SRI) se definen únicamente
 * en el archivo `.env`. Para portar la aplicación a producción solo
 * debe editar `.env` y ejecutar `php database/migrate.php` en el destino.
 *
 * Cargamos el archivo .env una sola vez; a partir de aquí los valores
 * están disponibles también vía putenv/getenv (usados por helpers como
 * app_url() y por core\Request::uri()).
 */
$env = new Env(dirname(__DIR__) . '/.env');

return [
    'app' => [
        'name'  => $env->get('APP_NAME', 'Opticenter'),
        'env'   => $env->get('APP_ENV', 'production'),
        'debug' => $env->bool('APP_DEBUG', false),
        'url'   => rtrim($env->get('APP_URL', ''), '/'),
    ],
    'database' => [
        'host'    => $env->get('DB_HOST', '127.0.0.1'),
        'port'    => $env->get('DB_PORT', '3306'),
        'name'    => $env->get('DB_NAME', 'optica_db'),
        'user'    => $env->get('DB_USER', 'root'),
        'pass'    => $env->get('DB_PASS', ''),
        'charset' => 'utf8mb4',
    ],
    'sri' => [
        'mode'           => $env->get('SRI_MODE', 'test'),
        'establecimiento' => $env->get('SRI_PLAZA', ''),
        'punto_emision'  => $env->get('SRI_EMISSION_POINT', ''),
    ],
];