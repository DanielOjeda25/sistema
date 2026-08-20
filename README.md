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

### Lo que se usa todos los días

| Documento | Para qué |
|-----------|----------|
| **[CRUZNEGRA_GUIA_EQUIPO.md](CRUZNEGRA_GUIA_EQUIPO.md)** | **Trabajo actual del equipo:** las 4 fases de las vistas, con el código completo listo para copiar |
| [docs/INSTALACION.md](docs/INSTALACION.md) | Cómo correr el proyecto desde cero en una máquina nueva |

### Consulta ocasional (carpeta `docs/`)

| Documento | Para qué |
|-----------|----------|
| [CRUZNEGRA_TAREAS.md](docs/CRUZNEGRA_TAREAS.md) | Cómo se construyó el modelo de datos (tablas, modelos, controllers, rutas) — ya completo |
| [CRUZNEGRA_DATOS_PRUEBA.md](docs/CRUZNEGRA_DATOS_PRUEBA.md) | Cómo cargar roles operativos, usuarios y datos de prueba vía seeders |
| [CRUZNEGRA_MODELO_RELACIONAL.md](docs/CRUZNEGRA_MODELO_RELACIONAL.md) | Diagrama entidad-relación de la base de datos |
| [CRUZNEGRA_DIAGRAMA_CLASES.md](docs/CRUZNEGRA_DIAGRAMA_CLASES.md) | Diagrama de clases de los modelos Eloquent |

## Empezar acá

Si nunca corriste el proyecto, ir directo a [docs/INSTALACION.md](docs/INSTALACION.md).

**Marcos, Jesús y Dante:** todo su trabajo está en
[CRUZNEGRA_GUIA_EQUIPO.md](CRUZNEGRA_GUIA_EQUIPO.md), acá en la raíz — es la única guía
que necesitan abrir. El resto de la documentación está en `docs/` y es para consultar
si hace falta, no para leer ahora.

## Estado del proyecto

- ✅ Base de datos, modelos, controllers y rutas de los 7 módulos
- ✅ Login, roles y permisos
- ✅ Datos de prueba (seeders)
- ✅ Módulo de **Clientes** con sus 4 pantallas
- ⬜ Vistas de los 6 módulos restantes — **en curso, ver la guía del equipo**

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