<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Controllers/CitaController.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Controlador del módulo de citas (agenda de atenciones).
 * ==================================================================
 */



declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cita;
use App\Services\AuthService;
use App\Services\CitaService;
use core\Controller;
use core\Request;
use RuntimeException;

final class CitaController extends Controller
{
    public function __construct(
        private readonly CitaService $servicio = new CitaService()
    ) {
    }

    public function index(Request $request): void
    {
        $fecha = trim((string)$request->get('fecha', date('Y-m-d')));
        $estado = trim((string)$request->get('estado'));

        $this->viewWithLayout('layouts/app', 'citas/index', [
            'citas'   => $this->servicio->list($fecha, $estado),
            'fecha'   => $fecha,
            'estado'  => $estado,
            'estados' => Cita::ESTADOS,
        ]);
    }

    public function create(): void
    {
        $this->viewWithLayout('layouts/app', 'citas/form', [
            'cita'     => new Cita(),
            'esEdicion' => false,
            'pacientes' => $this->servicio->pacientesParaSelector(),
        ]);
    }

    public function store(Request $request): void
    {
        if (!verify_csrf()) {
            session_flash('error', 'Sesión expirada, reintente.');
            redirect(app_url('/citas'));
        }

        try {
            $userId = (AuthService::user()['id'] ?? null) !== null ? (int)AuthService::user()['id'] : null;
            $this->servicio->registrar($request->all(), $userId);
            session_flash('success', 'Cita registrada correctamente.');
        } catch (RuntimeException $ex) {
            session_flash('error', $ex->getMessage());
            $_SESSION['_old'] = $request->all();
        }

        redirect(app_url('/citas'));
    }

    public function edit(int $id): void
    {
        $cita = $this->servicio->find($id);
        if ($cita === null) {
            abort(404);
        }

        $this->viewWithLayout('layouts/app', 'citas/form', [
            'cita'      => $cita,
            'esEdicion' => true,
            'pacientes' => $this->servicio->pacientesParaSelector(),
        ]);
    }

    public function update(Request $request, int $id): void
    {
        if (!verify_csrf()) {
            session_flash('error', 'Sesión expirada, reintente.');
            redirect(app_url('/citas'));
        }

        try {
            $this->servicio->actualizar($id, $request->all());
            session_flash('success', 'Cita actualizada correctamente.');
        } catch (RuntimeException $ex) {
            session_flash('error', $ex->getMessage());
            $_SESSION['_old'] = $request->all();
        }

        redirect(app_url('/citas'));
    }

    public function estado(Request $request, int $id): void
    {
        if (!verify_csrf()) {
            abort(403);
        }

        $estado = (string)$request->post('estado');
        try {
            $this->servicio->cambiarEstado($id, $estado);
            session_flash('success', 'Estado de la cita actualizado.');
        } catch (RuntimeException $ex) {
            session_flash('error', $ex->getMessage());
        }

        redirect(app_url('/citas'));
    }

    public function destroy(Request $request, int $id): void
    {
        if (!verify_csrf()) {
            abort(403);
        }

        $this->servicio->eliminar($id);
        session_flash('success', 'Cita eliminada (soft delete): el registro queda oculto, la información no se borra del sistema.');
        redirect(app_url('/citas'));
    }
}
