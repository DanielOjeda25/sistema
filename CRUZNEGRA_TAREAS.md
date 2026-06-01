# CRUZNEGRA — Guía de Implementación Paso a Paso

> **Leer antes de empezar:**
> - Cada paso debe hacerse **en orden**, de arriba hacia abajo.
> - Todos los comandos se ejecutan en la **terminal**, parados dentro de la carpeta del proyecto.
> - Si algo falla, **no avanzar** al siguiente paso hasta resolver el error.

---

## ESTADO DEL AVANCE

| Paso | Estado | Quién |
|------|--------|-------|
| PASO 1 — Modificar `users` | **HECHO** (2026-05-26) | Daniel |
| PASO 2 — Crear `clientes` | **HECHO** (2026-05-26) | Daniel |
| PASO 3 — Crear `proyectos` | **HECHO** (2026-05-28) | Jesus |
| PASO 4 — Crear `solicitudes_cambio` | **HECHO** (2026-05-28) | Daniel |
| PASO 5 — Crear `tareas` | **HECHO** (2026-05-29) | crookdteeth |
| PASO 6 — Crear `hitos` | **HECHO** (2026-05-29) | Dantex-dmv |
| PASO 7 — Crear `entregables_ia` | **HECHO** (2026-05-29) | Dantex-dmv |
| PASO 8 — Crear `facturas` | **HECHO** (2026-05-29) | Daniel |
| PASO 9 — Ejecutar migraciones | **HECHO** (2026-06-01) | Daniel |
| PASO 10 — Modelos | **HECHO** (2026-06-01) | Daniel |
| PASO 11 — Controladores | **HECHO** (2026-06-01) | Daniel |
| PASO 12 — Rutas | **HECHO** (2026-06-01) | Daniel |

> **Si te sumás ahora: empezá desde el PASO 2.** El archivo del PASO 1 ya está creado en `database/migrations/2026_05_26_215211_add_fields_to_users_table.php`. **NO lo borres ni lo edites.**

---

## ¿Por qué este orden?

Algunas tablas dependen de otras (usan su `id` como referencia). Si creamos una tabla que depende de otra que todavía no existe, Laravel dará error.

```
users ──┐
        ├──► proyectos ──┬──► solicitudes_cambio ──► tareas
clientes┘                ├──► hitos
                         ├──► entregables_ia
                         └──► facturas
```

---

## PASO 1 — Modificar la tabla `users` (ya existe) — **[HECHO]**

> Migración creada en `database/migrations/2026_05_26_215211_add_fields_to_users_table.php`. No re-ejecutar este paso. Saltar al PASO 2.

### 1.1 Crear la migración

```bash
php artisan make:migration add_fields_to_users_table
```

### 1.2 Abrir el archivo generado

Ir a la carpeta `database/migrations/` y abrir el archivo que termina en `add_fields_to_users_table.php`.

Reemplazar **todo** el contenido con esto:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido')->after('name');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['apellido', 'estado']);
        });
    }
};
```

---

## PASO 2 — Crear la tabla `clientes` — **[HECHO]**

> Migración creada en `database/migrations/2026_05_26_221716_create_clientes_table.php`. No re-ejecutar este paso. Saltar al PASO 3.

### 2.1 Crear la migración

```bash
php artisan make:migration create_clientes_table
```

### 2.2 Abrir el archivo generado

Ir a `database/migrations/` y abrir el archivo que termina en `create_clientes_table.php`.

Reemplazar **todo** el contenido con esto:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->string('empresa')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
```

---

## PASO 3 — Crear la tabla `proyectos` — **[HECHO]**

> Migración creada en `database/migrations/2026_05_28_033949_create_proyectos_table.php`. No re-ejecutar este paso. Saltar al PASO 4.

> Depende de: `users` y `clientes` (deben existir primero — ya los creamos).

### 3.1 Crear la migración

```bash
php artisan make:migration create_proyectos_table
```

### 3.2 Abrir el archivo generado

Ir a `database/migrations/` y abrir el archivo que termina en `create_proyectos_table.php`.

