# CRUZNEGRA — Guía de Datos de Prueba (Seeders)

> **Leer antes de empezar:**
> - Antes de tocar esta guía, los **12 pasos** de [CRUZNEGRA_TAREAS.md](CRUZNEGRA_TAREAS.md) deben estar completos.
> - Cada paso de acá también va **en orden**. Las tablas que dependen de otras necesitan que primero se siembren las dependencias.
> - Si una migración fue cambiada después de correr `migrate`, primero hay que correr `php artisan migrate:fresh` (borra todo) **antes** de seedear de nuevo.

---

## ESTADO DEL AVANCE

| Paso | Estado | Quién |
|------|--------|-------|
| PASO 1 — Actualizar `RoleSeeder` (roles operativos) | Pendiente | — |
| PASO 2 — Actualizar `UserSeeder` (usuarios de prueba por rol) | Pendiente | — |
| PASO 3 — Crear `ClienteSeeder` | Pendiente | — |
| PASO 4 — Crear `ProyectoSeeder` | Pendiente | — |
| PASO 5 — Crear `HitoSeeder` | Pendiente | — |
| PASO 6 — Crear `SolicitudCambioSeeder` | Pendiente | — |
| PASO 7 — Crear `TareaSeeder` | Pendiente | — |
| PASO 8 — Crear `EntregableIASeeder` | Pendiente | — |
| PASO 9 — Crear `FacturaSeeder` | Pendiente | — |
| PASO 10 — Registrar todo en `DatabaseSeeder` | Pendiente | — |
| PASO 11 — Ejecutar el seed | Pendiente | — |

---

## ¿Por qué este orden?

Los seeders tienen las mismas dependencias que las tablas. Si seedeás `Proyecto` antes que `Cliente`, no hay `cliente_id` válido para asignar.

```
Roles ──► Users ──► Clientes ──► Proyectos ──┬──► Hitos
                                              ├──► SolicitudCambio ──► Tareas
                                              ├──► EntregableIA
                                              └──► Factura
```

Las **Tareas dependen de SolicitudCambio** (la FK es opcional, pero si queremos datos realistas conviene tener solicitudes primero).

---

## ESTADO INICIAL

El proyecto **ya tiene** estos seeders escritos, pero **con roles que YA NO USAMOS** (Administrador, Supervisor, Empleado):

- `RoleSeeder.php` — vamos a **reemplazarlo** entero por los 5 roles reales del negocio.
- `UserSeeder.php` — vamos a **reemplazarlo** entero por un usuario de prueba por cada rol.
- `DatabaseSeeder.php` — ya llama a `RoleSeeder` y `UserSeeder`. Le vamos a sumar todos los nuevos.

Los 5 roles del sistema son: **Jefe, PM, PO, Programador, Cliente**.

---

## PASO 1 — Reescribir `RoleSeeder` con los 5 roles reales

### 1.1 Abrir el archivo

`database/seeders/RoleSeeder.php`

