<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Helpers/helpers.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Helpers / funciones globales reutilizables.
 * ==================================================================
 */



declare(strict_types=1);

use core\Database;

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return dirname(__DIR__, 2) . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        return base_path('public') . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage') . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('app_url')) {
    function app_url(string $path = ''): string
    {
        $url = env('APP_URL', '');
        return $url . ($path !== '' && !str_starts_with($path, '/') ? '/' . $path : $path);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }
}

if (!function_exists('abort')) {
    function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        $title = match ($code) {
            401 => 'No autorizado',
            403 => 'Acceso denegado',
            404 => 'Página no encontrada',
            default => 'Error ' . $code,
        };
        $body = $message !== '' ? $message : $title;
        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">'
            . '<title>' . $title . '</title>'
            . '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">'
            . '</head><body style="padding:40px"><div class="container">'
            . '<div class="card"><div class="card-body">'
            . '<h1 class="h3">' . $title . '</h1><p class="text-muted">' . htmlspecialchars($body) . '</p>'
            . '<a class="btn btn-primary" href="' . e(app_url()) . '">Ir al inicio</a>'
            . '</div></div></div></body></html>';
        echo $html;
        exit;
    }
}

if (!function_exists('e')) {
    function e(string|null $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        return $_SESSION['_old'][$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(): bool
    {
        $sent = $_POST['_token'] ?? '';
        return hash_equals($_SESSION['_csrf'] ?? '', (string)$sent);
    }
}

if (!function_exists('db')) {
    function db(): PDO
    {
        return Database::connection();
    }
}

if (!function_exists('is_post')) {
    function is_post(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }
}

if (!function_exists('session_flash')) {
    function session_flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }
}

if (!function_exists('session_flash_html')) {
    function session_flash_html(string $key, string $type = 'success'): string
    {
        if (!empty($_SESSION['_flash'][$key])) {
            $msg = e($_SESSION['_flash'][$key]);
            unset($_SESSION['_flash'][$key]);
            return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">'
                . $msg
                . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        }
        return '';
    }
}

if (!function_exists('format_edad')) {
    function format_edad(?string $nacimiento): string
    {
        if (!$nacimiento) {
            return '—';
        }
        try {
            $birth = new DateTime($nacimiento);
        } catch (Exception) {
            return '—';
        }
        $now = new DateTime();
        $diff = $now->diff($birth);
        return sprintf('%d años, %d meses, %d días', $diff->y, $diff->m, $diff->d);
    }
}

if (!function_exists('calcular_edad')) {
    /**
     * Devuelve [anios, meses, dias, encima_de_18]
     */
    function calcular_edad(string $nacimiento): array
    {
        $birth = new DateTime($nacimiento);
        $now = new DateTime();
        $diff = $now->diff($birth);
        return [
            'anios' => $diff->y,
            'meses' => $diff->m,
            'dias'  => $diff->d,
            'mayor' => $diff->y >= 18,
        ];
    }
}

if (!function_exists('user_avatar')) {
    /**
     * Devuelve la URL del avatar del usuario. Si tiene una foto subida la usa,
     * de lo contrario usa un SVG por defecto según el sexo.
     */
    function user_avatar(?string $avatar = null, ?string $sexo = null): string
    {
        if (!empty($avatar)) {
            return app_url('/uploads/avatars/' . ltrim($avatar, '/'));
        }
        $sexo = strtoupper((string)$sexo);
        $isFemale = $sexo === 'F';
        $svg = $isFemale
            ? 'data:image/svg+xml;utf8,' . rawurlencode(
                '<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="#e83e8c"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>'
            )
            : 'data:image/svg+xml;utf8,' . rawurlencode(
                '<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="#0d6efd"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>'
            );
        return $svg;
    }
}

if (!function_exists('validar_cedula_ecuador')) {
    /**
     * Valida una cédula ecuatoriana usando el algoritmo del dígito verificador
     * (módulo 10) y las reglas de provincia y tercer dígito del SRI.
     */
    function validar_cedula_ecuador(string $cedula): bool
    {
        $cedula = preg_replace('/\D/', '', $cedula);
        if (strlen($cedula) !== 10) {
            return false;
        }

        // Primeros dos dígitos: provincia (01–24)
        $provincia = (int)substr($cedula, 0, 2);
        if ($provincia < 1 || $provincia > 24) {
            return false;
        }

        // Tercer dígito: < 6 para personas naturales (cédula)
        if ((int)$cedula[2] >= 6) {
            return false;
        }

        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $suma = 0;
        for ($i = 0; $i < 9; $i++) {
            $v = (int)$cedula[$i] * $coeficientes[$i];
            if ($v >= 10) {
                $v -= 9;
            }
            $suma += $v;
        }
        $verificador = (10 - ($suma % 10)) % 10;
        return $verificador === (int)$cedula[9];
    }
}

if (!function_exists('whatsapp_numero')) {
    /**
     * Normaliza un número local ecuatoriano a formato internacional
     * para wa.me: "0999999999" => "593999999999".
     */
    function whatsapp_numero(string $telefono): string
    {
        $d = preg_replace('/\D/', '', $telefono);
        if ($d === '') {
            return '';
        }
        if (str_starts_with($d, '0')) {
            return '593' . substr($d, 1);
        }
        return $d;
    }
}

if (!function_exists('whatsapp_link')) {
    /**
     * Enlace wa.me con mensaje predefinido. Devuelve '' si el teléfono
     * es inválido (menos de 7 dígitos).
     */
    function whatsapp_link(string $telefono, string $mensaje): string
    {
        $numero = whatsapp_numero($telefono);
        if (strlen($numero) < 7) {
            return '';
        }
        return 'https://wa.me/' . $numero . '?text=' . rawurlencode($mensaje);
    }
}