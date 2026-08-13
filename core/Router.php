<?php

/**
 * ==================================================================
 *  ARCHIVO: core/Router.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Núcleo del framework propio: Router.
 * ==================================================================
 */



declare(strict_types=1);

namespace core;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function add(string $method, string $path, callable|array $handler, array $middleware = []): void
    {
        $this->routes[strtoupper($method)][] = [
            'path'       => $this->normalize($path),
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $uri = $this->normalize($uri);

        foreach ($this->routes[$method] ?? [] as $route) {
            $params = $this->match($route['path'], $uri);
            if ($params === null) {
                continue;
            }

            foreach ($route['middleware'] as $mw) {
                $instance = $this->resolveMiddleware($mw);
                if ($instance !== null) {
                    $instance->handle(fn() => null);
                }
            }

            $handler = $route['handler'];

            if (is_array($handler)) {
                [$controller, $action] = $handler;
                if (!class_exists($controller)) {
                    throw new \RuntimeException("Controlador no encontrado: {$controller}");
                }
                $controllerInstance = new $controller();
                if (!method_exists($controllerInstance, $action)) {
                    throw new \RuntimeException("Acción no encontrada: {$controller}::{$action}");
                }
                $args = $this->resolveArguments($controller, $action, $params);
                call_user_func_array([$controllerInstance, $action], $args);
            } else {
                call_user_func_array($handler, array_values($params));
            }
            return;
        }

        abort(404);
    }

    /**
     * Construye los argumentos de un método de controlador, inyectando
     * core\Request cuando el parámetro lo requiera y completando el resto
     * con los parámetros de la ruta en orden.
     */
    private function resolveArguments(string $controller, string $action, array $routeParams): array
    {
        $ref = new \ReflectionMethod($controller, $action);
        $routeValues = array_values($routeParams);
        $args = [];
        foreach ($ref->getParameters() as $param) {
            $type = $param->getType();
            if ($type !== null && !$type->isBuiltin() && $type->getName() === Request::class) {
                $args[] = new Request();
                continue;
            }
            $args[] = array_shift($routeValues) ?? ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);
        }
        return $args;
    }

    private function match(string $pattern, string $uri): ?array
    {
        if ($pattern === $uri) {
            return [];
        }
        $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        if (preg_match($regex, $uri, $m)) {
            return array_filter($m, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
        }
        return null;
    }

    private function resolveMiddleware(string $name): ?object
    {
        $class = 'App\\Middleware\\' . ucfirst($name) . 'Middleware';
        if (!class_exists($class)) {
            throw new \RuntimeException("Middleware no encontrado: {$name}");
        }
        return new $class();
    }

    private function normalize(string $path): string
    {
        return rtrim($path, '/') ?: '/';
    }
}