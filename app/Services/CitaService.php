<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Services/CitaService.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Casos de uso y reglas de aplicación de CitaService.
 * ==================================================================
 */



declare(strict_types=1);

namespace App\Services;

use App\Models\Cita;
use App\Repositories\CitaRepository;
use App\Repositories\PacienteRepository;
use DateTime;
use RuntimeException;

final class CitaService
{
    public function __construct(
        private readonly CitaRepository $citas = new CitaRepository(),
        private readonly PacienteRepository $pacientes = new PacienteRepository()
    ) {
    }

    public function list(?string $fecha = null, ?string $estado = null): array
    {
        return $this->citas->findAll($fecha, $estado);
    }

    public function find(int $id): ?Cita
    {
        return $this->citas->findById($id);
    }

    public function paciente(int $id): ?Paciente
    {
        return $this->pacientes->findById($id);
    }

    public function pacientesParaSelector(): array
    {
        return $this->pacientes->findAll();
    }

    public function deHoy(): array
    {
        return $this->citas->deHoy();
    }

    /**
     * @throws RuntimeException si hay datos inválidos
     */
    public function registrar(array $data, ?int $userId = null): int
    {
        $c = $this->validarYCompletar(new Cita(), $data);
        $c->user_id = $userId;
        return $this->citas->create($c);
    }

    /**
     * @throws RuntimeException si hay datos inválidos o la cita no existe
     */
    public function actualizar(int $id, array $data): void
    {
        $cita = $this->citas->findById($id);
        if ($cita === null) {
            throw new RuntimeException('Cita no encontrada.');
        }
        $c = $this->validarYCompletar($cita, $data);
        $c->id = $id;
        $this->citas->update($c);
    }

    /**
     * @throws RuntimeException si hay datos inválidos
     */
    public function cambiarEstado(int $id, string $estado): void
    {
        if (!in_array($estado, Cita::ESTADOS, true)) {
            throw new RuntimeException('Estado inválido.');
        }
        if ($this->citas->findById($id) === null) {
            throw new RuntimeException('Cita no encontrada.');
        }
        $this->citas->updateEstado($id, $estado);
    }

    public function eliminar(int $id): void
    {
        $this->citas->softDelete($id);
    }

    private function validarYCompletar(Cita $c, array $data): Cita
    {
        $pacienteId = (int)($data['paciente_id'] ?? 0);
        if ($pacienteId <= 0 || $this->pacientes->findById($pacienteId) === null) {
            throw new RuntimeException('Debe seleccionar un paciente válido.');
        }

        $fecha = trim((string)($data['fecha'] ?? ''));
        $hora = trim((string)($data['hora'] ?? ''));

        if ($fecha === '' || !strtotime($fecha)) {
            throw new RuntimeException('La fecha es obligatoria y debe ser válida.');
        }
        if ($hora === '') {
            throw new RuntimeException('La hora es obligatoria.');
        }
        $horaDt = DateTime::createFromFormat('H:i', $hora);
        if ($horaDt === false) {
            $horaDt = DateTime::createFromFormat('H:i:s', $hora);
        }
        if ($horaDt === false) {
            throw new RuntimeException('La hora debe tener formato HH:MM.');
        }

        $c->paciente_id = $pacienteId;
        $c->fecha = date('Y-m-d', strtotime($fecha));
        $c->hora = $horaDt->format('H:i:s');
        $c->motivo = trim((string)($data['motivo'] ?? ''));
        $c->notas = trim((string)($data['notas'] ?? '')) !== '' ? trim((string)$data['notas']) : null;

        $estado = (string)($data['estado'] ?? 'pendiente');
        $c->estado = in_array($estado, Cita::ESTADOS, true) ? $estado : 'pendiente';

        return $c;
    }
}
