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
// Todos requieren sesión iniciada. La granularidad por rol se aplica adentro
// de cada controller o vía Gate/Policy cuando se definan.
Route::middleware('auth')->group(function () {
    Route::resource('clientes', ClienteController::class);
    Route::resource('proyectos', ProyectoController::class);
    Route::resource('tareas', TareaController::class);
    Route::resource('hitos', HitoController::class);
    Route::resource('solicitudes-cambio', SolicitudCambioController::class);
    Route::resource('entregables', EntregableIAController::class);
    Route::resource('facturas', FacturaController::class);
});

// -----------------------------------------------------------------------------

require __DIR__.'/auth.php';