# CRUZNEGRA — Sistema de Gestión

Sistema interno para gestión de clientes, proyectos, tareas, hitos, entregables y facturación.

## Stack

- Laravel 12 + PHP 8.2
- Spatie Permission (roles y permisos)
- Laravel Breeze (autenticación)
- Laravel Auditing (registro de cambios)
- MySQL (vía Laragon)
- Vite + TailwindCSS + Alpine.js

## Documentación

| Documento | Para qué |
|-----------|----------|
| [INSTALACION.md](INSTALACION.md) | Cómo correr el proyecto desde cero en una máquina nueva |
| [CRUZNEGRA_TAREAS.md](CRUZNEGRA_TAREAS.md) | Guía paso a paso para construir el modelo de datos (tablas, modelos, controllers, rutas) |
| [CRUZNEGRA_DATOS_PRUEBA.md](CRUZNEGRA_DATOS_PRUEBA.md) | Cómo cargar roles operativos, usuarios y datos de prueba vía seeders |

## Empezar acá

Si nunca corriste el proyecto, ir directo a [INSTALACION.md](INSTALACION.md).

---

## Después de un `git pull`

Cuando bajés cambios del equipo, **siempre** corré:

```bash
php artisan migrate:fresh --seed
```

### ¿Qué hace?

- **`migrate:fresh`** — borra TODAS las tablas y las vuelve a crear desde las migraciones más nuevas. Esto te garantiza que tu DB queda igualita a la del resto del equipo.
- **`--seed`** — corre todos los seeders, así te quedan los 5 usuarios de prueba, clientes, proyectos, etc. listos para usar.

### ⚠️ Importante

Este comando **borra todos los datos** de tu DB local. Solo usalo en desarrollo. Si tenés datos manuales que querés conservar, **hacé un backup primero** o solamente corré `php artisan migrate` (sin `:fresh`) para aplicar solo las migraciones nuevas sin perder datos — aunque puede fallar si las migraciones nuevas chocan con tu schema actual.

### Si después de correr el comando te sale "Connection refused"

MySQL no está corriendo. Abrí Laragon y dale al botón **Start All**, después volvé a correrlo.

### Credenciales de los usuarios sembrados

| Email | Password | Rol |
|-------|----------|-----|
| `jefe@example.com` | `1234` | Jefe |
| `pm@example.com` | `1234` | PM |
| `po@example.com` | `1234` | PO |
| `dev@example.com` | `1234` | Programador |
| `cliente@example.com` | `1234` | Cliente |