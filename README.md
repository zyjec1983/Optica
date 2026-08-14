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

## Arquitectura

Framework MVC propio (sin framework externo) con capas en orden fijo:

```
Navegador → public/index.php (Front Controller) → config/routes.php (Router)
→ Middleware → Controller → Service → Repository → MySQL
→ View (HTML)
```

- **Controller** (`app/Controllers`): recibe la petición, valida lo formal, delega.
- **Service** (`app/Services`): reglas de negocio (validaciones de negocio, orquestación).
- **Repository** (`app/Repositories`): todo el SQL de una tabla (PDO preparado).
- **Model** (`app/Models`): datos y cálculos simples; nunca consulta la BD.
- **View** (`app/Views`): HTML con solo `e()` para escapar la salida.
- **Middleware** (`app/Middleware`): portero de rutas (ej. `auth`).

Cada capa solo llama a la de abajo. Ver `EXPLICACION_FLUJO.txt` para el
flujo detallado del login, pacientes y exámenes.

## Funcionalidades

- Autenticación con roles (administrador, optómetra, cajero)
- Dashboard con recordatorios de "lentes listos" (WhatsApp)
- Gestión de pacientes (validación de cédula/RUC, buscador en vivo, soft-delete)
- Agenda de citas
- Exámenes visuales con historial por paciente, firma manuscrita electrónica
  y 9 pruebas de la consulta (tabla normalizada)
- Recordatorios de lentes listos con aviso por WhatsApp
- Gestión de usuarios con perfil, avatar y cambio de contraseña

## Credenciales iniciales

| Rol        | Correo             | Clave      |
|------------|--------------------|------------|
| Admin      | admin@optica.com   | Admin123!  |
| Optómetra  | optometra@optica.com | Opto123! |
| Admin      | zyjec@yahoo.com    | 12345      |