### 1.2 Reemplazar TODO el contenido con esto

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ===== PERMISOS =====
        // Permiso fino para "editar el rol de otros usuarios".
        $permisoEditarRoles = Permission::firstOrCreate(['name' => 'editar_roles']);

        // ===== ROLES DEL SISTEMA =====
        // Son los 5 únicos. Cualquier usuario tiene uno o más.
        $jefe        = Role::firstOrCreate(['name' => 'Jefe']);
        $pm          = Role::firstOrCreate(['name' => 'PM']);
        $po          = Role::firstOrCreate(['name' => 'PO']);
        $programador = Role::firstOrCreate(['name' => 'Programador']);
        $cliente     = Role::firstOrCreate(['name' => 'Cliente']);

        // ===== ASIGNACIÓN DE PERMISOS =====
        // El Jefe es el único que puede editar roles de otros usuarios.
        $jefe->givePermissionTo($permisoEditarRoles);
    }
}
```

### 1.3 ¿Qué cambió respecto del original?

- **Borrados:** `Administrador`, `Supervisor`, `Empleado` — ya no existen en el sistema.
- **Agregados:** `Jefe`, `PM`, `PO`, `Programador`. `Cliente` se mantiene.
- `Role::create` → `Role::firstOrCreate` para que el seeder sea **idempotente** (correrlo dos veces no rompe).
- El permiso `editar_roles` ahora se asigna al `Jefe` (antes era el Administrador).

---

## PASO 2 — Reescribir `UserSeeder` con un usuario por rol

### 2.1 Abrir el archivo

`database/seeders/UserSeeder.php`

### 2.2 Reemplazar TODO el contenido con esto

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Un usuario de prueba por cada rol del negocio.
        // Los emails se usan en los demás seeders (ProyectoSeeder, etc.)
        // para no depender de IDs auto-incrementales.

        $jefe = User::firstOrCreate(
            ['email' => 'jefe@example.com'],
            ['name' => 'Roberto Acosta', 'password' => bcrypt('1234')]
        );
        $jefe->syncRoles(['Jefe']);

        $pm = User::firstOrCreate(
            ['email' => 'pm@example.com'],
            ['name' => 'Laura Mendez', 'password' => bcrypt('1234')]
        );
        $pm->syncRoles(['PM']);

        $po = User::firstOrCreate(
            ['email' => 'po@example.com'],
            ['name' => 'Diego Sosa', 'password' => bcrypt('1234')]
        );
        $po->syncRoles(['PO']);

        $programador = User::firstOrCreate(
            ['email' => 'dev@example.com'],
            ['name' => 'Sofia Ruiz', 'password' => bcrypt('1234')]
        );
        $programador->syncRoles(['Programador']);

        $cliente = User::firstOrCreate(
            ['email' => 'cliente@example.com'],
            ['name' => 'Juan Perez', 'password' => bcrypt('1234')]
        );
        $cliente->syncRoles(['Cliente']);
    }
}
```

### 2.3 Resumen de cuentas

| Email | Password | Rol |
|-------|----------|-----|
| `jefe@example.com` | `1234` | Jefe |
| `pm@example.com` | `1234` | PM |
| `po@example.com` | `1234` | PO |
| `dev@example.com` | `1234` | Programador |
| `cliente@example.com` | `1234` | Cliente |

> **Estas credenciales son para desarrollo solamente.** Nunca usar `1234` en producción.

### 2.4 ¿Por qué `syncRoles` y no `assignRole`?

- `assignRole(['Jefe'])` agrega ese rol sin tocar los demás.
- `syncRoles(['Jefe'])` reemplaza **todos los roles** del usuario por solo ese.

Como el seeder es idempotente, queremos `syncRoles` para que correrlo dos veces deje el estado deseado, no acumule roles viejos si alguien cambió algo a mano.

---

## PASO 3 — Crear `ClienteSeeder`

### 3.1 Crear el archivo

```bash
php artisan make:seeder ClienteSeeder
```

