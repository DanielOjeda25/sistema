<?php

use App\Http\Controllers\ActualizacionProyectoController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EntregableIAController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\HitoController;
use App\Http\Controllers\InformeIAController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\SolicitudCambioController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\SprintSummaryController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\UserController;
use App\Models\Cliente;
use App\Models\EntregableIA;
use App\Models\Factura;
use App\Models\Hito;
use App\Models\Proyecto;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// -----------------------------------------------------------------------------
// RUTAS PÚBLICAS
// -----------------------------------------------------------------------------
Route::get('/', function () {
    return view('welcome');
});

// -----------------------------------------------------------------------------
// RUTAS BÁSICAS DE AUTENTICACIÓN (Cualquiera que inicie sesión)
// -----------------------------------------------------------------------------
Route::get('/dashboard', function () {
    $usuario = auth()->user();
    $esCliente = $usuario->esCliente();

    // Números del dashboard actual. Para Cliente se limitan a su empresa.
    $datos = [
        'esCliente' => $esCliente,
        'totalClientes' => $esCliente ? null : Cliente::count(),
        'totalProyectos' => Proyecto::visiblePara($usuario)->count(),
        'tareasPendientes' => Tarea::visiblePara($usuario)
            ->where('estado', 'pendiente')->count(),
        'facturasPendientes' => Factura::visiblePara($usuario)
            ->where('estado', 'pendiente')->count(),
        'totalHitos' => Hito::visiblePara($usuario)->count(),
        // El Cliente solo recibe entregables aprobados; el equipo cuenta todos.
        'totalEntregables' => EntregableIA::visiblePara($usuario)
            ->when($esCliente, fn ($q) => $q->where('estado', 'aprobado'))
            ->count(),
    ];

    // Hitos que vencen pronto o ya vencieron (no completados).
    $datos['hitosProximos'] = Hito::visiblePara($usuario)
        ->where('completado', false)
        ->whereDate('fecha_objetivo', '<=', today()->addDays(7))
        ->with('proyecto')
        ->orderBy('fecha_objetivo')
        ->take(6)
        ->get();

    // Los reportes son globales y nunca se calculan para el rol Cliente.
    if (! $esCliente) {
        $datos['proyectosPorEstado'] = [
            'pendiente' => Proyecto::where('estado', 'pendiente')->count(),
            'en_progreso' => Proyecto::where('estado', 'en_progreso')->count(),
            'completado' => Proyecto::where('estado', 'completado')->count(),
            'cancelado' => Proyecto::where('estado', 'cancelado')->count(),
        ];

        $datos['tareasPorEstado'] = [
            'pendiente' => Tarea::where('estado', 'pendiente')->count(),
            'en_progreso' => Tarea::where('estado', 'en_progreso')->count(),
            'completada' => Tarea::where('estado', 'completada')->count(),
            'cancelada' => Tarea::where('estado', 'cancelada')->count(),
        ];

        $datos['totalFacturado'] = Factura::sum('monto');

        // Pendiente de cobro incluye facturas pendientes y vencidas: ninguna
        // de las dos fue pagada todavía.
        $datos['totalPendienteCobro'] = Factura::whereIn(
            'estado',
            ['pendiente', 'vencida']
        )->sum('monto');

        $datos['tareasVencidas'] = Tarea::whereDate('fecha_limite', '<', today())
            ->whereNotIn('estado', ['completada', 'cancelada'])
            ->count();

        // Facturacion por mes (ultimos 6 meses con movimiento) para el
        // grafico del dashboard interno.
        $formatoMes = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', fecha_emision)"
            : "DATE_FORMAT(fecha_emision, '%Y-%m')";
        $porMes = Factura::selectRaw("{$formatoMes} as mes, SUM(monto) as total")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->pluck('total', 'mes');
        $meses = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->format('Y-m'));
        $datos['facturacionPorMes'] = $meses->map(fn ($mes) => [
            'mes' => now()->createFromFormat('Y-m', $mes)->translatedFormat('M'),
            'total' => (float) ($porMes[$mes] ?? 0),
        ]);
        // Detalle para el dashboard del Cliente: avance de sus proyectos.
        $datos['misProyectos'] = $esCliente
            ? Proyecto::visiblePara($usuario)
                ->with('cliente')
                ->withCount([
                    'tareas',
                    'tareas as tareas_completadas' => fn ($q) => $q->where('estado', 'completada'),
                ])
                ->take(6)
                ->get()
            : null;
    }

    return view('dashboard', $datos);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Notificaciones in-app: marcar una como leida o todas.
    Route::post('/notificaciones/{id}/leer', function (Request $request, $id) {
        $notificacion = $request->user()->notifications()->findOrFail($id);
        $notificacion->markAsRead();

        return back();
    })->name('notificaciones.leer');

    Route::post('/notificaciones/leer-todas', function (Request $request) {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    })->name('notificaciones.leer-todas');

    // Rutas del perfil nativas de Laravel Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// -----------------------------------------------------------------------------
// MÓDULO DE USUARIOS Y ROLES (Protegido por Spatie)
// -----------------------------------------------------------------------------

/* * NIVEL 1: Lectura.
 * El PM necesita ver la lista para saber a quién asignar tareas. El Jefe la ve
 * porque administra. PO/Programador/Cliente no entran.
 */
Route::middleware(['auth', 'role:Jefe'])->group(function () {
    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
});

/* * NIVEL 2: Escritura/Edición.
 * Solo el Jefe edita roles de usuarios. Esa decisión queda concentrada en una
 * sola persona para evitar escaladas de permisos.
 */
Route::middleware(['auth', 'role:Jefe'])->group(function () {
    // Bitácora de cambios del sistema (laravel-auditing): quién hizo qué y
    // cuándo. Queda en Jefe porque es información sensible de administración.
    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');

    // Alta de usuarios desde el modal del listado. No hay registro público: las
    // cuentas se crean acá y se les asigna un rol. La contraseña que se pone es
    // provisional; la persona la cambia desde su perfil cuando entra.
    Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');

    // Cambio de rol (un solo rol por usuario) desde el modal de la lista.
    Route::put('/usuarios/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');

    // Edición y baja de usuarios (modales en la lista).
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::get('/tutorial', function () {
    return view('tutorial.index');
})->middleware(['auth'])->name('tutorial');

// -----------------------------------------------------------------------------
// MÓDULOS DEL SISTEMA (CRUD recursos del proyecto)
// -----------------------------------------------------------------------------
// El acceso se corta acá, en la ruta, y no solamente escondiendo botones en la
// vista: esconder un botón no impide que alguien escriba la URL a mano.
//
// La regla es la misma en todos los módulos: cualquiera con sesión iniciada
// puede LEER (index y show), pero ESCRIBIR (create, store, edit, update y
// destroy) queda limitado a los roles que correspondan.
//
// El orden importa: los grupos de escritura van PRIMERO; si fueran después,
// la ruta de lectura /clientes/{cliente} tomaría verbos de escritura como
// si fueran un id y daría 404.

/*
 * ESCRITURA — parte comercial y facturación.
 * Clientes, proyectos y facturas los maneja quien responde por la relación con
 * el cliente y por la plata: el Jefe y el PM.
 */
Route::middleware(['auth', 'role:Jefe|PM'])->group(function () {
    Route::resource('clientes', ClienteController::class)->only(['store', 'update', 'destroy']);
    Route::resource('proyectos', ProyectoController::class)->only(['store', 'update', 'destroy']);
    Route::resource('facturas', FacturaController::class)->only(['store', 'update']);
});

// Eliminar facturas sigue la regla del resto del módulo comercial: Jefe y PM
// (los mismos roles que pueden crearlas y editarlas).
Route::middleware(['auth', 'role:Jefe|PM'])->group(function () {
    Route::delete('facturas/{factura}', [FacturaController::class, 'destroy'])
        ->name('facturas.destroy');
});

/*
 * ESCRITURA — planificación del trabajo.
 * El PO entra acá porque define el alcance: qué se hace, en qué orden y qué
 * cambios se aceptan. Tareas e hitos salen de esa definición.
 */
Route::middleware(['auth', 'role:Jefe|PM|PO'])->group(function () {
    // Movimiento de tarjetas del tablero (drag & drop): recibe el estado y la
    // posición final de cada tarea movida.
    Route::patch('tareas/mover', [TareaController::class, 'mover'])->name('tareas.mover');
    Route::resource('tareas', TareaController::class)->only(['store', 'update', 'destroy']);
    Route::resource('hitos', HitoController::class)->only(['store', 'update', 'destroy']);
    Route::resource('solicitudes-cambio', SolicitudCambioController::class)->only(['store', 'update', 'destroy']);
    Route::resource('sprints', SprintController::class)->only(['store', 'update', 'destroy']);
});

/*
 * ESCRITURA — entregables.
 * Suma al Programador, que es quien produce el material que se entrega.
 */
Route::middleware(['auth', 'role:Jefe|PM|PO|Programador'])->group(function () {
    Route::resource('entregables', EntregableIAController::class)->only(['store', 'update', 'destroy']);
    Route::post('proyectos/{proyecto}/actualizaciones', [ActualizacionProyectoController::class, 'store'])
        ->name('proyectos.actualizaciones.store');
    Route::post('proyectos/{proyecto}/informes-ia', [InformeIAController::class, 'store'])
        ->name('proyectos.informes-ia.store');
    Route::post('sprints/{sprint}/resumen-ia', [SprintSummaryController::class, 'store'])
        ->name('sprints.resumen-ia.store');
});

Route::middleware(['auth', 'role:Jefe|PM|PO'])->group(function () {
    Route::patch('informes-ia/{entregable}/publicar', [InformeIAController::class, 'publish'])
        ->name('informes-ia.publish');
    Route::patch('informes-ia/{entregable}/retirar', [InformeIAController::class, 'unpublish'])
        ->name('informes-ia.unpublish');
});

/*
 * LECTURA — cualquiera con sesión iniciada.
 * El Cliente entra a seguir el avance de sus proyectos; el Programador, a ver
 * sus tareas. Ninguno de los dos puede modificar nada.
 */
/*
 * LECTURA — el módulo de Clientes es la cartera de la empresa: no tiene
 * sentido que un Cliente vea la lista de otros clientes, así que queda
 * limitado a los roles internos.
 */
Route::middleware(['auth', 'role:Jefe|PM|PO|Programador'])->group(function () {
    Route::resource('clientes', ClienteController::class)->only(['index', 'show']);
});

/*
 * LECTURA — cualquiera con sesión iniciada.
 * El Cliente entra a seguir el avance de SUS proyectos: los listados y las
 * fichas se filtran por su empresa en cada controller (scope visiblePara);
 * si intentara abrir por URL algo de otro cliente, recibe 403 (puedeVer).
 */
Route::middleware('auth')->group(function () {
    Route::resource('proyectos', ProyectoController::class)->only(['index', 'show']);
    // Va antes del resource para que "tablero" no se tome como un id de tarea.
    Route::get('tareas/tablero', [TareaController::class, 'tablero'])->name('tareas.tablero');
    Route::resource('tareas', TareaController::class)->only(['index', 'show']);
    Route::resource('hitos', HitoController::class)->only(['index', 'show']);
    Route::resource('solicitudes-cambio', SolicitudCambioController::class)->only(['index', 'show']);
    Route::resource('sprints', SprintController::class)->only(['index']);
    Route::resource('entregables', EntregableIAController::class)->only(['index', 'show']);
    Route::resource('facturas', FacturaController::class)->only(['index', 'show']);
    // Descargar factura en PDF: es lectura, cualquier usuario autorizado
    // puede bajar las facturas de los proyectos que puede ver.
    Route::get('/facturas/{factura}/pdf', [FacturaController::class, 'descargarPdf'])
        ->name('facturas.pdf');
});

// -----------------------------------------------------------------------------

require __DIR__.'/auth.php';
