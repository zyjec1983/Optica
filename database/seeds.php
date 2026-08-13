<?php

/**
 * ==================================================================
 *  ARCHIVO: database/seeds.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Script de base de datos: seeds.
 * ==================================================================
 */



declare(strict_types=1);

/**
 * Seeders: crea el usuario administrador inicial y datos de ejemplo.
 * Uso: php database/seeds.php
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use core\Database;

$pdo = Database::connection();

// ===== Usuario administrador inicial =====
$adminEmail = 'admin@optica.com';
$adminPassword = 'Admin123!';

$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
$stmt->execute(['email' => $adminEmail]);

if ((int)$stmt->fetchColumn() === 0) {
    $pdo->prepare(
        'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
    )->execute([
        'name'     => 'Administrador',
        'email'    => $adminEmail,
        'password' => password_hash($adminPassword, PASSWORD_DEFAULT),
        'role'     => 'administrador',
    ]);
    echo "Usuario admin creado: {$adminEmail} / {$adminPassword}\n";
} else {
    echo "El usuario admin ya existe.\n";
}

// ===== Usuario optómetra de prueba =====
$optEmail = 'optometra@optica.com';
$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
$stmt->execute(['email' => $optEmail]);
if ((int)$stmt->fetchColumn() === 0) {
    $pdo->prepare(
        'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
    )->execute([
        'name'     => 'Optómetra Demo',
        'email'    => $optEmail,
        'password' => password_hash('Opto123!', PASSWORD_DEFAULT),
        'role'     => 'optometra',
    ]);
    echo "Usuario optómetra creado: {$optEmail} / Opto123!\n";
}

// ===== Usuario administrador (dueño) =====
$ownerEmail = 'zyjec@yahoo.com';
$stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
$stmt->execute(['email' => $ownerEmail]);
if ((int)$stmt->fetchColumn() === 0) {
    $pdo->prepare(
        'INSERT INTO users (name, email, password, role, sexo) VALUES (:name, :email, :password, :role, :sexo)'
    )->execute([
        'name'     => 'Christian',
        'email'    => $ownerEmail,
        'password' => password_hash('12345', PASSWORD_DEFAULT),
        'role'     => 'administrador',
        'sexo'     => 'M',
    ]);
    echo "Usuario dueño creado: {$ownerEmail} / 12345\n";
} else {
    echo "El usuario dueño ya existe.\n";
}

echo "Seeds completados.\n";