<?php

/**
 * ==================================================================
 *  ARCHIVO: core/Response.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Núcleo del framework propio: Response.
 * ==================================================================
 */



declare(strict_types=1);

namespace core;

final class Response
{
    public function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    public function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }

    public function back(): never
    {
        $path = $_SERVER['HTTP_REFERER'] ?? app_url();
        $this->redirect($path);
    }

    public function withInput(): void
    {
        $_SESSION['_old'] = array_merge($_GET, $_POST);
    }
}