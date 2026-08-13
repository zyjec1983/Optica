<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Repositories/ExamenRepository.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Acceso y persistencia de Examen en MySQL (PDO).
 * ==================================================================
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Examen;
use core\Database;
use PDO;

/**
 * Repositorio de exámenes visuales.
 *
 * Todas las consultas incluyen los datos del paciente (nombre e
 * identificación) mediante un JOIN, para listados e historial.
 */
final class ExamenRepository
{
    /**
     * Lista exámenes (opcionalmente filtrados por paciente).
     * Orden cronológico descendente: el más reciente primero.
     */
    public function findAll(?int $pacienteId = null): array
    {
        $sql = 'SELECT e.*,
                       CONCAT(p.primer_nombre, " ", p.segundo_nombre, " ", p.apellido_paterno, " ", p.apellido_materno) AS paciente_nombre,
                       p.identificacion AS paciente_identificacion
                FROM examenes e
                INNER JOIN pacientes p ON p.id = e.paciente_id';

        $params = [];
        if ($pacienteId !== null && $pacienteId > 0) {
            $sql .= ' WHERE e.paciente_id = :paciente';
            $params['paciente'] = $pacienteId;
        }
        $sql .= ' ORDER BY e.fecha_examen DESC, e.id DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Historial cronológico de exámenes de un paciente concreto.
     */
    public function historial(int $pacienteId): array
    {
        return $this->findAll($pacienteId);
    }

    public function findById(int $id): ?Examen
    {
        $stmt = Database::connection()->prepare(
            'SELECT e.*,
                    CONCAT(p.primer_nombre, " ", p.segundo_nombre, " ", p.apellido_paterno, " ", p.apellido_materno) AS paciente_nombre,
                    p.identificacion AS paciente_identificacion,
                    p.telefono AS paciente_telefono
             FROM examenes e
             INNER JOIN pacientes p ON p.id = e.paciente_id
             WHERE e.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $examen = Examen::fromRow($row);
        $examen->paciente_nombre = $row['paciente_nombre'];
        $examen->paciente_identificacion = $row['paciente_identificacion'];
        $examen->paciente_telefono = $row['paciente_telefono'];
        return $examen;
    }

    public function create(Examen $e): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO examenes (paciente_id, user_id, fecha_examen,
                od_esfera, od_cilindro, od_eje, os_esfera, os_cilindro, os_eje,
                dp, add_value, diagnostico, observaciones, firma, firma_representante)
             VALUES (:paciente, :user, :fecha,
                :od_esf, :od_cil, :od_eje, :os_esf, :os_cil, :os_eje,
                :dp, :add, :diag, :obs, :firma, :firma_rep)'
        );
        $stmt->execute($this->bindData($e));
        return (int)Database::connection()->lastInsertId();
    }

    public function update(Examen $e): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE examenes SET
                paciente_id = :paciente, fecha_examen = :fecha,
                od_esfera = :od_esf, od_cilindro = :od_cil, od_eje = :od_eje,
                os_esfera = :os_esf, os_cilindro = :os_cil, os_eje = :os_eje,
                dp = :dp, add_value = :add, diagnostico = :diag, observaciones = :obs,
                firma = :firma, firma_representante = :firma_rep
             WHERE id = :id'
        );
        $data = $this->bindData($e);
        $data['id'] = $e->id;
        unset($data['user']); // user_id no cambia al editar y el SQL no lo define
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM examenes WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int)Database::connection()->query('SELECT COUNT(*) FROM examenes')->fetchColumn();
    }

    /**
     * Convierte los valores del modelo a parámetros listos para PDO
     * (los valores vacíos se envían como NULL para columnas DECIMAL).
     */
    private function bindData(Examen $e): array
    {
        return [
            'paciente'  => $e->paciente_id,
            'user'      => $e->user_id,
            'fecha'     => $e->fecha_examen,
            'od_esf'    => $this->nullable($e->od_esfera),
            'od_cil'    => $this->nullable($e->od_cilindro),
            'od_eje'    => $this->nullable($e->od_eje),
            'os_esf'    => $this->nullable($e->os_esfera),
            'os_cil'    => $this->nullable($e->os_cilindro),
            'os_eje'    => $this->nullable($e->os_eje),
            'dp'        => $this->nullable($e->dp),
            'add'       => $this->nullable($e->add_value),
            'diag'      => $e->diagnostico,
            'obs'       => $e->observaciones,
            'firma'     => $e->firma,
            'firma_rep' => $e->firma_representante ? 1 : 0,
        ];
    }

    /**
     * Devuelve null si el valor está vacío (campos numéricos opcionales).
     */
    private function nullable(mixed $value): mixed
    {
        return ($value === null || $value === '') ? null : $value;
    }
}
