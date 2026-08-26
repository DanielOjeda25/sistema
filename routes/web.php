<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EntregableIAController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\HitoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\SolicitudCambioController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\UserController;
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
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
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
Route::middleware(['auth', 'role:Jefe|PM'])->group(function () {
    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
});

/* * NIVEL 2: Escritura/Edición.
 * Solo el Jefe edita roles de usuarios. Esa decisión queda concentrada en una
 * sola persona para evitar escaladas de permisos.
 */
Route::middleware(['auth', 'role:Jefe'])->group(function () {
    Route::get('/usuarios/{user}/roles', [UserController::class, 'editRoles'])->name('users.roles.edit');
    Route::put('/usuarios/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');
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
// El orden importa: los grupos de escritura van PRIMERO porque definen rutas
// literales como /clientes/create. Si fueran después, la ruta de lectura
// /clientes/{cliente} tomaría "create" como si fuera un id y daría 404.

/*
 * ESCRITURA — parte comercial y facturación.
 * Clientes, proyectos y facturas los maneja quien responde por la relación con
 * el cliente y por la plata: el Jefe y el PM.
 */
Route::middleware(['auth', 'role:Jefe|PM'])->group(function () {
    Route::resource('clientes', ClienteController::class)->except(['index', 'show']);
    Route::resource('proyectos', ProyectoController::class)->except(['index', 'show']);
    Route::resource('facturas', FacturaController::class)->except(['index', 'show']);
});

/*
 * ESCRITURA — planificación del trabajo.
 * El PO entra acá porque define el alcance: qué se hace, en qué orden y qué
 * cambios se aceptan. Tareas e hitos salen de esa definición.
 */
Route::middleware(['auth', 'role:Jefe|PM|PO'])->group(function () {
    Route::resource('tareas', TareaController::class)->except(['index', 'show']);
    Route::resource('hitos', HitoController::class)->except(['index', 'show']);
    Route::resource('solicitudes-cambio', SolicitudCambioController::class)->except(['index', 'show']);
});

/*
 * ESCRITURA — entregables.
 * Suma al Programador, que es quien produce el material que se entrega.
 */
Route::middleware(['auth', 'role:Jefe|PM|PO|Programador'])->group(function () {
    Route::resource('entregables', EntregableIAController::class)->except(['index', 'show']);
});

/*
 * LECTURA — cualquiera con sesión iniciada.
 * El Cliente entra a seguir el avance de sus proyectos; el Programador, a ver
 * sus tareas. Ninguno de los dos puede modificar nada.
 */
Route::middleware('auth')->group(function () {
    Route::resource('clientes', ClienteController::class)->only(['index', 'show']);
    Route::resource('proyectos', ProyectoController::class)->only(['index', 'show']);
    Route::resource('tareas', TareaController::class)->only(['index', 'show']);
    Route::resource('hitos', HitoController::class)->only(['index', 'show']);
    Route::resource('solicitudes-cambio', SolicitudCambioController::class)->only(['index', 'show']);
    Route::resource('entregables', EntregableIAController::class)->only(['index', 'show']);
    Route::resource('facturas', FacturaController::class)->only(['index', 'show']);
});

// -----------------------------------------------------------------------------

require __DIR__.'/auth.php';