### 3.2 Abrir `database/seeders/ClienteSeeder.php` y reemplazar todo con

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Mariana',
                'apellido' => 'Lopez',
                'email' => 'mariana@constructoraLR.com',
                'telefono' => '0981-111-222',
                'empresa' => 'Constructora L&R',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Federico',
                'apellido' => 'Gimenez',
                'email' => 'fede@gimenezabog.com.py',
                'telefono' => '0982-333-444',
                'empresa' => 'Gimenez Abogados',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Patricia',
                'apellido' => 'Martinez',
                'email' => 'patri@cooperativaUnion.com',
                'telefono' => '0983-555-666',
                'empresa' => 'Cooperativa Union',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Hernan',
                'apellido' => 'Vera',
                'email' => 'hvera@autopartesvera.com',
                'telefono' => null,
                'empresa' => 'Autopartes Vera SA',
                'estado' => 'inactivo',
            ],
            [
                'nombre' => 'Lucia',
                'apellido' => 'Fernandez',
                'email' => 'lucia.f@gmail.com',
                'telefono' => '0984-777-888',
                'empresa' => null,
                'estado' => 'activo',
            ],
        ];

        foreach ($clientes as $datos) {
            Cliente::firstOrCreate(['email' => $datos['email']], $datos);
        }
    }
}
```

### 3.3 Por qué `firstOrCreate` con `email`

`email` es `unique` en la tabla, así que si corremos el seeder dos veces, no duplica clientes.

---

## PASO 4 — Crear `ProyectoSeeder`

> Depende de: `clientes` y `users` (PM).

### 4.1 Crear el archivo

```bash
php artisan make:seeder ProyectoSeeder
```

### 4.2 Reemplazar todo el contenido con

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\User;

class ProyectoSeeder extends Seeder
{
    public function run(): void
    {
        // Recuperamos PMs por email para no depender del id auto-incremental.
        $pmLaura = User::where('email', 'pm@example.com')->first();
        $pmJefe  = User::where('email', 'jefe@example.com')->first();

        $clienteLR    = Cliente::where('email', 'mariana@constructoraLR.com')->first();
        $clienteAbog  = Cliente::where('email', 'fede@gimenezabog.com.py')->first();
        $clienteCoop  = Cliente::where('email', 'patri@cooperativaUnion.com')->first();
        $clienteLucia = Cliente::where('email', 'lucia.f@gmail.com')->first();

        $proyectos = [
            [
                'nombre' => 'Sistema de obra L&R',
                'descripcion' => 'Plataforma interna para seguimiento de obra: avance, presupuesto, materiales.',
                'fecha_inicio' => '2026-03-01',
                'fecha_fin_estimada' => '2026-09-30',
                'estado' => 'en_progreso',
                'cliente_id' => $clienteLR->id,
                'pm_id' => $pmLaura->id,
            ],
            [
                'nombre' => 'Gestor de expedientes Gimenez',
                'descripcion' => 'CRM legal con flujo de aprobación y notificaciones.',
                'fecha_inicio' => '2026-04-15',
                'fecha_fin_estimada' => '2026-08-15',
                'estado' => 'en_progreso',
                'cliente_id' => $clienteAbog->id,
                'pm_id' => $pmLaura->id,
            ],
            [
                'nombre' => 'Portal del socio — Cooperativa Union',
                'descripcion' => 'Sitio público para que los socios consulten saldos y soliciten créditos.',
                'fecha_inicio' => '2026-01-10',
                'fecha_fin_estimada' => '2026-05-20',
                'estado' => 'completado',
                'cliente_id' => $clienteCoop->id,
                'pm_id' => $pmJefe->id,
            ],
            [
                'nombre' => 'Landing personal — Lucia',
                'descripcion' => 'Página de presentación profesional con CV y portfolio.',
                'fecha_inicio' => '2026-05-20',
                'fecha_fin_estimada' => '2026-06-30',
                'estado' => 'pendiente',
                'cliente_id' => $clienteLucia->id,
                'pm_id' => $pmLaura->id,
            ],
        ];

        foreach ($proyectos as $datos) {
            Proyecto::firstOrCreate(
                ['nombre' => $datos['nombre']],
                $datos
            );
        }
    }
}
```

### 4.3 Detalle clave

- Buscamos los users y clientes **por email**, no por `id`. Si alguien corre los seeders por separado y los ids saltan, esto sigue funcionando.
- Asumimos que `PASO 2` y `PASO 3` ya corrieron — si no, vas a tener errores tipo "Trying to read property 'id' on null".

---

## PASO 5 — Crear `HitoSeeder`

> Depende de: `proyectos`.

### 5.1 Crear y editar

```bash
php artisan make:seeder HitoSeeder
```

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hito;
use App\Models\Proyecto;

