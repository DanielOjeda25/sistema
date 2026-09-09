# CRUZNEGRA — Guía de Mejoras: el equipo + Lucas

**Todo lo que hay que hacer para las tarjetas nuevas del BACKLOG de Trello está en este archivo.**
Con el mismo estilo de `CRUZNEGRA_GUIA_EQUIPO.md`: cada tarea dice qué archivo crear,
el código completo para copiar y pegar, y cómo comprobar que funciona.

## Cómo se usa esta guía

- **Marcos, Jesús y Dante** hacen sus tarjetas completas: **backend y frontend**.
  Cada tarjeta indica qué parte de backend (PHP) y qué parte de frontend (Blade) le toca.
- **Lucas** hace únicamente dos tarjetas: el **Informe IA semanal automático** y
  los **Tests Feature de los módulos**. Son las secciones 6 y 7 de esta guía.

> **Regla de oro (igual que siempre):** copiá el bloque de código **entero**,
> desde la primera línea hasta la última. Si copiás la mitad, la página se rompe.

> **⚠️ ACTUALIZACIÓN 09/09 — leé esto antes de empezar**
>
> La estructura cambió desde que se escribió esta guía. Lo que hay que saber:
>
> - **Los formularios ya NO son páginas aparte**: todos los módulos crean y editan
>   en **modales** dentro del listado (componente `<x-crud-modal>` + JS
>   `resources/js/crud-modal.js`, campos en partials `_campos.blade.php`).
>   Si tu tarjeta agrega un formulario nuevo, seguí ese patrón.
> - **Ya existe el módulo de Sprints** (`/sprints`, tabla `sprints` con columnas
>   `descripcion`, `estado`, `resumen_ia`) y el **resumen IA** de sprint
>   (`POST /sprints/{id}/resumen-ia`, botón de chispas en el listado).
>   El sistema tiene **8 módulos**, no 7.
> - **El dashboard se rediseñó**: los reportes por estado están en un `@foreach`
>   y los accesos rápidos en un loop `$accesos`. Anclá tus tarjetas nuevas
>   después del grid de accesos rápidos.
> - **El layout se rediseñó** (nav con logo, componente `mobile-nav` para móvil).
>   La campanita de notificaciones va en `layouts/navigation.blade.php`, en el
>   `<x-dropdown>` del usuario.
> - **Facturas**: crear/editar/eliminar es para **Jefe y PM** (cambió; antes decía
>   Jefe y PO).
> - **Ya hay 48 tests** (`php artisan test`): TableroTareasTest,
>   CorreccionesSeguridadTest, SprintSummaryEndpointTest, ProjectAIReportTest
>   + auth de Breeze. La sección 7 es para **ampliar** esa cobertura, no empezar
>   de cero.
> - La paleta es **indigo** para acciones primarias (no azules sueltos).

## Índice — quién hace qué

| Tarjeta en Trello | Responsable | Backend | Frontend |
| ----------------- | ----------- | ------- | -------- |
| Visor de auditoría | **Marcos** | Controller + ruta (1.1) | Vista + menú (1.2) |
| Dashboard de Cliente con métricas | **Marcos** | Datos en dashboard (4.1) | Barras de avance (4.2) |
| Notificaciones de solicitudes de cambio | **Jesús** | Notification + controller (2.1) | Campanita (2.2) |
| Exportar facturas a PDF | **Jesús** | dompdf + plantilla (5.1) | Botón (5.2) |
| Recordatorios de hitos por vencer | **Dante** | Query en dashboard (3.1) | Tarjeta de hitos (3.2) |
| Informe IA semanal automático | **Lucas** | Todo (sección 6) | — |
| Tests Feature de los módulos | **Lucas** | Todo (sección 7) | — |

---

## 1) VISOR DE AUDITORÍA

El sistema ya registra todos los cambios con `laravel-auditing` (cada modelo usa el
trait `Auditable`). Solo falta una vista para verlos.

### 1.1 BACKEND — Marcos

**Archivo: `app/Http/Controllers/AuditoriaController.php`** (crear)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $auditoria = Audit::with('user')
            ->latest()
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->lower();
                $query->where(function ($sub) use ($q) {
                    $sub->where('event', 'like', "%{$q}%")
                        ->orWhere('auditable_type', 'like', "%{$q}%");
                });
            })
            ->paginate(20)
            ->withQueryString();

        return view('auditoria.index', compact('auditoria'));
    }
}
```

**Archivo: `routes/web.php`** — agregar **dentro** del grupo `role:Jefe` que ya existe
(el mismo que tiene `users.create`):

```php
    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
