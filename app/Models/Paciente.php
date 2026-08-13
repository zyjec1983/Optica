<?php

declare(strict_types=1);

namespace App\Models;

final class Paciente
{
    public ?int $id = null;
    public string $tipo_identificacion = 'cedula';
    public string $identificacion = '';
    public string $apellido_paterno = '';
    public string $apellido_materno = '';
    public string $primer_nombre = '';
    public string $segundo_nombre = '';
    public string $sexo = 'M';
    public ?string $fecha_nacimiento = null;
    public string $telefono = '';
    public string $email = '';
    public ?int $representante_id = null;

    // Datos del representante (para el formulario)
    public string $rep_parentesco = '';
    public string $rep_nombres = '';
    public string $rep_cedula = '';
    public string $rep_telefono = '';

    public const IDENTIFICACIONES = ['cedula', 'pasaporte', 'ruc'];

    public function nombreCompleto(): string
    {
        $nombres = trim($this->primer_nombre . ' ' . $this->segundo_nombre);
        $apellidos = trim($this->apellido_paterno . ' ' . $this->apellido_materno);
        return trim($nombres . ' ' . $apellidos);
    }

    public function esMenor(): bool
    {
        if (!$this->fecha_nacimiento) {
            return false;
        }
        return !calcular_edad($this->fecha_nacimiento)['mayor'];
    }

    public function sexoLabel(): string
    {
        return $this->sexo === 'F' ? 'Femenino' : 'Masculino';
    }

    public static function fromRow(array $row): self
    {
        $p = new self();
        $p->id = (int)$row['id'];
        $p->tipo_identificacion = $row['tipo_identificacion'];
        $p->identificacion = $row['identificacion'];
        $p->apellido_paterno = $row['apellido_paterno'];
        $p->apellido_materno = $row['apellido_materno'] ?? '';
        $p->primer_nombre = $row['primer_nombre'];
        $p->segundo_nombre = $row['segundo_nombre'] ?? '';
        $p->sexo = $row['sexo'];
        $p->fecha_nacimiento = $row['fecha_nacimiento'];
        $p->telefono = $row['telefono'] ?? '';
        $p->email = $row['email'] ?? '';
        $p->representante_id = $row['representante_id'] !== null ? (int)$row['representante_id'] : null;
        return $p;
    }
}