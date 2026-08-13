<?php

/**
 * ==================================================================
 *  ARCHIVO: core/Database.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Núcleo del framework propio: Database.
 * ==================================================================
 */



declare(strict_types=1);

namespace core;

use PDO;

/**
 * Conexión única (singleton) a MySQL mediante PDO.
 *
 * Los datos de conexión provienen de config/app.php (que lee el .env),
 * por lo que al cambiar el .env la conexión usa los nuevos valores sin
 * modificar código.
 */
final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $config = require dirname(__DIR__) . '/config/app.php';
            $db = $config['database'];
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $db['host'],
                $db['port'],
                $db['name'],
                $db['charset']
            );
            self::$connection = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$connection;
    }
}