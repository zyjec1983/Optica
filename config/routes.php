<?php

/**
 * ==================================================================
 *  ARCHIVO: config/routes.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Configuración central del proyecto (portable vía .env).
 * ==================================================================
 */



declare(strict_types=1);

use core\Router;
use App\Controllers\{
    AuthController,
    PacienteController,
    DashboardController,
    PerfilController,
    UserController,
    CitaController,
    ExamenController,
    RecordatorioController
};

$router = new Router();

// ===== Autenticación =====
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

// ===== Perfil (propio) =====
$router->post('/perfil', [PerfilController::class, 'avatar'], ['auth']);
$router->post('/perfil/password', [PerfilController::class, 'password'], ['auth']);

// ===== Usuarios (solo administrador) =====
$router->get('/usuarios', [UserController::class, 'index'], ['auth']);
$router->get('/usuarios/nuevo', [UserController::class, 'create'], ['auth']);
$router->post('/usuarios', [UserController::class, 'store'], ['auth']);
$router->get('/usuarios/editar/{id}', [UserController::class, 'edit'], ['auth']);
$router->post('/usuarios/editar/{id}', [UserController::class, 'update'], ['auth']);
$router->post('/usuarios/eliminar/{id}', [UserController::class, 'destroy'], ['auth']);

// ===== Autenticado =====
$router->get('/', [DashboardController::class, 'index'], ['auth']);
$router->get('/dashboard', [DashboardController::class, 'index'], ['auth']);

// ===== Pacientes =====
$router->get('/pacientes', [PacienteController::class, 'index'], ['auth']);
$router->get('/pacientes/buscar', [PacienteController::class, 'buscar'], ['auth']);
$router->get('/pacientes/nuevo', [PacienteController::class, 'create'], ['auth']);
$router->post('/pacientes', [PacienteController::class, 'store'], ['auth']);
$router->get('/pacientes/editar/{id}', [PacienteController::class, 'edit'], ['auth']);
$router->post('/pacientes/editar/{id}', [PacienteController::class, 'update'], ['auth']);
$router->post('/pacientes/eliminar/{id}', [PacienteController::class, 'destroy'], ['auth']);

// ===== Citas =====
$router->get('/citas', [CitaController::class, 'index'], ['auth']);
$router->get('/citas/nueva', [CitaController::class, 'create'], ['auth']);
$router->post('/citas', [CitaController::class, 'store'], ['auth']);
$router->get('/citas/editar/{id}', [CitaController::class, 'edit'], ['auth']);
$router->post('/citas/editar/{id}', [CitaController::class, 'update'], ['auth']);
$router->post('/citas/estado/{id}', [CitaController::class, 'estado'], ['auth']);
$router->post('/citas/eliminar/{id}', [CitaController::class, 'destroy'], ['auth']);

// ===== Exámenes visuales =====
$router->get('/examenes', [ExamenController::class, 'index'], ['auth']);
$router->get('/examenes/nuevo', [ExamenController::class, 'create'], ['auth']);
$router->post('/examenes', [ExamenController::class, 'store'], ['auth']);
$router->get('/examenes/editar/{id}', [ExamenController::class, 'edit'], ['auth']);
$router->post('/examenes/editar/{id}', [ExamenController::class, 'update'], ['auth']);
$router->get('/examenes/{id}', [ExamenController::class, 'show'], ['auth']);
$router->post('/examenes/eliminar/{id}', [ExamenController::class, 'destroy'], ['auth']);

// ===== Recordatorios (aviso de lentes listos) =====
$router->post('/recordatorios/marcar/{id}', [RecordatorioController::class, 'marcar'], ['auth']);