```

Y arriba del archivo, junto a los demás `use`:

```php
use App\Http\Controllers\AuditoriaController;
```

**Comprobar:** entrar como Jefe a `/auditoria` y ver la tabla de cambios.

### 1.2 FRONTEND — Marcos

**Archivo: `resources/views/auditoria/index.blade.php`** (crear)

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Auditoría del sistema</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="GET" class="mb-4 flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por evento o modelo..."
                   class="border-gray-300 rounded-md w-64">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">Buscar</button>
        </form>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Evento</th>
                        <th class="px-4 py-3">Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditoria as $registro)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $registro->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $registro->user->name ?? 'Sistema' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs
                                    {{ $registro->event === 'created' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $registro->event === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $registro->event === 'deleted' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $registro->event }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ class_basename($registro->auditable_type) }} #{{ $registro->auditable_id }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Sin registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $auditoria->links() }}
    </div>
</x-app-layout>
```

**Y el botón de entrada:** en `resources/views/layouts/navigation.blade.php`, agregar
este ítem **solo para el Jefe** (al lado de Usuarios, dentro de su `@role`):

```blade
@role('Jefe')
    <x-nav-link :href="route('auditoria.index')" :active="request()->routeIs('auditoria.*')">
        Auditoría
    </x-nav-link>
@endrole
```

**Comprobar:** como Jefe se ve "Auditoría" en el menú; como otro rol, no.

---

## 2) NOTIFICACIONES DE SOLICITUDES DE CAMBIO

Usamos el canal `database` de Laravel: las notificaciones se guardan en la tabla
`notifications` (ya existe en la migración base) y se leen desde la sesión.

### 2.1 BACKEND — Jesús

**Archivo: `app/Notifications/SolicitudCambioCreada.php`** (crear carpeta y archivo)

```php
<?php

namespace App\Notifications;

use App\Models\SolicitudCambio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SolicitudCambioCreada extends Notification
{
    use Queueable;

    public function __construct(public SolicitudCambio $solicitud) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titulo' => $this->solicitud->titulo,
            'proyecto' => $this->solicitud->proyecto->nombre,
            'url' => route('solicitudes-cambio.show', $this->solicitud),
        ];
    }
}
```

**Archivo: `app/Http/Controllers/SolicitudCambioController.php`** — en `store()`,
justo **después** de la línea `SolicitudCambio::create($data);`, agregar:

```php
        // Avisar al Jefe y a los PM de la nueva solicitud.
        $solicitud = SolicitudCambio::latest('id')->first();
        \App\Models\User::role(['Jefe', 'PM'])->get()->each(
            fn (User $usuario) => $usuario->notify(new \App\Notifications\SolicitudCambioCreada($solicitud))
        );
```

*(Mejor aún: cambiar `SolicitudCambio::create($data)` por `$solicitud = SolicitudCambio::create($data);` y usar `$solicitud` en el aviso.)*

**Ruta para marcar como leída** — en `routes/web.php`, dentro del grupo `auth` general:

```php
Route::post('/notificaciones/leer/{id}', function ($id) {
    $notificacion = auth()->user()->notifications()->findOrFail($id);
    $notificacion->markAsRead();
    return back();
})->middleware('auth')->name('notificaciones.leer');
```

**Comprobar:** crear una solicitud y verificar en la tabla `notifications` de la DB
que el Jefe tiene un registro.

### 2.2 FRONTEND — Jesús

**Archivo: `resources/views/layouts/navigation.blade.php`** — buscar el bloque del
usuario (donde está el menú desplegable con el nombre) y agregar **antes** del
nombre la campanita:

```blade
@php
    $sinLeer = auth()->user()->unreadNotifications()->take(5)->get();
@endphp
<div class="relative" x-data="{ abierto: false }">
    <button @click="abierto = !abierto" class="text-gray-500 hover:text-gray-700 relative">
        🔔
        @if ($sinLeer->isNotEmpty())
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full px-1">
                {{ $sinLeer->count() }}
            </span>
        @endif
    </button>

    <div x-show="abierto" @click.away="abierto = false" x-cloak
         class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-lg border z-50">
        <div class="p-2">
            @forelse ($sinLeer as $notificacion)
                @php($datos = $notificacion->data)
                <div class="p-2 border-b last:border-0">
                    <p class="text-sm font-medium">Nueva solicitud: {{ $datos['titulo'] }}</p>
                    <p class="text-xs text-gray-500">Proyecto: {{ $datos['proyecto'] }}</p>
                    <div class="flex gap-2 mt-1">
                        <a href="{{ $datos['url'] }}" class="text-xs text-indigo-600 hover:underline">Ver</a>
                        <form method="POST" action="{{ route('notificaciones.leer', $notificacion->id) }}">
                            @csrf
                            <button class="text-xs text-gray-400 hover:underline">Marcar leída</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="p-3 text-sm text-gray-400">Sin notificaciones nuevas.</p>
            @endforelse
        </div>
    </div>
</div>
```

**Comprobar:** crear una solicitud con un usuario, loguearse como Jefe y ver la
campanita con el número rojo.

---

## 3) RECORDATORIOS DE HITOS POR VENCER

### 3.1 BACKEND — Dante

**Archivo: `routes/web.php`** — dentro del closure de `/dashboard`, agregar al final
del array `$datos` (después de `'totalEntregables' => ...`):

```php
        // Hitos que vencen pronto o ya vencieron (no completados).
        $datos['hitosProximos'] = \App\Models\Hito::visiblePara($usuario)
            ->where('completado', false)
            ->whereDate('fecha_objetivo', '<=', today()->addDays(7))
            ->with('proyecto')
            ->orderBy('fecha_objetivo')
            ->take(6)
            ->get();
```

**Comprobar:** `php artisan tinker` → `Hito::whereDate('fecha_objetivo','<=', now()->addDays(7))->count()`.

### 3.2 FRONTEND — Dante

**Archivo: `resources/views/dashboard.blade.php`** — agregar esta tarjeta **después
del grid de accesos rápidos** (el `@foreach ($accesos as ...)`), antes del cierre
del contenedor `max-w-7xl`.

```blade
@if (isset($hitosProximos) && $hitosProximos->isNotEmpty())
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-3">Hitos por vencer</h3>
        <ul class="divide-y">
            @foreach ($hitosProximos as $hito)
                <li class="py-2 flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ $hito->nombre }}</p>
                        <p class="text-xs text-gray-500">{{ $hito->proyecto->nombre }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full
                        {{ $hito->fecha_objetivo->isPast() ? 'bg-red-100 text-red-700 font-bold' : 'bg-yellow-100 text-yellow-700' }}">
                        @if ($hito->fecha_objetivo->isPast())
                            VENCIDO
                        @else
                            {{ $hito->fecha_objetivo->diffForHumans() }}
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif
```

**Comprobar:** crear un hito con fecha de mañana; en el dashboard aparece en
amarillo. Uno con fecha de ayer aparece en rojo con "VENCIDO".

---

## 4) DASHBOARD DE CLIENTE CON MÉTRICAS

### 4.1 BACKEND — Marcos

**Archivo: `routes/web.php`** — dentro del closure de `/dashboard`, agregar al array
`$datos` (siempre; para roles internos queda en null y la vista no lo muestra):

```php
        // Detalle para el dashboard del Cliente: avance de sus proyectos.
        $datos['misProyectos'] = $esCliente
            ? \App\Models\Proyecto::visiblePara($usuario)
                ->with('cliente')
                ->withCount([
                    'tareas',
                    'tareas as tareas_completadas' => fn ($q) => $q->where('estado', 'completada'),
                ])
                ->take(6)
                ->get()
            : null;
```

### 4.2 FRONTEND — Marcos

**Archivo: `resources/views/dashboard.blade.php`** — sección solo para Cliente
(mismo lugar: después del grid de accesos rápidos):

```blade
@if ($misProyectos ?? null)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-3">Avance de mis proyectos</h3>
        <ul class="divide-y">
            @foreach ($misProyectos as $proyecto)
                @php
                    $total = $proyecto->tareas_count;
                    $hechas = $proyecto->tareas_completadas;
                    $pct = $total > 0 ? round($hechas * 100 / $total) : 0;
                @endphp
                <li class="py-3">
                    <div class="flex justify-between mb-1">
                        <span class="font-medium">{{ $proyecto->nombre }}</span>
                        <span class="text-sm text-gray-500">{{ $hechas }}/{{ $total }} tareas ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif
```

