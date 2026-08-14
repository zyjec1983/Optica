<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Services/ExamenService.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Casos de uso y reglas de aplicación de Examen.
 * ==================================================================
 */

declare(strict_types=1);

namespace App\Services;

use App\Models\Examen;
use App\Models\PruebaExamen;
use App\Repositories\ExamenRepository;
use App\Repositories\PacienteRepository;
use App\Repositories\PruebaExamenRepository;
use RuntimeException;

/**
 * Servicio de exámenes visuales.
 *
 * Valida los datos de refracción (OD/OS, DP, ADD), la firma manuscrita
 * electrónica, las pruebas complementarias de la consulta y delega la
 * persistencia al repositorio.
 */
final class ExamenService
{
    public function __construct(
        private readonly ExamenRepository $examenes = new ExamenRepository(),
        private readonly PacienteRepository $pacientes = new PacienteRepository(),
        private readonly PruebaExamenRepository $pruebas = new PruebaExamenRepository()
    ) {
    }

    public function list(?int $pacienteId = null): array
    {
        return $this->examenes->findAll($pacienteId);
    }

    public function historial(int $pacienteId): array
    {
        return $this->examenes->historial($pacienteId);
    }

    public function find(int $id): ?Examen
    {
        $examen = $this->examenes->findById($id);
        if ($examen !== null) {
            $examen->pruebas = $this->pruebas->porExamen($id);
        }
        return $examen;
    }

    public function paciente(int $id): ?\App\Models\Paciente
    {
        return $this->pacientes->findById($id);
    }

    public function pacientesParaSelector(): array
    {
        return $this->pacientes->findAll();
    }

    /**
     * @throws RuntimeException si los datos son inválidos
     */
    public function registrar(array $data, ?int $userId = null): int
    {
        $e = $this->validarYCompletar(new Examen(), $data, true);
        $e->user_id = $userId;
        $id = $this->examenes->create($e);
        $this->guardarPruebas($id, $data['pruebas'] ?? []);
        return $id;
    }

    /**
     * @throws RuntimeException si los datos son inválidos o el examen no existe
     */
    public function actualizar(int $id, array $data): void
    {
        $examen = $this->examenes->findById($id);
        if ($examen === null) {
            throw new RuntimeException('Examen no encontrado.');
        }
        $e = $this->validarYCompletar($examen, $data, false);
        $e->id = $id;
        $this->examenes->update($e);
        $this->guardarPruebas($id, $data['pruebas'] ?? []);
    }

    public function eliminar(int $id): void
    {
        $this->examenes->softDelete($id);
    }

    /**
     * Valida y completa la entidad a partir de los datos del formulario.
     *
     * @param bool $exigirFirma true al crear (la firma es obligatoria),
     *                          false al editar (se conserva la anterior).
     *
     * @throws RuntimeException ante cualquier dato inválido
     */
    private function validarYCompletar(Examen $e, array $data, bool $exigirFirma): Examen
    {
        $pacienteId = (int)($data['paciente_id'] ?? 0);
        if ($pacienteId <= 0 || $this->pacientes->findById($pacienteId) === null) {
            throw new RuntimeException('Debe seleccionar un paciente válido.');
        }

        $fecha = trim((string)($data['fecha_examen'] ?? ''));
        if ($fecha === '' || !strtotime($fecha)) {
            throw new RuntimeException('La fecha del examen es obligatoria y debe ser válida.');
        }

        $e->paciente_id = $pacienteId;
        $e->fecha_examen = date('Y-m-d', strtotime($fecha));

        // Campos de refracción (opcionales, con formato numérico válido)
        $e->od_esfera = $this->validarRefraccion('OD esfera', $data['od_esfera'] ?? '', true);
        $e->od_cilindro = $this->validarRefraccion('OD cilindro', $data['od_cilindro'] ?? '', true);
        $e->od_eje = $this->validarEje('OD eje', $data['od_eje'] ?? '');
        $e->os_esfera = $this->validarRefraccion('OS esfera', $data['os_esfera'] ?? '', true);
        $e->os_cilindro = $this->validarRefraccion('OS cilindro', $data['os_cilindro'] ?? '', true);
        $e->os_eje = $this->validarEje('OS eje', $data['os_eje'] ?? '');
        $e->dp = $this->validarRefraccion('DP', $data['dp'] ?? '', true);
        $e->add_value = $this->validarRefraccion('ADD', $data['add_value'] ?? '', true);

        $e->diagnostico = trim((string)($data['diagnostico'] ?? '')) !== ''
            ? trim((string)$data['diagnostico']) : null;
        $e->observaciones = trim((string)($data['observaciones'] ?? '')) !== ''
            ? trim((string)$data['observaciones']) : null;

        $firma = trim((string)($data['firma'] ?? ''));
        if ($firma !== '') {
            if (!str_starts_with($firma, 'data:image/png;base64,')) {
                throw new RuntimeException('La firma capturada no es válida.');
            }
            $e->firma = $firma;
        } elseif ($exigirFirma) {
            throw new RuntimeException('Debe capturar la firma del paciente o representante.');
        }

        $e->firma_representante = !empty($data['firma_representante']);

        return $e;
    }

    /**
     * Valida un valor de refracción (esfera/cilindro/DP/ADD): numérico,
     * con signo opcional y hasta 2 decimales. Permite vacío si no es obligatorio.
     *
     * @throws RuntimeException si el formato es inválido
     */
    private function validarRefraccion(string $campo, mixed $valor, bool $opcional): ?string
    {
        $valor = trim((string)$valor);
        if ($valor === '') {
            if ($opcional) {
                return null;
            }
            throw new RuntimeException("El campo {$campo} es obligatorio.");
        }
        if (!preg_match('/^[+-]?\d+(\.\d{1,2})?$/', $valor)) {
            throw new RuntimeException("El campo {$campo} tiene un formato inválido (ej: -1.25).");
        }
        return $valor;
    }

    /**
     * Valida el eje de un astigmatismo: entero entre 0 y 180.
     *
     * @throws RuntimeException si el valor está fuera de rango
     */
    private function validarEje(string $campo, mixed $valor): ?string
    {
        $valor = trim((string)$valor);
        if ($valor === '') {
            return null;
        }
        if (!ctype_digit($valor) || (int)$valor < 0 || (int)$valor > 180) {
            throw new RuntimeException("El campo {$campo} debe ser un entero entre 0 y 180.");
        }
        return $valor;
    }

    /**
     * Normaliza y persiste las pruebas complementarias de la consulta.
     * Solo se guardan las que tienen al menos un valor; la refracción y
     * el astigmatismo se conservan en las columnas del examen.
     */
    private function guardarPruebas(int $examenId, mixed $data): void
    {
        $data = is_array($data) ? $data : [];
        $limpio = [];

        foreach (PruebaExamen::PRUEBAS as $clave => $cfg) {
            $raw = is_array($data[$clave] ?? null) ? $data[$clave] : [];
            $item = [];

            foreach (['od', 'os', 'resultado'] as $campo) {
                $v = trim((string)($raw[$campo] ?? ''));
                if ($v !== '') {
                    $item[$campo] = $v;
                }
            }

            if (isset($raw['normal']) && $raw['normal'] !== '') {
                $item['normal'] = (string)$raw['normal'] === '1' ? 1 : 0;
            }

            if ($item !== []) {
                $limpio[$clave] = $item;
            }
        }

        $this->pruebas->reemplazar($examenId, $limpio);
    }
}
