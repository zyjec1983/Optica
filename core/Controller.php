<?php

/**
 * ==================================================================
 *  ARCHIVO: core/Controller.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Núcleo del framework propio: Controller.
 * ==================================================================
 */



declare(strict_types=1);

namespace core;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        $file = dirname(__DIR__) . '/app/Views/' . $view . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Vista no encontrada: {$view}");
        }
        extract($data, EXTR_SKIP);
        require $file;
    }

    protected function viewWithLayout(string $layout, string $view, array $data = []): void
    {
        $data['_content'] = $view;
        $data['_data'] = $data;
        $this->view($layout, $data);
    }

    protected function validate(array $rules): array
    {
        // Validación ligera: ['campo' => 'required|email|min:2|max:50']
        $errors = [];
        foreach ($rules as $field => $ruleList) {
            $value = $_POST[$field] ?? '';
            foreach (explode('|', $ruleList) as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                switch ($name) {
                    case 'required':
                        if ($value === '') {
                            $errors[$field][] = 'El campo es obligatorio.';
                        }
                        break;
                    case 'email':
                        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = 'Debe ser un correo válido.';
                        }
                        break;
                    case 'min':
                        if (mb_strlen((string)$value) < (int)$param) {
                            $errors[$field][] = "Debe tener al menos {$param} caracteres.";
                        }
                        break;
                    case 'max':
                        if (mb_strlen((string)$value) > (int)$param) {
                            $errors[$field][] = "No debe superar {$param} caracteres.";
                        }
                        break;
                    case 'date':
                        if ($value !== '' && !strtotime($value)) {
                            $errors[$field][] = 'Debe ser una fecha válida.';
                        }
                        break;
                    case 'numeric':
                        if ($value !== '' && !is_numeric($value)) {
                            $errors[$field][] = 'Debe ser numérico.';
                        }
                        break;
                }
            }
        }
        return $errors;
    }

    protected function fail(array $errors): never
    {
        $_SESSION['_errors'] = $errors;
        $_SESSION['_old'] = $_POST;
        $referer = $_SERVER['HTTP_REFERER'] ?? app_url();
        redirect($referer);
    }

    protected function hasErrors(): bool
    {
        if (!empty($_SESSION['_errors'])) {
            unset($_SESSION['_errors']);
            return true;
        }
        return false;
    }
}