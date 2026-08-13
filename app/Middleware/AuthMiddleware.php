<?php

declare(strict_types=1);

namespace App\Middleware;

final class AuthMiddleware implements Middleware
{
    public function handle(callable $next): void
    {
        if (empty($_SESSION['user'])) {
            redirect(app_url('/login'));
        }
        $next();
    }
}