Reemplazar **todo** el contenido con esto:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin_estimada')->nullable();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completado', 'cancelado'])->default('pendiente');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('pm_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};
```

---

## PASO 4 — Crear la tabla `solicitudes_cambio` — **[HECHO]**

> Migración creada en `database/migrations/2026_05_28_202909_create_solicitudes_cambio_table.php`. No re-ejecutar este paso. Saltar al PASO 5.

> Depende de: `proyectos` (debe existir primero — ya la creamos).

### 4.1 Crear la migración

```bash
php artisan make:migration create_solicitudes_cambio_table
```

### 4.2 Abrir el archivo generado

Ir a `database/migrations/` y abrir el archivo que termina en `create_solicitudes_cambio_table.php`.

Reemplazar **todo** el contenido con esto:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_cambio', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('solicitado_por')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_cambio');
    }
};
```

---

## PASO 5 — Crear la tabla `tareas` — **[HECHO]**

> Migración creada en `database/migrations/2026_05_29_000000_create_tareas_table.php`. No re-ejecutar este paso. Saltar al PASO 6.

> Depende de: `proyectos`, `users` y `solicitudes_cambio` (todas ya creadas).

### 5.1 Crear la migración

```bash
php artisan make:migration create_tareas_table
```

### 5.2 Abrir el archivo generado

Ir a `database/migrations/` y abrir el archivo que termina en `create_tareas_table.php`.

Reemplazar **todo** el contenido con esto:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada', 'cancelada'])->default('pendiente');
            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');
            $table->date('fecha_limite')->nullable();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('asignado_a')->constrained('users')->onDelete('cascade');
            $table->foreignId('solicitud_cambio_id')->nullable()->constrained('solicitudes_cambio')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
```

---

## PASO 6 — Crear la tabla `hitos` — **[HECHO]**

> Migración creada en `database/migrations/2026_05_29_204456_create_hitos_table.php`. No re-ejecutar este paso. Saltar al PASO 7.

> Depende de: `proyectos` (ya creada).

### 6.1 Crear la migración

```bash
php artisan make:migration create_hitos_table
```

### 6.2 Abrir el archivo generado

Ir a `database/migrations/` y abrir el archivo que termina en `create_hitos_table.php`.

Reemplazar **todo** el contenido con esto:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hitos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->date('fecha_objetivo');
            $table->boolean('completado')->default(false);
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hitos');
    }
};
```

---

## PASO 7 — Crear la tabla `entregables_ia`

> Depende de: `proyectos` y `users` (ya creadas).

### 7.1 Crear la migración

```bash
php artisan make:migration create_entregables_ia_table
```

### 7.2 Abrir el archivo generado

Ir a `database/migrations/` y abrir el archivo que termina en `create_entregables_ia_table.php`.

Reemplazar **todo** el contenido con esto:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregables_ia', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('contenido');
            $table->string('tipo')->default('documento');
            $table->enum('estado', ['borrador', 'revisado', 'aprobado'])->default('borrador');
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('generado_por')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregables_ia');
    }
};
```

---

## PASO 8 — Crear la tabla `facturas`

> Depende de: `proyectos` y `users` (ya creadas).

### 8.1 Crear la migración

```bash
php artisan make:migration create_facturas_table
```

### 8.2 Abrir el archivo generado

Ir a `database/migrations/` y abrir el archivo que termina en `create_facturas_table.php`.

Reemplazar **todo** el contenido con esto:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->decimal('monto', 10, 2);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['pendiente', 'pagada', 'vencida'])->default('pendiente');
            $table->text('detalle')->nullable();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('emitida_por')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
```

---

## PASO 9 — Ejecutar TODAS las migraciones

Una vez que los 8 archivos están editados, ejecutar este único comando para crear todas las tablas en la base de datos:

```bash
php artisan migrate
```

### ¿Cómo saber si funcionó?

El output debe mostrar algo así:

```
INFO  Running migrations.

  YYYY_MM_DD_XXXXXX_add_fields_to_users_table ............. DONE
  YYYY_MM_DD_XXXXXX_create_clientes_table ................. DONE
  YYYY_MM_DD_XXXXXX_create_proyectos_table ................ DONE
  YYYY_MM_DD_XXXXXX_create_solicitudes_cambio_table ....... DONE
  YYYY_MM_DD_XXXXXX_create_tareas_table ................... DONE
  YYYY_MM_DD_XXXXXX_create_hitos_table .................... DONE
  YYYY_MM_DD_XXXXXX_create_entregables_ia_table ........... DONE
  YYYY_MM_DD_XXXXXX_create_facturas_table ................. DONE
```