class HitoSeeder extends Seeder
{
    public function run(): void
    {
        $obraLR    = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor    = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();
        $portal    = Proyecto::where('nombre', 'Portal del socio — Cooperativa Union')->first();

        $hitos = [
            ['nombre' => 'Kick-off y firma de contrato', 'descripcion' => null,                                    'fecha_objetivo' => '2026-03-05', 'completado' => true,  'proyecto_id' => $obraLR->id],
            ['nombre' => 'Entrega del módulo de avance', 'descripcion' => 'Visualización de etapas por obra.',     'fecha_objetivo' => '2026-06-15', 'completado' => false, 'proyecto_id' => $obraLR->id],
            ['nombre' => 'Demo final L&R',                'descripcion' => 'Presentación al cliente.',              'fecha_objetivo' => '2026-09-25', 'completado' => false, 'proyecto_id' => $obraLR->id],

            ['nombre' => 'Diseño de flujo de expedientes', 'descripcion' => null,                                   'fecha_objetivo' => '2026-05-15', 'completado' => true,  'proyecto_id' => $gestor->id],
            ['nombre' => 'Sprint de integración email',    'descripcion' => 'SMTP del estudio.',                    'fecha_objetivo' => '2026-07-10', 'completado' => false, 'proyecto_id' => $gestor->id],

            ['nombre' => 'Lanzamiento portal',             'descripcion' => 'Entrega final, ya en producción.',     'fecha_objetivo' => '2026-05-18', 'completado' => true,  'proyecto_id' => $portal->id],
        ];

        foreach ($hitos as $datos) {
            Hito::firstOrCreate(
                ['nombre' => $datos['nombre'], 'proyecto_id' => $datos['proyecto_id']],
                $datos
            );
        }
    }
}
```

---

## PASO 6 — Crear `SolicitudCambioSeeder`

> Depende de: `proyectos` y `users`.

### 6.1 Crear y editar

```bash
php artisan make:seeder SolicitudCambioSeeder
```

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\User;

class SolicitudCambioSeeder extends Seeder
{
    public function run(): void
    {
        $obraLR  = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor  = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();
        $cliente = User::where('email', 'cliente@example.com')->first();
        $pm      = User::where('email', 'pm@example.com')->first();

        $solicitudes = [
            [
                'titulo' => 'Agregar reporte de horas por obra',
                'descripcion' => 'El cliente pide un PDF mensual con horas hombre por obra.',
                'estado' => 'aprobada',
                'prioridad' => 'alta',
                'proyecto_id' => $obraLR->id,
                'solicitado_por' => $cliente->id,
            ],
            [
                'titulo' => 'Cambiar paleta de colores',
                'descripcion' => 'El cliente quiere usar los colores corporativos.',
                'estado' => 'pendiente',
                'prioridad' => 'baja',
                'proyecto_id' => $obraLR->id,
                'solicitado_por' => $pm->id,
            ],
            [
                'titulo' => 'Notificaciones por WhatsApp',
                'descripcion' => 'Reemplazar email por WhatsApp para abogados senior.',
                'estado' => 'rechazada',
                'prioridad' => 'media',
                'proyecto_id' => $gestor->id,
                'solicitado_por' => $cliente->id,
            ],
        ];

        foreach ($solicitudes as $datos) {
            SolicitudCambio::firstOrCreate(
                ['titulo' => $datos['titulo'], 'proyecto_id' => $datos['proyecto_id']],
                $datos
            );
        }
    }
}
```

---

## PASO 7 — Crear `TareaSeeder`

> Depende de: `proyectos`, `users` y `solicitudes_cambio` (la última es opcional).

### 7.1 Crear y editar

```bash
php artisan make:seeder TareaSeeder
```

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\Tarea;
use App\Models\User;

