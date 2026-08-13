<?php

/**
 * ==================================================================
 *  ARCHIVO: database/migrate.php
 *  PROYECTO: Sistema de Gestión para Óptica / Consultorio Optométrico
 *  DESCRIPCIÓN: Script de base de datos: migrate.
 * ==================================================================
 */



declare(strict_types=1);

/**
 * Migraciones de base de datos.
 * Uso: php database/migrate.php
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use core\Database;

$pdo = Database::connection();
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

$queries = [
    // ===== users =====
    'CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(120) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM("administrador","optometra","cajero") NOT NULL DEFAULT "cajero",
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',

    // ===== representantes =====
    'CREATE TABLE IF NOT EXISTS representantes (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        parentesco VARCHAR(50) NOT NULL,
        nombres VARCHAR(160) NOT NULL,
        cedula VARCHAR(20) NOT NULL UNIQUE,
        telefono VARCHAR(20) DEFAULT "",
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',

    // ===== pacientes =====
    'CREATE TABLE IF NOT EXISTS pacientes (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        tipo_identificacion ENUM("cedula","pasaporte","ruc") NOT NULL DEFAULT "cedula",
        identificacion VARCHAR(20) NOT NULL,
        apellido_paterno VARCHAR(60) NOT NULL,
        apellido_materno VARCHAR(60) DEFAULT "",
        primer_nombre VARCHAR(60) NOT NULL,
        segundo_nombre VARCHAR(60) DEFAULT "",
        sexo ENUM("M","F") NOT NULL DEFAULT "M",
        fecha_nacimiento DATE,
        telefono VARCHAR(20) DEFAULT "",
        email VARCHAR(100) DEFAULT "",
        representante_id INT UNSIGNED NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_identificacion (tipo_identificacion, identificacion),
        KEY idx_representante (representante_id),
        CONSTRAINT fk_paciente_representante FOREIGN KEY (representante_id)
            REFERENCES representantes (id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',
    // ===== citas =====
    'CREATE TABLE IF NOT EXISTS citas (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        paciente_id INT UNSIGNED NOT NULL,
        user_id INT UNSIGNED NULL,
        fecha DATE NOT NULL,
        hora TIME NOT NULL,
        motivo VARCHAR(255) DEFAULT "",
        notas TEXT,
        estado ENUM("pendiente","confirmada","atendida","cancelada") NOT NULL DEFAULT "pendiente",
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY idx_cita_paciente (paciente_id),
        KEY idx_cita_fecha (fecha),
        KEY idx_cita_estado (estado),
        CONSTRAINT fk_cita_paciente FOREIGN KEY (paciente_id)
            REFERENCES pacientes (id) ON DELETE CASCADE,
        CONSTRAINT fk_cita_usuario FOREIGN KEY (user_id)
            REFERENCES users (id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',
    // ===== examenes visuales =====
    'CREATE TABLE IF NOT EXISTS examenes (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        paciente_id INT UNSIGNED NOT NULL,
        user_id INT UNSIGNED NULL,
        fecha_examen DATE NOT NULL,
        od_esfera DECIMAL(5,2) NULL,
        od_cilindro DECIMAL(5,2) NULL,
        od_eje SMALLINT UNSIGNED NULL,
        os_esfera DECIMAL(5,2) NULL,
        os_cilindro DECIMAL(5,2) NULL,
        os_eje SMALLINT UNSIGNED NULL,
        dp DECIMAL(4,1) NULL,
        add_value DECIMAL(4,2) NULL,
        diagnostico TEXT,
        observaciones TEXT,
        firma LONGTEXT,
        firma_representante TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY idx_examen_paciente (paciente_id),
        KEY idx_examen_fecha (fecha_examen),
        CONSTRAINT fk_examen_paciente FOREIGN KEY (paciente_id)
            REFERENCES pacientes (id) ON DELETE CASCADE,
        CONSTRAINT fk_examen_usuario FOREIGN KEY (user_id)
            REFERENCES users (id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4',
];

foreach ($queries as $sql) {
    $pdo->exec($sql);
}

// ===== Columnas adicionales (idempotente para tablas ya existentes) =====
$columnas = [
    'users' => [
        'sexo ENUM("M","F") NOT NULL DEFAULT "M" AFTER role',
        'avatar VARCHAR(255) DEFAULT NULL AFTER sexo',
    ],
];

foreach ($columnas as $tabla => $cols) {
    $existentes = $pdo->query("SHOW COLUMNS FROM `{$tabla}`")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($cols as $col) {
        $nombre = explode(' ', trim($col))[0];
        if (!in_array($nombre, $existentes, true)) {
            $pdo->exec("ALTER TABLE `{$tabla}` ADD COLUMN {$col}");
        }
    }
}

echo "Tablas creadas/verificadas correctamente.\n";