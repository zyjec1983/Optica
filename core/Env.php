<?php

/**
 * ==================================================================
 *  ARCHIVO: core/Env.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Núcleo del framework propio: Env.
 * ==================================================================
 */



declare(strict_types=1);

namespace core;

/**
 * Carga las variables del archivo .env a $_ENV (y putenv).
 */
class Env
{
    private array $vars = [];

    public function __construct(string $path)
    {
        if (!is_file($path)) {
            throw new RuntimeException("Archivo de entorno no encontrado: {$path}");
        }
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = array_map('trim', explode('=', $line, 2));
            $value = trim($value, "\"'");
            $this->vars[$key] = $value;
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->vars[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }

    public function bool(string $key, bool $default = false): bool
    {
        $v = strtolower((string)$this->get($key, $default ? 'true' : 'false'));
        return in_array($v, ['1', 'true', 'yes', 'on']);
    }
}