**Comprobar:** entrar con el usuario Cliente del seeder; solo ve sus proyectos con
la barra de avance.

---

## 5) EXPORTAR FACTURAS A PDF

### 5.1 BACKEND — Jesús

Instalar la librería (una sola vez, en la raíz del proyecto):

```bash
composer require barryvdh/laravel-dompdf
```

**Archivo: `app/Http/Controllers/FacturaController.php`** — agregar este método
adentro de la clase:

```php
    public function descargarPdf(Request $request, Factura $factura)
    {
        $factura->load(['proyecto.cliente', 'emisor']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('facturas.pdf', compact('factura'));

        return $pdf->download("factura-{$factura->numero}.pdf");
    }
```

**Archivo: `routes/web.php`** — dentro del grupo que ya tiene el resource de
facturas, agregar:

```php
    Route::get('/facturas/{factura}/pdf', [FacturaController::class, 'descargarPdf'])
        ->name('facturas.pdf');
```

**Archivo: `resources/views/facturas/pdf.blade.php`** (crear — es la plantilla del PDF)

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { color: #4338ca; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>CRUZNEGRA</h1>
    <p>Factura {{ $factura->numero }} — emitida el {{ $factura->fecha_emision->format('d/m/Y') }}</p>

    <table>
        <tr><th>Cliente</th><td>{{ $factura->proyecto->cliente->nombre ?? '—' }}</td></tr>
        <tr><th>Proyecto</th><td>{{ $factura->proyecto->nombre }}</td></tr>
        <tr><th>Detalle</th><td>{{ $factura->detalle }}</td></tr>
        <tr><th>Estado</th><td>{{ ucfirst($factura->estado) }}</td></tr>
        <tr><th>Vencimiento</th><td>{{ $factura->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td></tr>
        <tr><th><strong>Total</strong></th><td><strong>${{ number_format($factura->monto, 2) }}</strong></td></tr>
    </table>
</body>
</html>
```

### 5.2 FRONTEND — Jesús

En `resources/views/facturas/show.blade.php` (y/o en cada fila del index), agregar
el botón:

```blade
<a href="{{ route('facturas.pdf', $factura) }}"
   class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md text-sm hover:bg-red-700"
   target="_blank">
    Descargar PDF
</a>
```

> En el index la variable se llama distinto según el bucle (mirá cómo se usa el
> `@foreach` de otras columnas y usá la misma variable).

**Comprobar:** abrir una factura y descargar el PDF.

---

## 6) INFORME IA SEMANAL AUTOMÁTICO — Solo Lucas

El endpoint `POST /sprints/{sprint}/resumen-ia` y el `SprintSummaryService` ya
existen. Solo falta el comando programado y el email.

**Archivo: `app/Console/Commands/EnviarResumenSprintSemanal.php`** (crear carpetas)

```php
<?php

namespace App\Console\Commands;

use App\Models\Sprint;
use App\Models\User;
use App\Services\AI\SprintSummaryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarResumenSprintSemanal extends Command
{
    protected $signature = 'informes:resumen-sprint-semanal';

    protected $description = 'Envía al Jefe el resumen IA del sprint activo de cada proyecto';

    public function handle(SprintSummaryService $servicio): int
    {
        $jefes = User::role('Jefe')->get();

        if ($jefes->isEmpty()) {
            $this->warn('No hay usuarios con rol Jefe.');
            return self::SUCCESS;
        }

        $sprints = Sprint::where('estado', 'activo')->with('proyecto')->get();

        foreach ($sprints as $sprint) {
            try {
                $resultado = $servicio->generate($sprint, false);
                $cuerpo = "Resumen de {$sprint->nombre} ({$sprint->proyecto->nombre}):\n\n"
                    . $resultado['resumen'];
            } catch (\Throwable $e) {
                report($e);
                $cuerpo = "No fue posible generar el resumen IA de {$sprint->nombre}.";
            }

            foreach ($jefes as $jefe) {
                Mail::raw($cuerpo, function ($mail) use ($jefe, $sprint) {
                    $mail->to($jefe->email)->subject("Resumen semanal: {$sprint->nombre}");
                });
            }
        }

        $this->info("Enviados resúmenes de {$sprints->count()} sprint(s).");
        return self::SUCCESS;
    }
}
```

**Archivo: `routes/console.php`** — agregar:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('informes:resumen-sprint-semanal')->mondays()->at('08:00');
```

> Con `MAIL_MAILER=log` (como está el `.env`), el email se escribe en
> `storage/logs/laravel.log` — suficiente para probar.
>
> **Ojo:** el comando busca sprints con `estado = 'activo'`, pero el seeder los
> crea con el default `'planificado'`. Para probar, marcá uno así:
> `php artisan tinker` → `\App\Models\Sprint::where('id', 1)->update(['estado' => 'activo']);`

**Comprobar manualmente:** `php artisan informes:resumen-sprint-semanal` y revisar
el log.

---

## 7) TESTS FEATURE DE LOS MÓDULOS — Solo Lucas

> **Actualizado:** ya existen 48 tests (ver banner de arriba). Esta tarjeta ahora
> es para **completar la cobertura que falta**: no hay tests de los CRUD de
> Clientes, Proyectos, Hitos, Solicitudes, Entregables, Facturas (listado/creación
> por modal) ni de Sprints. Creá `tests/Feature/ModulosTest.php` y sumá casos con
> el patrón de abajo; corré `php artisan test` para ver que no pises los existentes.

**Archivo: `tests/Feature/ModulosTest.php`** (crear — ejemplo base para extender)

> Ojo: solo existe `UserFactory`. Para Cliente y Proyecto creamos los registros
> a mano con sus campos reales.

```php
<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModulosTest extends TestCase
{
    use RefreshDatabase;

    private function cliente(array $extra = []): Cliente
    {
        return Cliente::create($extra + [
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'email' => uniqid() . '@test.com',
            'telefono' => '1234',
            'empresa' => 'Empresa ' . uniqid(),
            'estado' => 'activo',
        ]);
    }

    private function proyecto(Cliente $cliente): Proyecto
    {
        return Proyecto::create([
            'nombre' => 'Proyecto ' . uniqid(),
            'descripcion' => 'prueba',
            'fecha_inicio' => today(),
            'estado' => 'en_progreso',
            'cliente_id' => $cliente->id,
        ]);
    }

    public function test_el_jefe_ve_el_listado_de_clientes(): void
    {
        $jefe = User::factory()->create()->assignRole('Jefe');

        $this->actingAs($jefe)->get('/clientes')->assertOk();
    }

    public function test_un_cliente_solo_ve_proyectos_de_su_empresa(): void
    {
        $miEmpresa = $this->cliente();
        $otra = $this->cliente();

        $mio = $this->proyecto($miEmpresa);
        $this->proyecto($otra);

        $usuarioCliente = User::factory()->create(['cliente_id' => $miEmpresa->id])
            ->assignRole('Cliente');

        $this->actingAs($usuarioCliente)
            ->get('/proyectos')
            ->assertOk()
            ->assertSee($mio->nombre);
    }
}
```

**Comprobar:** `php artisan test`. Extender el mismo patrón para Tareas, Hitos,
Solicitudes, Entregables y Facturas.

---

## Si algo sale mal

| Problema                                                       | Solución                                                                                                         |
| -------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------- |
| `Class "App\Http\Controllers\AuditoriaController" not found` | Falta el`use` arriba de `routes/web.php` o corre `php artisan optimize:clear`                               |
| La campanita no aparece                                        | Revisá que el bloque esté dentro del`<nav>` de `navigation.blade.php`, donde el usuario ya está logueado   |
| El PDF sale en blanco                                          | Corre`php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"` y `php artisan optimize:clear` |
| Las notificaciones no se guardan                               | Corre`php artisan migrate` (la tabla `notifications` tiene que existir)                                       |
| El comando del informe no envía nada                          | Tiene que existir al menos un`Sprint` con `estado = activo` y un usuario con rol `Jefe`                     |

---

## Orden sugerido de trabajo

1. Cada uno hace su tarjeta completa: primero el backend (PHP), después el frontend (Blade).
2. Cuando una tarjeta termina, se marca en Trello.
3. Lucas trabaja en paralelo con el informe IA y los tests, sin depender de nadie.

Prioridad sugerida: **Notificaciones → Recordatorios de hitos → Auditoría →
PDF facturas → Dashboard Cliente → Informe IA → Tests**.
