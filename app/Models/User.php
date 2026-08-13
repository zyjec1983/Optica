<?php

declare(strict_types=1);

namespace App\Models;

final class User
{
    public ?int $id = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'cajero';
    public string $sexo = 'M';
    public ?string $avatar = null;
    public string $created_at = '';

    public const ROLES = ['administrador', 'optometra', 'cajero'];

    public static function fromRow(array $row): self
    {
        $u = new self();
        $u->id = (int)$row['id'];
        $u->name = $row['name'];
        $u->email = $row['email'];
        $u->password = $row['password'];
        $u->role = $row['role'];
        $u->sexo = $row['sexo'] ?? 'M';
        $u->avatar = $row['avatar'] ?? null;
        $u->created_at = $row['created_at'] ?? '';
        return $u;
    }

    public function rolLabel(): string
    {
        return match ($this->role) {
            'administrador' => 'Administrador',
            'optometra'     => 'Optómetra',
            default         => 'Cajero',
        };
    }

    public function sexoLabel(): string
    {
        return $this->sexo === 'F' ? 'Mujer' : 'Hombre';
    }
}