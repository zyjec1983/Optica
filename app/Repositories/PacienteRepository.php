<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Repositories/PacienteRepository.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Acceso y persistencia de PacienteRepository en MySQL (PDO).
 * ==================================================================
 */



declare(strict_types=1);

namespace App\Repositories;

use App\Models\Paciente;
use core\Database;
use PDO;

final class PacienteRepository
{
    public function findAll(string $search = ''): array
    {
        $sql = 'SELECT p.*, r.nombres AS rep_nombres, r.parentesco AS rep_parentesco
                FROM pacientes p
                LEFT JOIN representantes r ON r.id = p.representante_id
                WHERE p.deleted_at IS NULL';

        $params = [];
        if ($search !== '') {
            $sql .= ' AND (p.identificacion LIKE :search1
                      OR CONCAT(p.primer_nombre, " ", p.segundo_nombre, " ", p.apellido_paterno, " ", p.apellido_materno) LIKE :search2
                      OR p.telefono LIKE :search3)';
            $params['search1'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
            $params['search3'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY p.id DESC';

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Búsqueda ligera para el autocompletado del cuadro de búsqueda
     * (endpoint JSON): por nombre, identificación o teléfono, con límite.
     */
    public function buscar(string $q, int $limit = 10): array
    {
        $sql = 'SELECT p.id, p.tipo_identificacion, p.identificacion, p.apellido_paterno, p.apellido_materno,
                       p.primer_nombre, p.segundo_nombre, p.fecha_nacimiento, p.telefono, p.email,
                       r.nombres AS rep_nombres, r.parentesco AS rep_parentesco
                FROM pacientes p
                LEFT JOIN representantes r ON r.id = p.representante_id
                WHERE p.deleted_at IS NULL
                  AND (p.identificacion LIKE :s1
                       OR CONCAT(p.primer_nombre, " ", p.segundo_nombre, " ", p.apellido_paterno, " ", p.apellido_materno) LIKE :s2
                       OR p.telefono LIKE :s3)
                ORDER BY p.id DESC
                LIMIT :lim';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':s1', '%' . $q . '%');
        $stmt->bindValue(':s2', '%' . $q . '%');
        $stmt->bindValue(':s3', '%' . $q . '%');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?Paciente
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM pacientes WHERE id = :id AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? Paciente::fromRow($row) : null;
    }

    public function findByIdentificacion(string $identificacion): ?Paciente
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM pacientes WHERE identificacion = :id AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->execute(['id' => $identificacion]);
        $row = $stmt->fetch();
        return $row ? Paciente::fromRow($row) : null;
    }

    public function create(Paciente $p): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO pacientes (tipo_identificacion, identificacion, apellido_paterno, apellido_materno,
                primer_nombre, segundo_nombre, sexo, fecha_nacimiento, telefono, email, representante_id)
             VALUES (:tipo, :id, :ap, :am, :pn, :sn, :sexo, :fn, :tel, :email, :rep)'
        );
        $stmt->execute([
            'tipo' => $p->tipo_identificacion,
            'id'   => $p->identificacion,
            'ap'   => $p->apellido_paterno,
            'am'   => $p->apellido_materno,
            'pn'   => $p->primer_nombre,
            'sn'   => $p->segundo_nombre,
            'sexo' => $p->sexo,
            'fn'   => $p->fecha_nacimiento,
            'tel'  => $p->telefono,
            'email' => $p->email,
            'rep'  => $p->representante_id,
        ]);
        return (int)Database::connection()->lastInsertId();
    }

    public function update(Paciente $p): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE pacientes SET tipo_identificacion = :tipo, identificacion = :id,
                apellido_paterno = :ap, apellido_materno = :am, primer_nombre = :pn, segundo_nombre = :sn,
                sexo = :sexo, fecha_nacimiento = :fn, telefono = :tel, email = :email, representante_id = :rep
             WHERE id = :pid'
        );
        $stmt->execute([
            'tipo' => $p->tipo_identificacion,
            'id'   => $p->identificacion,
            'ap'   => $p->apellido_paterno,
            'am'   => $p->apellido_materno,
            'pn'   => $p->primer_nombre,
            'sn'   => $p->segundo_nombre,
            'sexo' => $p->sexo,
            'fn'   => $p->fecha_nacimiento,
            'tel'  => $p->telefono,
            'email' => $p->email,
            'rep'  => $p->representante_id,
            'pid'  => $p->id,
        ]);
    }

    /**
     * Soft-delete: solo marca `deleted_at`; la fila permanece en la base.
     */
    public function softDelete(int $id): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE pacientes SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL'
        );
        $stmt->execute(['id' => $id]);
    }

    public function count(): int
    {
        return (int)Database::connection()
            ->query('SELECT COUNT(*) FROM pacientes WHERE deleted_at IS NULL')
            ->fetchColumn();
    }

    public function ultimos(int $limit = 5): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT p.* FROM pacientes p WHERE p.deleted_at IS NULL ORDER BY p.id DESC LIMIT :lim'
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}