class TareaSeeder extends Seeder
{
    public function run(): void
    {
        $obraLR  = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor  = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();
        $portal  = Proyecto::where('nombre', 'Portal del socio — Cooperativa Union')->first();
        $dev     = User::where('email', 'dev@example.com')->first();
        $pm      = User::where('email', 'pm@example.com')->first();

        $solicitudReporte = SolicitudCambio::where('titulo', 'Agregar reporte de horas por obra')->first();

        $tareas = [
            // Obra L&R
            [
                'titulo' => 'Diseñar pantalla de avance por obra',
                'descripcion' => 'Mockup en Figma + revisión con cliente.',
                'estado' => 'completada',
                'prioridad' => 'alta',
                'fecha_limite' => '2026-04-10',
                'proyecto_id' => $obraLR->id,
                'asignado_a' => $pm->id,
                'solicitud_cambio_id' => null,
            ],
            [
                'titulo' => 'Implementar listado de obras',
                'descripcion' => 'Endpoint + vista index.',
                'estado' => 'en_progreso',
                'prioridad' => 'alta',
                'fecha_limite' => '2026-05-20',
                'proyecto_id' => $obraLR->id,
                'asignado_a' => $dev->id,
                'solicitud_cambio_id' => null,
            ],
            [
                'titulo' => 'PDF reporte mensual de horas',
                'descripcion' => 'Originado por solicitud de cambio del cliente.',
                'estado' => 'pendiente',
                'prioridad' => 'alta',
                'fecha_limite' => '2026-07-15',
                'proyecto_id' => $obraLR->id,
                'asignado_a' => $dev->id,
                'solicitud_cambio_id' => $solicitudReporte?->id,
            ],

            // Gestor expedientes
            [
                'titulo' => 'Crear modelo Expediente',
                'descripcion' => null,
                'estado' => 'completada',
                'prioridad' => 'media',
                'fecha_limite' => '2026-05-05',
                'proyecto_id' => $gestor->id,
                'asignado_a' => $dev->id,
                'solicitud_cambio_id' => null,
            ],
            [
                'titulo' => 'Login con dos factores',
                'descripcion' => 'Requerimiento legal.',
                'estado' => 'pendiente',
                'prioridad' => 'alta',
                'fecha_limite' => '2026-06-20',
                'proyecto_id' => $gestor->id,
                'asignado_a' => $dev->id,
                'solicitud_cambio_id' => null,
            ],

            // Portal cooperativa
            [
                'titulo' => 'Migrar datos legacy a producción',
                'descripcion' => 'Importación de socios histórica.',
                'estado' => 'completada',
                'prioridad' => 'alta',
                'fecha_limite' => '2026-05-10',
                'proyecto_id' => $portal->id,
                'asignado_a' => $dev->id,
                'solicitud_cambio_id' => null,
            ],
        ];

        foreach ($tareas as $datos) {
            Tarea::firstOrCreate(
                ['titulo' => $datos['titulo'], 'proyecto_id' => $datos['proyecto_id']],
                $datos
            );
        }
    }
}
```

### 7.2 El operador `?->`

`$solicitudReporte?->id` — si la solicitud no existe (por ejemplo porque no se corrió el PASO 6), no rompe, queda `null`. Es válido porque la FK `solicitud_cambio_id` es nullable.

---

## PASO 8 — Crear `EntregableIASeeder`

> Depende de: `proyectos` y `users`.

```bash
php artisan make:seeder EntregableIASeeder
```

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Models\User;

class EntregableIASeeder extends Seeder
{
    public function run(): void
    {
        $obraLR = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();
        $po     = User::where('email', 'po@example.com')->first();

        $entregables = [
            [
                'titulo' => 'Borrador de manual de usuario',
                'contenido' => 'Generado por IA a partir de las pantallas de avance. Necesita revisión humana.',
                'tipo' => 'documento',
                'estado' => 'borrador',
                'proyecto_id' => $obraLR->id,
                'generado_por' => $po->id,
            ],
            [
                'titulo' => 'Resumen de reunión 2026-05-22',
                'contenido' => 'Transcripción procesada de la call con el cliente.',
                'tipo' => 'transcripcion',
                'estado' => 'revisado',
                'proyecto_id' => $obraLR->id,
                'generado_por' => $po->id,
            ],
            [
                'titulo' => 'Esquema legal del flujo de expedientes',
                'contenido' => 'Diagrama generado a partir de los requerimientos del estudio.',
                'tipo' => 'diagrama',
                'estado' => 'aprobado',
                'proyecto_id' => $gestor->id,
                'generado_por' => $po->id,
            ],
        ];

        foreach ($entregables as $datos) {
            EntregableIA::firstOrCreate(
                ['titulo' => $datos['titulo'], 'proyecto_id' => $datos['proyecto_id']],
                $datos
            );
        }
    }
}
```

