# Optica

Sistema de Gestión para Óptica / Consultorio Optométrico.

## Stack

- PHP 8.2+ (arquitectura MVC + Services + Repository)
- MySQL / MariaDB 8.0+
- Bootstrap 5 + Bootstrap Icons
- Vanilla JS

## Estructura

```
app/         Controladores, modelos, servicios, repositorios, vistas, middlewares, helpers
config/      Rutas y configuración
core/        Front Controller, Router, Request, Controller base, Database, Session
database/    Migraciones y seeders
public/      Punto de entrada (index.php), assets, uploads
storage/     Logs, documentos, importaciones, exportaciones
```

## Requisitos

- PHP 8.2+ con extensiones `pdo_mysql`, `mbstring`, `zip`
- Composer
- MySQL 8.0+ / MariaDB
- Apache con `mod_rewrite`

## Instalación

```bash
cp .env.example .env        # configure sus credenciales
composer install
php database/migrate.php    # crea las tablas
php database/seeds.php      # crea usuarios iniciales
```

La aplicación se sirve desde `public/` (reescritura de Apache incluida).

## Funcionalidades

- Autenticación con roles (administrador, optómetra, cajero)
- Dashboard
- Gestión de pacientes (validación de cédula/RUC ecuatoriana)
- Agenda de citas
- Gestión de usuarios con perfil, avatar y cambio de contraseña

## Credenciales iniciales

| Rol        | Correo             | Clave      |
|------------|--------------------|------------|
| Admin      | admin@optica.com   | Admin123!  |
| Optómetra  | optometra@optica.com | Opto123! |
| Admin      | zyjec@yahoo.com    | 12345      |
