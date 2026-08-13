<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Paciente;
use App\Repositories\PacienteRepository;
use App\Repositories\RepresentanteRepository;
use RuntimeException;

final class PacienteService
{
    public function __construct(
        private readonly PacienteRepository $pacientes = new PacienteRepository(),
        private readonly RepresentanteRepository $representantes = new RepresentanteRepository()
    ) {
    }

    public function list(string $search = ''): array
    {
        return $this->pacientes->findAll($search);
    }

    public function find(int $id): ?Paciente
    {
        return $this->pacientes->findById($id);
    }

    /**
     * Registra un paciente (con representante si es menor de edad).
     *
     * @throws RuntimeException si ya existe la identificación
     */
    public function registrar(array $data): int
    {
        if ($this->pacientes->findByIdentificacion($data['identificacion']) !== null) {
            throw new RuntimeException('Ya existe un paciente con esta identificación.');
        }

        $paciente = $this->fillPaciente(new Paciente(), $data);

        if ($paciente->esMenor()) {
            $representante = $this->representantes->findByCedula($data['rep_cedula']);
            if ($representante) {
                $this->representantes->update(
                    (int)$representante['id'],
                    $data['rep_parentesco'],
                    $data['rep_nombres'],
                    $data['rep_cedula'],
                    $data['rep_telefono']
                );
                $paciente->representante_id = (int)$representante['id'];
            } else {
                $paciente->representante_id = $this->representantes->create(
                    $data['rep_parentesco'],
                    $data['rep_nombres'],
                    $data['rep_cedula'],
                    $data['rep_telefono']
                );
            }
        }

        return $this->pacientes->create($paciente);
    }

    public function actualizar(int $id, array $data): void
    {
        $paciente = $this->pacientes->findById($id);
        if ($paciente === null) {
            throw new RuntimeException('Paciente no encontrado.');
        }

        $this->fillPaciente($paciente, $data);
        $paciente->id = $id;

        if ($paciente->esMenor()) {
            $representante = $this->representantes->findByCedula($data['rep_cedula']);
            if ($representante) {
                $this->representantes->update(
                    (int)$representante['id'],
                    $data['rep_parentesco'],
                    $data['rep_nombres'],
                    $data['rep_cedula'],
                    $data['rep_telefono']
                );
                $paciente->representante_id = (int)$representante['id'];
            } else {
                $paciente->representante_id = $this->representantes->create(
                    $data['rep_parentesco'],
                    $data['rep_nombres'],
                    $data['rep_cedula'],
                    $data['rep_telefono']
                );
            }
        } else {
            $paciente->representante_id = null;
        }

        $this->pacientes->update($paciente);
    }

    public function eliminar(int $id): void
    {
        $this->pacientes->delete($id);
    }

    public function dashboardStats(): array
    {
        return [
            'total_pacientes' => $this->pacientes->count(),
            'ultimos'         => $this->pacientes->ultimos(),
        ];
    }

    private function fillPaciente(Paciente $p, array $data): Paciente
    {
        $p->tipo_identificacion  = $data['tipo_identificacion'] ?? 'cedula';
        $p->identificacion       = trim($data['identificacion'] ?? '');
        $p->apellido_paterno     = trim($data['apellido_paterno'] ?? '');
        $p->apellido_materno     = trim($data['apellido_materno'] ?? '');
        $p->primer_nombre        = trim($data['primer_nombre'] ?? '');
        $p->segundo_nombre       = trim($data['segundo_nombre'] ?? '');
        $p->sexo                 = $data['sexo'] ?? 'M';
        $p->fecha_nacimiento     = $data['fecha_nacimiento'] ?: null;
        $p->telefono             = trim($data['telefono'] ?? '');
        $p->email                = trim($data['email'] ?? '');
        $p->rep_parentesco       = trim($data['rep_parentesco'] ?? '');
        $p->rep_nombres          = trim($data['rep_nombres'] ?? '');
        $p->rep_cedula           = trim($data['rep_cedula'] ?? '');
        $p->rep_telefono         = trim($data['rep_telefono'] ?? '');
        return $p;
    }
}