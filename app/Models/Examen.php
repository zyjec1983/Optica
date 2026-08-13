<?php

/**
 * ==================================================================
 *  ARCHIVO: app/Models/Examen.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Modelo / entidad de dominio Examen (sin SQL).
 * ==================================================================
 */

declare(strict_types=1);

namespace App\Models;

/**
 * Examen visual de un paciente.
 *
 * Representa una valoración optométrica con las prescripciones de cada
 * ojo (OD: ojo derecho, OS: ojo izquierdo), distancia pupilar (DP),
 * adición (ADD), diagnóstico, observaciones y la firma manuscrita
 * electrónica capturada digitalmente.
 */
final class Examen
{
    public ?int $id = null;
    public ?int $paciente_id = null;
    public ?int $user_id = null;
    public ?string $fecha_examen = null;

    public ?string $od_esfera = null;
    public ?string $od_cilindro = null;
    public ?string $od_eje = null;
    public ?string $os_esfera = null;
    public ?string $os_cilindro = null;
    public ?string $os_eje = null;

    public ?string $dp = null;
    public ?string $add_value = null;
    public ?string $diagnostico = null;
    public ?string $observaciones = null;

    public ?string $firma = null;
    public bool $firma_representante = false;

    public string $created_at = '';

    // Datos del paciente (para listados y detalle, sin SQL aquí)
    public string $paciente_nombre = '';
    public string $paciente_identificacion = '';
    public string $paciente_telefono = '';

    /**
     * Crea una instancia a partir de una fila de la base de datos.
     */
    public static function fromRow(array $row): self
    {
        $e = new self();
        $e->id = (int)$row['id'];
        $e->paciente_id = $row['paciente_id'] !== null ? (int)$row['paciente_id'] : null;
        $e->user_id = $row['user_id'] !== null ? (int)$row['user_id'] : null;
        $e->fecha_examen = $row['fecha_examen'];
        $e->od_esfera = self::castString($row['od_esfera'] ?? null);
        $e->od_cilindro = self::castString($row['od_cilindro'] ?? null);
        $e->od_eje = self::castString($row['od_eje'] ?? null);
        $e->os_esfera = self::castString($row['os_esfera'] ?? null);
        $e->os_cilindro = self::castString($row['os_cilindro'] ?? null);
        $e->os_eje = self::castString($row['os_eje'] ?? null);
        $e->dp = self::castString($row['dp'] ?? null);
        $e->add_value = self::castString($row['add_value'] ?? null);
        $e->diagnostico = $row['diagnostico'] ?? null;
        $e->observaciones = $row['observaciones'] ?? null;
        $e->firma = $row['firma'] ?? null;
        $e->firma_representante = (bool)($row['firma_representante'] ?? false);
        $e->created_at = $row['created_at'] ?? '';
        return $e;
    }

    /**
     * Etiqueta legible para saber quién firmó el examen.
     */
    public function firmaLabel(): string
    {
        return $this->firma_representante ? 'Representante' : 'Paciente';
    }

    /**
     * Convierte valores de columnas numéricas (DECIMAL/INT) a string,
     * manteniendo null cuando la columna está vacía.
     */
    private static function castString(mixed $value): ?string
    {
        return $value === null ? null : (string)$value;
    }
}