---

## PASO 9 — Crear `FacturaSeeder`

> Depende de: `proyectos` y `users`.

```bash
php artisan make:seeder FacturaSeeder
```

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Factura;
use App\Models\Proyecto;
use App\Models\User;

class FacturaSeeder extends Seeder
{
    public function run(): void
    {
        $obraLR = Proyecto::where('nombre', 'Sistema de obra L&R')->first();
        $gestor = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->first();
        $portal = Proyecto::where('nombre', 'Portal del socio — Cooperativa Union')->first();
        $jefe   = User::where('email', 'jefe@example.com')->first();

        $facturas = [
            [
                'numero' => 'F-2026-0001',
                'monto' => 4500000.00,
                'fecha_emision' => '2026-03-15',
                'fecha_vencimiento' => '2026-04-15',
                'estado' => 'pagada',
                'detalle' => 'Anticipo del 30% del proyecto Sistema de obra L&R.',
                'proyecto_id' => $obraLR->id,
                'emitida_por' => $jefe->id,
            ],
            [
                'numero' => 'F-2026-0002',
                'monto' => 7500000.00,
                'fecha_emision' => '2026-06-01',
                'fecha_vencimiento' => '2026-07-01',
                'estado' => 'pendiente',
                'detalle' => 'Avance del 50% — proyecto Sistema de obra L&R.',
                'proyecto_id' => $obraLR->id,
                'emitida_por' => $jefe->id,
            ],
            [
                'numero' => 'F-2026-0003',
                'monto' => 3200000.00,
                'fecha_emision' => '2026-04-20',
                'fecha_vencimiento' => '2026-05-20',
                'estado' => 'pagada',
                'detalle' => 'Pago único — Gestor expedientes (anticipo).',
                'proyecto_id' => $gestor->id,
                'emitida_por' => $jefe->id,
            ],
            [
                'numero' => 'F-2026-0004',
                'monto' => 9000000.00,
                'fecha_emision' => '2026-05-20',
                'fecha_vencimiento' => '2026-06-20',
                'estado' => 'pagada',
                'detalle' => 'Facturación final — Portal del socio.',
                'proyecto_id' => $portal->id,
                'emitida_por' => $jefe->id,
            ],
        ];

        foreach ($facturas as $datos) {
            Factura::firstOrCreate(['numero' => $datos['numero']], $datos);
        }
    }
}
```

---

## PASO 10 — Registrar todos los seeders en `DatabaseSeeder`

### 10.1 Abrir el archivo

`database/seeders/DatabaseSeeder.php`

### 10.2 Reemplazar todo el contenido con

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // El orden importa — cada seeder asume que los anteriores ya corrieron.
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            ClienteSeeder::class,
            ProyectoSeeder::class,
            HitoSeeder::class,
            SolicitudCambioSeeder::class,
            TareaSeeder::class,
            EntregableIASeeder::class,
            FacturaSeeder::class,
        ]);
    }
}
```

> Si agregás un seeder nuevo en el futuro, sumalo a este array en la posición correcta según sus dependencias.

---

## PASO 11 — Ejecutar el seed

Tenés **dos opciones**, según el estado actual de tu base.

