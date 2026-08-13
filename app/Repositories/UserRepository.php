<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use core\Database;
use PDO;

final class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? User::fromRow($row) : null;
    }

    public function create(array $data): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO users (name, email, password, role, sexo, avatar)
             VALUES (:name, :email, :password, :role, :sexo, :avatar)'
        );
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'sexo' => $data['sexo'] ?? 'M',
            'avatar' => $data['avatar'] ?? null,
        ]);
        return (int)Database::connection()->lastInsertId();
    }

    public function findById(int $id): ?User
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? User::fromRow($row) : null;
    }

    public function updateAvatar(int $id, ?string $avatar): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET avatar = :avatar WHERE id = :id');
        $stmt->execute(['avatar' => $avatar, 'id' => $id]);
    }

    public function updatePassword(int $id, string $hash): void
    {
        $stmt = Database::connection()->prepare('UPDATE users SET password = :password WHERE id = :id');
        $stmt->execute(['password' => $hash, 'id' => $id]);
    }

    public function update(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE users SET name = :name, email = :email, role = :role, sexo = :sexo, avatar = :avatar WHERE id = :id'
        );
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'sexo' => $data['sexo'] ?? 'M',
            'avatar' => $data['avatar'] ?? null,
            'id' => $data['id'],
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function findAll(): array
    {
        return Database::connection()
            ->query('SELECT * FROM users ORDER BY name')
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}