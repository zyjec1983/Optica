<?php

declare(strict_types=1);

namespace core;

final class Request
{
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // Elimina el prefijo base configurado en APP_URL (ej. /optica)
        $base = parse_url((string)getenv('APP_URL'), PHP_URL_PATH) ?: '';
        if ($base !== '' && $base !== '/' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        return rtrim($path, '/') ?: '/';
    }

    public function query(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY) ?? '';
    }
}