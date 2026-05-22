# CRUZNEGRA — Plan de Implementación

## FASE 1 — Ajuste de la tabla `users` existente

```bash
php artisan make:migration add_fields_to_users_table
```

Campos a agregar: `apellido`, `estado`

---

## FASE 2 — Crear migraciones de las nuevas tablas

```bash
php artisan make:migration create_clientes_table
php artisan make:migration create_proyectos_table
php artisan make:migration create_solicitudes_cambio_table
php artisan make:migration create_tareas_table
php artisan make:migration create_hitos_table
php artisan make:migration create_entregables_ia_table
php artisan make:migration create_facturas_table
```

> El orden importa: `proyectos` depende de `clientes` y `users`. `tareas` depende de `proyectos` y `solicitudes_cambio`. `facturas` y `entregables_ia` dependen de `proyectos`.

---

## FASE 3 — Crear los Modelos

```bash
php artisan make:model Cliente
php artisan make:model Proyecto
php artisan make:model SolicitudCambio
php artisan make:model Tarea
php artisan make:model Hito
php artisan make:model EntregableIA
php artisan make:model Factura
```

---

## FASE 4 — Crear los Controladores

```bash
php artisan make:controller ClienteController --resource
php artisan make:controller ProyectoController --resource
php artisan make:controller TareaController --resource
php artisan make:controller HitoController --resource
php artisan make:controller SolicitudCambioController --resource
php artisan make:controller EntregableIAController --resource
php artisan make:controller FacturaController --resource
```

> `--resource` genera automáticamente los métodos: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.

---

## FASE 5 — Actualizar Seeders

```bash
php artisan make:seeder ClienteSeeder
php artisan make:seeder ProyectoSeeder
php artisan make:seeder TareaSeeder
```

> `RoleSeeder` ya existe — editar para reemplazar roles actuales por: `PM`, `PO`, `Programador`, `Jefe`, `Cliente`.
> `UserSeeder` ya existe — editar para asignar los nuevos roles.

---

## FASE 6 — Correr migraciones y seeders

```bash
# Primera vez o reseteo completo
php artisan migrate:fresh --seed

# Solo correr las migraciones nuevas (sin borrar datos)
php artisan migrate

# Solo correr los seeders
php artisan db:seed

# Correr un seeder específico
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=ClienteSeeder
```

---

## FASE 7 — Crear las Vistas (por módulo)

```bash
# Las vistas se crean manualmente en resources/views/
# Estructura sugerida:

resources/views/
├── clientes/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── proyectos/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── tareas/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── hitos/
│   ├── index.blade.php
│   └── create.blade.php
├── solicitudes_cambio/
│   ├── index.blade.php
│   └── create.blade.php
├── entregables/
│   ├── index.blade.php
│   └── show.blade.php
└── facturas/
    ├── index.blade.php
    └── show.blade.php
```

---

## FASE 8 — Registrar rutas en `routes/web.php`

```php
Route::resource('clientes', ClienteController::class);
Route::resource('proyectos', ProyectoController::class);
Route::resource('tareas', TareaController::class);
Route::resource('hitos', HitoController::class);
Route::resource('solicitudes-cambio', SolicitudCambioController::class);
Route::resource('entregables', EntregableIAController::class);
Route::resource('facturas', FacturaController::class);
```

---

## FASE 9 — Comandos de verificación y utilidad

```bash
# Ver todas las rutas registradas
php artisan route:list

# Ver estado de las migraciones
php artisan migrate:status

# Limpiar caché de configuración (después de editar .env)
php artisan config:clear

# Limpiar caché de vistas
php artisan view:clear

# Limpiar toda la caché
php artisan optimize:clear

# Abrir tinker (consola interactiva de Laravel)
php artisan tinker
```

---

## RESUMEN DE TABLAS A CREAR

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

## ROLES A DEFINIR EN `RoleSeeder`

| Rol | Descripción |
|-----|-------------|
| `PM` | Project Manager — gestiona proyectos y asigna tareas |
| `PO` | Product Owner — valida tareas y entregables IA |
| `Programador` | Ejecuta tareas y marca finalización |
| `Jefe` | Aprueba cambios, emite facturas, consulta reportes |
| `Cliente` | Solo lectura — consulta avance y solicita cambios |