### Opción A — Solo agregar datos (sin borrar lo que ya hay)

Si ya tenés tu DB con datos reales que no querés perder:

```bash
php artisan db:seed
```

Esto corre `DatabaseSeeder` y sus seeders. Gracias a `firstOrCreate`, no rompe si ya hay registros.

### Opción B — Empezar de cero (RECOMENDADO en desarrollo)

Si tu DB tiene datos viejos o medio rotos y querés un estado limpio:

```bash
php artisan migrate:fresh --seed
```

⚠️ **`migrate:fresh` borra TODAS las tablas y las vuelve a crear.** Solo en desarrollo. Nunca en producción.

### ¿Cómo saber si funcionó?

El output debe mostrar:

```
INFO  Running migrations.
... (todas las migraciones DONE) ...

INFO  Seeding database.
  Database\Seeders\RoleSeeder ............... RUNNING
  Database\Seeders\RoleSeeder ............... 50ms DONE
  Database\Seeders\UserSeeder ............... RUNNING
  Database\Seeders\UserSeeder ............... 80ms DONE
  Database\Seeders\ClienteSeeder ............ RUNNING
  Database\Seeders\ClienteSeeder ............ 25ms DONE
  ... (uno por uno hasta FacturaSeeder DONE) ...
```

Si alguno tira **error**, **parar** y avisar en el grupo. No correr el siguiente paso hasta resolver.

### Verificar los datos

```bash
php artisan tinker
```

Y dentro del tinker:

```php
\App\Models\Cliente::count();          // → 5
\App\Models\Proyecto::count();         // → 4
\App\Models\Tarea::count();            // → 6
\App\Models\Factura::sum('monto');     // → 24200000

// Probar una relación
\App\Models\Proyecto::first()->cliente;
\App\Models\Proyecto::first()->tareas;
```

Si los counts son distintos de los esperados, alguno de los seeders falló silenciosamente. Revisar el último seeder modificado.

---

## PROBLEMAS COMUNES

| Error | Causa probable | Solución |
|-------|---------------|----------|
| `Attempt to read property "id" on null` en un seeder | El user/cliente de referencia no existe (PASO previo no corrió) | Correr todo desde cero con `migrate:fresh --seed` |
| `SQLSTATE[23000]: Integrity constraint violation` | Estás intentando insertar duplicados | Reemplazar `Cliente::create` por `Cliente::firstOrCreate` en el seeder |
| `Class "Database\Seeders\ClienteSeeder" not found` | Olvidaste registrarlo en `DatabaseSeeder` | Volver al PASO 10 y agregarlo al array |
| `Mass assignment to undefined attribute` | Falta el campo en `$fillable` del modelo | Abrir el modelo y agregar la columna a `protected $fillable` |
| Roles no aparecen en `$user->roles` | El seeder corrió pero la asignación falló | Verificar que Spatie esté configurado y que `RoleSeeder` corra ANTES de `UserSeeder` |

---

## RESUMEN — COMANDOS EN ORDEN

```bash
# 1. Crear todos los seeders
php artisan make:seeder ClienteSeeder
php artisan make:seeder ProyectoSeeder
php artisan make:seeder HitoSeeder
php artisan make:seeder SolicitudCambioSeeder
php artisan make:seeder TareaSeeder
php artisan make:seeder EntregableIASeeder
php artisan make:seeder FacturaSeeder

# 2. Editar los 7 archivos con el contenido de cada PASO de esta guía.
# 3. Editar también RoleSeeder, UserSeeder y DatabaseSeeder (PASOs 1, 2 y 10).

# 4. Correr el seed
php artisan migrate:fresh --seed
```

---

## SIGUIENTE FASE

Cuando esto esté hecho, la DB tiene datos realistas y los usuarios pueden loguearse. El próximo paso es **armar las vistas Blade** para que el sistema sea usable desde el navegador. Eso queda para una tercera guía.