### Si algo sale mal (empezar desde cero)

```bash
php artisan migrate:fresh
```

> ⚠️ Este comando **borra todo** y vuelve a crear las tablas. Usarlo solo en desarrollo, nunca en producción.

---

## PASO 10 — Crear los Modelos

Un modelo por tabla. Ejecutar cada comando:

```bash
php artisan make:model Cliente
php artisan make:model Proyecto
php artisan make:model SolicitudCambio
php artisan make:model Tarea
php artisan make:model Hito
php artisan make:model EntregableIA
php artisan make:model Factura
```

Los archivos se crean en `app/Models/`.

---

## PASO 11 — Crear los Controladores

```bash
php artisan make:controller ClienteController --resource
php artisan make:controller ProyectoController --resource
php artisan make:controller TareaController --resource
php artisan make:controller HitoController --resource
php artisan make:controller SolicitudCambioController --resource
php artisan make:controller EntregableIAController --resource
php artisan make:controller FacturaController --resource
```

Los archivos se crean en `app/Http/Controllers/`.  
El flag `--resource` genera automáticamente los métodos: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.

---

## PASO 12 — Registrar las rutas

Abrir el archivo `routes/web.php` y agregar al final:

```php
Route::resource('clientes', ClienteController::class);
Route::resource('proyectos', ProyectoController::class);
Route::resource('tareas', TareaController::class);
Route::resource('hitos', HitoController::class);
Route::resource('solicitudes-cambio', SolicitudCambioController::class);
Route::resource('entregables', EntregableIAController::class);
Route::resource('facturas', FacturaController::class);
```

Para verificar que las rutas quedaron registradas:

```bash
php artisan route:list
```

---

## RESUMEN DE TABLAS Y DEPENDENCIAS

| # | Tabla | Depende de |
|---|-------|-----------|
| 1 | `users` (modificar) | — |
| 2 | `clientes` | — |
| 3 | `proyectos` | `users`, `clientes` |
| 4 | `solicitudes_cambio` | `proyectos` |
| 5 | `tareas` | `proyectos`, `users`, `solicitudes_cambio` |
| 6 | `hitos` | `proyectos` |
| 7 | `entregables_ia` | `proyectos`, `users` |
| 8 | `facturas` | `proyectos`, `users` |

---

## ROLES DEL SISTEMA

Son los **5 roles únicos** del sistema. Cada usuario tiene uno o más.

| Rol | Qué puede hacer |
|-----|----------------|
| `Jefe` | Aprueba cambios, emite facturas, consulta reportes, edita roles de usuarios. |
| `PM` | Gestiona proyectos, asigna tareas, puede ver la lista de usuarios. |
| `PO` | Valida tareas y entregables IA. |
| `Programador` | Ejecuta tareas asignadas y marca finalización. |
| `Cliente` | Solo lectura — consulta avance del proyecto y solicita cambios. |

> El acceso a `/usuarios` está restringido a `Jefe` y `PM`. La edición de roles solo la puede hacer `Jefe`. Ver [routes/web.php:38-52](routes/web.php#L38-L52).

---

## ¿Y AHORA QUÉ? — SIGUIENTE FASE

Los 12 pasos de esta guía están **completos**: las tablas existen, los modelos tienen relaciones, los controllers funcionan, las rutas están registradas.

Pero el sistema todavía **no es usable** porque:

1. **No hay datos para probar.** Las pantallas y endpoints existen, pero sin clientes, proyectos, tareas o usuarios cargados, no se puede ver nada en acción.
2. **No hay vistas Blade.** Los controllers apuntan a `view('clientes.index')`, etc., pero esos archivos no existen todavía. Cualquier `GET` en el navegador va a tirar "View not found".

La siguiente fase es **cargar datos de prueba** vía seeders, para que cuando empecemos las vistas haya algo concreto para mostrar. Ver:

| Guía | Para qué |
|------|----------|
| [CRUZNEGRA_DATOS_PRUEBA.md](CRUZNEGRA_DATOS_PRUEBA.md) | Paso a paso para cargar roles operativos, usuarios de prueba y datos de ejemplo para cada tabla |
