<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Paciente;
use App\Repositories\RepresentanteRepository;
use App\Services\PacienteService;
use core\Controller;
use core\Request;
use RuntimeException;

final class PacienteController extends Controller
{
    public function __construct(
        private readonly PacienteService $servicio = new PacienteService(),
        private readonly RepresentanteRepository $representantes = new RepresentanteRepository()
    ) {
    }

    public function index(Request $request): void
    {
        $busqueda = trim((string)$request->get('q'));
        $pacientes = $this->servicio->list($busqueda);
        $this->viewWithLayout('layouts/app', 'pacientes/index', [
            'pacientes' => $pacientes,
            'busqueda'  => $busqueda,
        ]);
    }

    public function create(): void
    {
        $this->viewWithLayout('layouts/app', 'pacientes/form', [
            'paciente' => new Paciente(),
            'esEdicion' => false,
        ]);
    }

    public function store(Request $request): void
    {
        if (!verify_csrf()) {
            session_flash('error', 'Sesión expirada, reintente.');
            redirect(app_url('/pacientes'));
        }

        $errors = $this->validate([
            'identificacion'   => 'required|min:5|max:20',
            'apellido_paterno' => 'required|max:60',
            'primer_nombre'    => 'required|max:60',
            'fecha_nacimiento' => 'required|date',
            'email'            => 'email|max:100',
        ]);

        $errors = $this->validarIdentificacionEcuador($request, $errors);
        if ($errors) {
            $this->fail($errors);
        }

        try {
            if ($this->servicio->registrar($request->all())) {
                session_flash('success', 'Paciente registrado correctamente.');
            }
        } catch (RuntimeException $ex) {
            session_flash('error', $ex->getMessage());
        }

        redirect(app_url('/pacientes'));
    }

    public function edit(int $id): void
    {
        $paciente = $this->servicio->find($id);
        if ($paciente === null) {
            abort(404);
        }

        $representante = null;
        if ($paciente->representante_id !== null) {
            $representante = $this->representanteRow($paciente->representante_id);
            if ($representante) {
                $paciente->rep_cedula = $representante['cedula'];
                $paciente->rep_nombres = $representante['nombres'];
                $paciente->rep_parentesco = $representante['parentesco'];
                $paciente->rep_telefono = $representante['telefono'];
            }
        }

        $this->viewWithLayout('layouts/app', 'pacientes/form', [
            'paciente' => $paciente,
            'esEdicion' => true,
        ]);
    }

    public function update(Request $request, int $id): void
    {
        if (!verify_csrf()) {
            session_flash('error', 'Sesión expirada, reintente.');
            redirect(app_url('/pacientes'));
        }

        $errors = $this->validate([
            'identificacion'   => 'required|min:5|max:20',
            'apellido_paterno' => 'required|max:60',
            'primer_nombre'    => 'required|max:60',
            'fecha_nacimiento' => 'required|date',
            'email'            => 'email|max:100',
        ]);

        $errors = $this->validarIdentificacionEcuador($request, $errors);
        if ($errors) {
            $this->fail($errors);
        }

        try {
            $this->servicio->actualizar($id, $request->all());
            session_flash('success', 'Paciente actualizado correctamente.');
        } catch (RuntimeException $ex) {
            session_flash('error', $ex->getMessage());
        }

        redirect(app_url('/pacientes'));
    }

    public function destroy(Request $request, int $id): void
    {
        if (!verify_csrf()) {
            abort(403);
        }
        try {
            $this->servicio->eliminar($id);
            session_flash('success', 'Paciente eliminado.');
        } catch (RuntimeException $ex) {
            session_flash('error', $ex->getMessage());
        }
        redirect(app_url('/pacientes'));
    }

    private function representanteRow(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM representantes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Aplica las reglas de validación específicas de identificación ecuatoriana:
     * cédula (algoritmo módulo 10) y RUC de persona natural (cédula + 001).
     */
    private function validarIdentificacionEcuador(Request $request, array $errors): array
    {
        $tipo = (string)$request->post('tipo_identificacion', 'cedula');
        $numero = preg_replace('/\D/', '', (string)$request->post('identificacion'));

        if ($tipo === 'cedula' && !validar_cedula_ecuador($numero)) {
            $errors['identificacion'][] = 'La cédula no es válida (verifique el dígito verificador).';
        }

        if ($tipo === 'ruc') {
            // RUC de persona natural: cédula válida + sufijo "001"
            if (strlen($numero) === 13 && substr($numero, 10) === '001') {
                if (!validar_cedula_ecuador(substr($numero, 0, 10))) {
                    $errors['identificacion'][] = 'El RUC no es válido (dígito verificador de la cédula incorrecto).';
                }
            } else {
                $errors['identificacion'][] = 'Formato de RUC inválido.';
            }
        }

        return $errors;
    }
}