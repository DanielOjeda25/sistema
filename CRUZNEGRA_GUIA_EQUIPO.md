# CRUZNEGRA — Guía para Marcos, Jesús y Dante

**Todo lo que tenés que hacer está en este archivo.** No hay otro que leer.

## Cómo se usa esta guía

Es larga porque trae el código completo, pero **vos solo leés tu parte**:

1. Hacé el **PASO 0** una vez (preparar la máquina)
2. Andá a la **fase** que están haciendo ahora
3. Buscá **tu nombre** dentro de esa fase
4. Hacé lo que dice, uno por uno

Cada tarea tuya te dice tres cosas:

- **Qué archivo crear** (o abrir)
- **Todo el código que va adentro** — copialo entero y pegalo
- **Cómo comprobar que funciona**

**El código está completo y probado. No hay que inventar ni completar nada.**
Copiás, pegás, guardás y funciona.

> **Importante:** cuando te damos un bloque de código, copialo **entero**, desde la
> primera línea hasta la última. Si copiás la mitad, la página se rompe.

## Índice

| Parte                                                                         | Qué es                                                         |
| ----------------------------------------------------------------------------- | --------------------------------------------------------------- |
| [PASO 0](#paso-0--preparar-la-máquina-todos-una-sola-vez)                     | Preparar la máquina —**hacelo una vez, antes que nada** |
| [Reglas](#reglas)                                                              | Lo que no hay que romper                                        |
| [Cómo crear un archivo](#cómo-crear-un-archivo-por-si-nunca-lo-hiciste)      | Por si nunca lo hiciste en VS Code                              |
| [El ejemplo de Clientes](#el-módulo-de-clientes-ya-está-hecho-es-tu-ejemplo) | El módulo ya hecho que podés mirar cuando dudes               |
| [FASE 1](#fase-1)                                                              | Marcos: Hitos · Jesús: Proyectos · Dante: Tareas             |
| [FASE 2](#fase-2)                                                              | Marcos: Facturas · Jesús: Solicitudes · Dante: Entregables   |
| [FASE 3](#fase-3)                                                              | Jesús: Menú · Dante: Dashboard · Marcos: probar             |
| [SPRINT 2 — Buscador](#sprint-2--jesús-buscador-y-filtros-en-los-listados)      | Jesús: búsqueda, filtro por estado y paginación                 |
| [SPRINT 2 — Reportes](#sprint-2--dante-panel-de-reportes-del-jefe)              | Dante: panel global de reportes por estado y facturación       |
| [SPRINT 2 — Testing](#sprint-2--testing-del-sistema)                            | Equipo: recorrido E2E, roles y pruebas automáticas              |
| [FASE 4](#fase-4--repaso-final-entre-los-tres)                                 | Repaso final entre los tres                                     |
| [Si algo sale mal](#si-algo-sale-mal--soluciones-rápidas)                     | Tabla de errores y cómo resolverlos                            |

> **Consejo:** en VS Code abrí este archivo y apretá **Ctrl+Shift+V** para verlo lindo,
> con el índice clickeable.

---

## Quién hace qué

| Fase        | Marcos                      | Jesús                | Dante          |
| ----------- | --------------------------- | --------------------- | -------------- |
| **1** | Hitos                       | Proyectos             | Tareas         |
| **2** | Facturas                    | Solicitudes de Cambio | Entregables IA |
| **3** | Ayudar en el repaso         | Menú de navegación  | Dashboard      |
| **4** | Repaso final entre los tres |                       |                |

Cada uno hace **4 archivos** por fase. Los 4 archivos son siempre lo mismo:

- `index.blade.php` → la lista
- `create.blade.php` → el formulario para crear
- `edit.blade.php` → el formulario para editar
- `show.blade.php` → la ficha con el detalle

## Estado de las fases (agosto 2026)

| Fase   | Estado                                                                                                    |
| ------ | --------------------------------------------------------------------------------------------------------- |
| FASE 1 | Casi completa — faltan los 4 archivos de**Hitos** (Marcos). Proyectos y Tareas ya están           |
| FASE 2 | Casi completa — faltan los 4 archivos de**Facturas** (Marcos). Solicitudes y Entregables ya están |
| FASE 3 | **Completa** — menú (Jesús) y dashboard (Dante) subidos a `main`                               |
| FASE 4 | Pendiente — repaso final, recién cuando Hitos y Facturas estén en`main`                              |

> ⚠️ El menú ya enlaza a `/hitos` y `/facturas`, pero esas vistas no existen:
> entrar a esas direcciones tira error hasta que se creen los archivos.
>
> **Sprint actual (31/08 – 11/09):** mientras Marcos termina Hitos y Facturas,
> Jesús y Dante trabajan en mejoras del sistema (buscador y filtros, panel de
> reportes, limpieza y tests). Las tarjetas están en `tareas_sprint_jesus_dante.txt`
> y en Trello.

---

## PASO 0 — Preparar la máquina (todos, una sola vez)

### 1. Prendé Laragon

Abrí **Laragon** y apretá el botón **Start All**. Esperá a que los dos indicadores
queden en verde.

### 2. Abrí una terminal en la carpeta del proyecto

Copiá y pegá esto, de a un comando por vez:

```bash
cd c:\laragon\www\sistema
git pull
composer install
npm install
php artisan migrate:fresh --seed
```

### 3. Dejá los estilos corriendo

En esa misma terminal:

```bash
npm run dev
```

**Esa terminal queda ocupada y no te devuelve el cursor. Es correcto, dejala así.**
Si la cerrás, la página se ve fea y sin colores.

Para los demás comandos, abrí **otra** terminal.

### 4. Entrá al sistema

Abrí el navegador en `http://sistema.test` (si no anda, probá
`http://localhost/sistema/public`).

Usuario y contraseña para entrar (las creadas por el seeder del proyecto):

| Email                   | Contraseña | Rol         |
| ----------------------- | ----------- | ----------- |
| `jefe@example.com`    | `1234`    | Jefe        |
| `pm@example.com`      | `1234`    | PM          |
| `po@example.com`      | `1234`    | PO          |
| `dev@example.com`     | `1234`    | Programador |
| `cliente@example.com` | `1234`    | Cliente     |

> Las credenciales de prueba son solo para desarrollo. Usá `1234` solo en este entorno local.

> ⚠️ Si el login falla, revisá que estés usando la misma versión del proyecto que está corriendo en Laravel y que la base haya sido sembrada con `php artisan migrate:fresh --seed`. El problema más común fue una copia desincronizada del proyecto.

### 5. Creá tu rama

Cada vez que empieces una fase, copiá y pegá esto **cambiando tu nombre y el número de fase**:

```bash
git checkout main
git pull
git checkout -b fase1-marcos
```

---

## Reglas

1. **Solo tocás los archivos que dice tu tarea.** Nada más.
2. **Nunca toques las carpetas `app`, `database` ni `routes`.** Ya están terminadas.
3. **No empieces la fase siguiente** hasta que los tres terminaron la actual.
4. **Si algo falla**, sacá captura del error y mandala al grupo. No borres código.

---

## Cómo crear un archivo (por si nunca lo hiciste)

1. En VS Code, buscá la carpeta `resources` → `views` en el panel izquierdo
2. Si la carpeta de tu módulo no existe, clic derecho sobre `views` → **New Folder** →
   escribí el nombre exacto que dice tu tarea
3. Clic derecho sobre esa carpeta → **New File**
4. Escribí el nombre del archivo **con todo y el `.blade.php`** al final
5. Pegá el código
6. Guardá con **Ctrl + S**

---

## El módulo de Clientes ya está hecho: es tu ejemplo

El módulo **Clientes** ya está terminado y funcionando. **No lo toques** — está ahí para
que lo mires cuando tengas dudas.

Sus 4 archivos están en `resources/views/clientes/`:

| Si tenés que hacer...  | Abrí este archivo y miralo   |
| ----------------------- | ----------------------------- |
| Una lista con tabla     | `clientes/index.blade.php`  |
| Un formulario de crear  | `clientes/create.blade.php` |
| Un formulario de editar | `clientes/edit.blade.php`   |
| Una ficha de detalle    | `clientes/show.blade.php`   |

**Cuando algo no te salga, comparalo con el archivo equivalente de Clientes.**
Entrá a `http://sistema.test/clientes` y probalo: creá un cliente, editalo, mirá el
detalle. Así ves cómo tiene que quedar el tuyo cuando termines.

> El código que te damos más abajo sigue exactamente el mismo formato que Clientes.
> Si algo se ve distinto, es porque ese módulo tiene campos distintos, no porque esté mal.

---

# FASE 1

---

## MARCOS — Fase 1: Hitos

**Tu carpeta:** `resources/views/hitos/` — **no existe, hay que crearla**

---

### Archivo 1 de 4 — `index.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Hitos
            </h2>
            <a href="{{ route('hitos.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Nuevo Hito
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha objetivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                            @forelse ($hitos as $hito)
                                <tr>
                                    <td class="px-6 py-4">{{ $hito->nombre }}</td>
                                    <td class="px-6 py-4">{{ $hito->proyecto?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $hito->fecha_objetivo?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $hito->completado ? 'Completado' : 'Pendiente' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('hitos.show', $hito) }}" class="text-blue-600 hover:underline">Ver</a>
                                        <a href="{{ route('hitos.edit', $hito) }}" class="text-yellow-600 hover:underline ml-3">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No hay hitos registrados aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $hitos->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 2 de 4 — `create.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nuevo Hito
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('hitos.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="nombre" value="Nombre del Hito" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción (opcional)" />
                        <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_objetivo" value="Fecha Objetivo" />
                        <x-text-input id="fecha_objetivo" name="fecha_objetivo" type="date" class="mt-1 block w-full" :value="old('fecha_objetivo')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_objetivo')" />
                    </div>

                    <div>
                        <x-input-label for="completado" value="Estado" />
                        <select id="completado" name="completado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="0" @selected(old('completado') == '0')>Pendiente</option>
                            <option value="1" @selected(old('completado') == '1')>Completado</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('completado')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id') == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('hitos.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar Hito</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 3 de 4 — `edit.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Hito
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('hitos.update', $hito) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nombre" value="Nombre del Hito" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $hito->nombre)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción (opcional)" />
                        <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion', $hito->descripcion) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_objetivo" value="Fecha Objetivo" />
                        <x-text-input id="fecha_objetivo" name="fecha_objetivo" type="date" class="mt-1 block w-full" :value="old('fecha_objetivo', $hito->fecha_objetivo?->format('Y-m-d'))" required />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_objetivo')" />
                    </div>

                    <div>
                        <x-input-label for="completado" value="Estado" />
                        <select id="completado" name="completado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="0" @selected(old('completado', $hito->completado) == '0')>Pendiente</option>
                            <option value="1" @selected(old('completado', $hito->completado) == '1')>Completado</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('completado')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id', $hito->proyecto_id) == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('hitos.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Actualizar Hito</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 4 de 4 — `show.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Hito
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Nombre:</strong> {{ $hito->nombre }}</p>
                <p><strong>Descripción:</strong> {{ $hito->descripcion ?? 'Sin descripción' }}</p>
                <p><strong>Proyecto:</strong> {{ $hito->proyecto?->nombre ?? 'N/A' }}</p>
                <p><strong>Fecha objetivo:</strong> {{ $hito->fecha_objetivo?->format('d/m/Y') ?? 'N/A' }}</p>
                <p><strong>Estado:</strong> {{ $hito->completado ? 'Completado' : 'Pendiente' }}</p>

                <div class="pt-4 flex gap-4 border-t">
                    <a href="{{ route('hitos.edit', $hito) }}" class="text-yellow-600 hover:underline">Editar</a>
                    <a href="{{ route('hitos.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Marcos: comprobá que funciona

Entrá a `http://sistema.test/hitos`:

- [ ] Se ve la lista de hitos con su proyecto
- [ ] Podés crear un hito nuevo
- [ ] Al editar, la fecha y el estado aparecen ya cargados
- [ ] El detalle muestra "Completado" o "Pendiente", no un `1` o un `0`

---

## JESÚS — Fase 1: Proyectos

**Tu carpeta:** `resources/views/proyectos/` — **no existe, hay que crearla**

Creá la carpeta `proyectos` dentro de `resources/views/`, y adentro los 4 archivos.

---

### Archivo 1 de 4 — `index.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Proyectos
            </h2>
            <a href="{{ route('proyectos.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Nuevo Proyecto
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inicio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                            @forelse ($proyectos as $proyecto)
                                <tr>
                                    <td class="px-6 py-4">{{ $proyecto->nombre }}</td>
                                    <td class="px-6 py-4">{{ $proyecto->cliente?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $proyecto->pm?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}</td>
                                    <td class="px-6 py-4">{{ $proyecto->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('proyectos.show', $proyecto) }}" class="text-blue-600 hover:underline">Ver</a>
                                        <a href="{{ route('proyectos.edit', $proyecto) }}" class="text-yellow-600 hover:underline ml-3">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No hay proyectos registrados aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $proyectos->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 2 de 4 — `create.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nuevo Proyecto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('proyectos.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="nombre" value="Nombre del Proyecto" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción (opcional)" />
                        <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_inicio" value="Fecha de Inicio" />
                        <x-text-input id="fecha_inicio" name="fecha_inicio" type="date" class="mt-1 block w-full" :value="old('fecha_inicio')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_inicio')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_fin_estimada" value="Fecha de Fin Estimada (opcional)" />
                        <x-text-input id="fecha_fin_estimada" name="fecha_fin_estimada" type="date" class="mt-1 block w-full" :value="old('fecha_fin_estimada')" />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_fin_estimada')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado') == 'pendiente')>Pendiente</option>
                            <option value="en_progreso" @selected(old('estado') == 'en_progreso')>En progreso</option>
                            <option value="completado" @selected(old('estado') == 'completado')>Completado</option>
                            <option value="cancelado" @selected(old('estado') == 'cancelado')>Cancelado</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="cliente_id" value="Cliente" />
                        <select id="cliente_id" name="cliente_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un cliente —</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c->id }}" @selected(old('cliente_id') == $c->id)>{{ $c->nombre }} {{ $c->apellido }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('cliente_id')" />
                    </div>

                    <div>
                        <x-input-label for="pm_id" value="Project Manager" />
                        <select id="pm_id" name="pm_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un PM —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('pm_id') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('pm_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('proyectos.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar Proyecto</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 3 de 4 — `edit.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Proyecto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('proyectos.update', $proyecto) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nombre" value="Nombre del Proyecto" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $proyecto->nombre)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción (opcional)" />
                        <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion', $proyecto->descripcion) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_inicio" value="Fecha de Inicio" />
                        <x-text-input id="fecha_inicio" name="fecha_inicio" type="date" class="mt-1 block w-full" :value="old('fecha_inicio', $proyecto->fecha_inicio?->format('Y-m-d'))" required />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_inicio')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_fin_estimada" value="Fecha de Fin Estimada (opcional)" />
                        <x-text-input id="fecha_fin_estimada" name="fecha_fin_estimada" type="date" class="mt-1 block w-full" :value="old('fecha_fin_estimada', $proyecto->fecha_fin_estimada?->format('Y-m-d'))" />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_fin_estimada')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado', $proyecto->estado) == 'pendiente')>Pendiente</option>
                            <option value="en_progreso" @selected(old('estado', $proyecto->estado) == 'en_progreso')>En progreso</option>
                            <option value="completado" @selected(old('estado', $proyecto->estado) == 'completado')>Completado</option>
                            <option value="cancelado" @selected(old('estado', $proyecto->estado) == 'cancelado')>Cancelado</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="cliente_id" value="Cliente" />
                        <select id="cliente_id" name="cliente_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un cliente —</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c->id }}" @selected(old('cliente_id', $proyecto->cliente_id) == $c->id)>{{ $c->nombre }} {{ $c->apellido }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('cliente_id')" />
                    </div>

                    <div>
                        <x-input-label for="pm_id" value="Project Manager" />
                        <select id="pm_id" name="pm_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un PM —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('pm_id', $proyecto->pm_id) == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('pm_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('proyectos.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Actualizar Proyecto</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 4 de 4 — `show.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Proyecto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Nombre:</strong> {{ $proyecto->nombre }}</p>
                <p><strong>Descripción:</strong> {{ $proyecto->descripcion ?? 'Sin descripción' }}</p>
                <p><strong>Cliente:</strong> {{ $proyecto->cliente?->nombre ?? 'N/A' }} {{ $proyecto->cliente?->apellido }}</p>
                <p><strong>Project Manager:</strong> {{ $proyecto->pm?->name ?? 'N/A' }}</p>
                <p><strong>Estado:</strong> {{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}</p>
                <p><strong>Fecha de inicio:</strong> {{ $proyecto->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</p>
                <p><strong>Fecha de fin estimada:</strong> {{ $proyecto->fecha_fin_estimada?->format('d/m/Y') ?? 'N/A' }}</p>

                <div class="pt-4 border-t">
                    <p class="font-semibold mb-2">Tareas de este proyecto:</p>
                    <ul class="list-disc ms-5">
                        @forelse ($proyecto->tareas as $t)
                            <li>{{ $t->titulo }}</li>
                        @empty
                            <li class="list-none text-gray-500">Sin tareas todavía.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="pt-4 flex gap-4 border-t">
                    <a href="{{ route('proyectos.edit', $proyecto) }}" class="text-yellow-600 hover:underline">Editar</a>
                    <a href="{{ route('proyectos.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Jesús: comprobá que funciona

Entrá a `http://sistema.test/proyectos` y verificá:

- [X] Se ve la lista con el nombre del cliente y del PM en cada fila
- [X] Apretás "+ Nuevo Proyecto", elegís cliente y PM de las listas, guardás → vuelve con
  cartel verde
- [X] Apretás "Editar" → las fechas y los dos desplegables aparecen **ya seleccionados**
- [X] Apretás "Ver" → se ve el detalle con la lista de tareas del proyecto

---

## DANTE — Fase 1: Tareas

**Tu carpeta:** `resources/views/tareas/` — **no existe, hay que crearla**

---

### Archivo 1 de 4 — `index.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Tareas
            </h2>
            <a href="{{ route('tareas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Nueva Tarea
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asignado a</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Límite</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                            @forelse ($tareas as $tarea)
                                <tr>
                                    <td class="px-6 py-4">{{ $tarea->titulo }}</td>
                                    <td class="px-6 py-4">{{ $tarea->proyecto?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $tarea->asignado?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ ucfirst(str_replace('_', ' ', $tarea->estado)) }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($tarea->prioridad) }}</td>
                                    <td class="px-6 py-4">{{ $tarea->fecha_limite?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('tareas.show', $tarea) }}" class="text-blue-600 hover:underline">Ver</a>
                                        <a href="{{ route('tareas.edit', $tarea) }}" class="text-yellow-600 hover:underline ml-3">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No hay tareas registradas aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $tareas->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 2 de 4 — `create.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nueva Tarea
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('tareas.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="titulo" value="Título" />
                        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción (opcional)" />
                        <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado') == 'pendiente')>Pendiente</option>
                            <option value="en_progreso" @selected(old('estado') == 'en_progreso')>En progreso</option>
                            <option value="completada" @selected(old('estado') == 'completada')>Completada</option>
                            <option value="cancelada" @selected(old('estado') == 'cancelada')>Cancelada</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="prioridad" value="Prioridad" />
                        <select id="prioridad" name="prioridad" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="baja" @selected(old('prioridad') == 'baja')>Baja</option>
                            <option value="media" @selected(old('prioridad') == 'media')>Media</option>
                            <option value="alta" @selected(old('prioridad') == 'alta')>Alta</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('prioridad')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_limite" value="Fecha Límite (opcional)" />
                        <x-text-input id="fecha_limite" name="fecha_limite" type="date" class="mt-1 block w-full" :value="old('fecha_limite')" />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_limite')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id') == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div>
                        <x-input-label for="asignado_a" value="Asignar a" />
                        <select id="asignado_a" name="asignado_a" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un responsable —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('asignado_a') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('asignado_a')" />
                    </div>

                    <div>
                        <x-input-label for="solicitud_cambio_id" value="Solicitud de Cambio (opcional)" />
                        <select id="solicitud_cambio_id" name="solicitud_cambio_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">— Ninguna —</option>
                            @foreach ($solicitudes as $s)
                                <option value="{{ $s->id }}" @selected(old('solicitud_cambio_id') == $s->id)>{{ $s->titulo }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('solicitud_cambio_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('tareas.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar Tarea</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 3 de 4 — `edit.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Tarea
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('tareas.update', $tarea) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="titulo" value="Título" />
                        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo', $tarea->titulo)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción (opcional)" />
                        <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion', $tarea->descripcion) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado', $tarea->estado) == 'pendiente')>Pendiente</option>
                            <option value="en_progreso" @selected(old('estado', $tarea->estado) == 'en_progreso')>En progreso</option>
                            <option value="completada" @selected(old('estado', $tarea->estado) == 'completada')>Completada</option>
                            <option value="cancelada" @selected(old('estado', $tarea->estado) == 'cancelada')>Cancelada</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="prioridad" value="Prioridad" />
                        <select id="prioridad" name="prioridad" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="baja" @selected(old('prioridad', $tarea->prioridad) == 'baja')>Baja</option>
                            <option value="media" @selected(old('prioridad', $tarea->prioridad) == 'media')>Media</option>
                            <option value="alta" @selected(old('prioridad', $tarea->prioridad) == 'alta')>Alta</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('prioridad')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_limite" value="Fecha Límite (opcional)" />
                        <x-text-input id="fecha_limite" name="fecha_limite" type="date" class="mt-1 block w-full" :value="old('fecha_limite', $tarea->fecha_limite?->format('Y-m-d'))" />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_limite')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id', $tarea->proyecto_id) == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div>
                        <x-input-label for="asignado_a" value="Asignar a" />
                        <select id="asignado_a" name="asignado_a" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un responsable —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('asignado_a', $tarea->asignado_a) == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('asignado_a')" />
                    </div>

                    <div>
                        <x-input-label for="solicitud_cambio_id" value="Solicitud de Cambio (opcional)" />
                        <select id="solicitud_cambio_id" name="solicitud_cambio_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">— Ninguna —</option>
                            @foreach ($solicitudes as $s)
                                <option value="{{ $s->id }}" @selected(old('solicitud_cambio_id', $tarea->solicitud_cambio_id) == $s->id)>{{ $s->titulo }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('solicitud_cambio_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('tareas.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Actualizar Tarea</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 4 de 4 — `show.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de la Tarea
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Título:</strong> {{ $tarea->titulo }}</p>
                <p><strong>Descripción:</strong> {{ $tarea->descripcion ?? 'Sin descripción' }}</p>
                <p><strong>Proyecto:</strong> {{ $tarea->proyecto?->nombre ?? 'N/A' }}</p>
                <p><strong>Asignado a:</strong> {{ $tarea->asignado?->name ?? 'N/A' }}</p>
                <p><strong>Estado:</strong> {{ ucfirst(str_replace('_', ' ', $tarea->estado)) }}</p>
                <p><strong>Prioridad:</strong> {{ ucfirst($tarea->prioridad) }}</p>
                <p><strong>Fecha límite:</strong> {{ $tarea->fecha_limite?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                <p><strong>Solicitud de cambio:</strong> {{ $tarea->solicitudCambio?->titulo ?? 'Ninguna' }}</p>

                <div class="pt-4 flex gap-4 border-t">
                    <a href="{{ route('tareas.edit', $tarea) }}" class="text-yellow-600 hover:underline">Editar</a>
                    <a href="{{ route('tareas.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Dante: comprobá que funciona

Entrá a `http://sistema.test/tareas` y verificá:

- [X] Se ve la lista con el proyecto y el responsable de cada tarea
- [X] Creás una tarea dejando "Solicitud de Cambio" en **— Ninguna —** → se guarda igual
- [X] Apretás "Editar" → los desplegables aparecen ya seleccionados
- [X] Apretás "Ver" → se ve el detalle completo

---

## Cierre de la Fase 1 (los tres)

Cuando terminaste **tus** 4 archivos y los 4 tildes de tu checklist están puestos, copiá
y pegá esto en la terminal (cambiando tu nombre y tu módulo):

```bash
git add .
git commit -m "Fase 1: vistas de hitos"
git push -u origin fase1-marcos
```

Avisá al grupo que terminaste. **Cuando los tres terminaron**, se juntan las ramas en
`main` y todos corren:

```bash
git checkout main
git pull
php artisan migrate:fresh --seed
```

Recién ahí empieza la Fase 2.

---

---

# FASE 2

> **Alto.** No empieces esta fase hasta que los tres terminaron la Fase 1 y la subieron.
> Primero, todos corren esto:
>
> ```bash
> git checkout main
> git pull
> php artisan migrate:fresh --seed
> ```
>
> Y después, cada uno crea su rama nueva (cambiando el nombre):
>
> ```bash
> git checkout -b fase2-marcos
> ```

| Marcos   | Jesús                | Dante          |
| -------- | --------------------- | -------------- |
| Facturas | Solicitudes de Cambio | Entregables IA |

---

## MARCOS — Fase 2: Facturas

**Tu carpeta:** `resources/views/facturas/` — **crearla**

Este es el último CRUD del sistema.

---

### Archivo 1 de 4 — `index.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Facturas
            </h2>
            <a href="{{ route('facturas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Nueva Factura
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Emisión</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                            @forelse ($facturas as $factura)
                                <tr>
                                    <td class="px-6 py-4">{{ $factura->numero }}</td>
                                    <td class="px-6 py-4">{{ $factura->proyecto?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">${{ number_format($factura->monto, 2) }}</td>
                                    <td class="px-6 py-4">{{ $factura->fecha_emision?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($factura->estado) }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('facturas.show', $factura) }}" class="text-blue-600 hover:underline">Ver</a>
                                        <a href="{{ route('facturas.edit', $factura) }}" class="text-yellow-600 hover:underline ml-3">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No hay facturas registradas aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $facturas->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 2 de 4 — `create.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nueva Factura
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('facturas.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="numero" value="Número de Factura (no se puede repetir)" />
                        <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full" :value="old('numero')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('numero')" />
                    </div>

                    <div>
                        <x-input-label for="monto" value="Monto" />
                        <x-text-input id="monto" name="monto" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('monto')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('monto')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_emision" value="Fecha de Emisión" />
                        <x-text-input id="fecha_emision" name="fecha_emision" type="date" class="mt-1 block w-full" :value="old('fecha_emision')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_emision')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_vencimiento" value="Fecha de Vencimiento (opcional)" />
                        <x-text-input id="fecha_vencimiento" name="fecha_vencimiento" type="date" class="mt-1 block w-full" :value="old('fecha_vencimiento')" />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_vencimiento')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado') == 'pendiente')>Pendiente</option>
                            <option value="pagada" @selected(old('estado') == 'pagada')>Pagada</option>
                            <option value="vencida" @selected(old('estado') == 'vencida')>Vencida</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="detalle" value="Detalle (opcional)" />
                        <textarea id="detalle" name="detalle" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('detalle') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('detalle')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id') == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div>
                        <x-input-label for="emitida_por" value="Emitida por" />
                        <select id="emitida_por" name="emitida_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un usuario —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('emitida_por') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('emitida_por')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('facturas.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar Factura</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 3 de 4 — `edit.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Factura
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('facturas.update', $factura) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="numero" value="Número de Factura" />
                        <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full" :value="old('numero', $factura->numero)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('numero')" />
                    </div>

                    <div>
                        <x-input-label for="monto" value="Monto" />
                        <x-text-input id="monto" name="monto" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('monto', $factura->monto)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('monto')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_emision" value="Fecha de Emisión" />
                        <x-text-input id="fecha_emision" name="fecha_emision" type="date" class="mt-1 block w-full" :value="old('fecha_emision', $factura->fecha_emision?->format('Y-m-d'))" required />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_emision')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_vencimiento" value="Fecha de Vencimiento (opcional)" />
                        <x-text-input id="fecha_vencimiento" name="fecha_vencimiento" type="date" class="mt-1 block w-full" :value="old('fecha_vencimiento', $factura->fecha_vencimiento?->format('Y-m-d'))" />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_vencimiento')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado', $factura->estado) == 'pendiente')>Pendiente</option>
                            <option value="pagada" @selected(old('estado', $factura->estado) == 'pagada')>Pagada</option>
                            <option value="vencida" @selected(old('estado', $factura->estado) == 'vencida')>Vencida</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="detalle" value="Detalle (opcional)" />
                        <textarea id="detalle" name="detalle" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('detalle', $factura->detalle) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('detalle')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id', $factura->proyecto_id) == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div>
                        <x-input-label for="emitida_por" value="Emitida por" />
                        <select id="emitida_por" name="emitida_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un usuario —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('emitida_por', $factura->emitida_por) == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('emitida_por')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('facturas.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Actualizar Factura</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 4 de 4 — `show.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de la Factura
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Número:</strong> {{ $factura->numero }}</p>
                <p><strong>Proyecto:</strong> {{ $factura->proyecto?->nombre ?? 'N/A' }}</p>
                <p><strong>Emitida por:</strong> {{ $factura->emisor?->name ?? 'N/A' }}</p>
                <p><strong>Monto:</strong> ${{ number_format($factura->monto, 2) }}</p>
                <p><strong>Fecha de emisión:</strong> {{ $factura->fecha_emision?->format('d/m/Y') ?? 'N/A' }}</p>
                <p><strong>Fecha de vencimiento:</strong> {{ $factura->fecha_vencimiento?->format('d/m/Y') ?? 'Sin vencimiento' }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($factura->estado) }}</p>
                <p><strong>Detalle:</strong> {{ $factura->detalle ?? 'Sin detalle' }}</p>

                <div class="pt-4 flex gap-4 border-t">
                    <a href="{{ route('facturas.edit', $factura) }}" class="text-yellow-600 hover:underline">Editar</a>
                    <a href="{{ route('facturas.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Marcos: comprobá que funciona

Entrá a `http://sistema.test/facturas`:

- [ ] Se ve la lista con los montos formateados con `$` y dos decimales
- [ ] Podés crear una factura nueva
- [ ] **Probá poner un número de factura que ya existe** → tiene que aparecer un mensaje
  de error en rojo debajo del campo, y no guardarse. Eso está bien, es a propósito.
- [ ] Al editar, las fechas aparecen ya cargadas

**Con esto quedan los 7 módulos completos.**

---

## JESÚS — Fase 2: Solicitudes de Cambio

**Tu carpeta:** `resources/views/solicitudes_cambio/` — **crearla**

> ⚠️ **Ojo con el nombre de la carpeta:** va con **guion bajo** (`solicitudes_cambio`).
> Adentro del código vas a ver `solicitudes-cambio` con **guion medio** — eso está bien
> así, no lo cambies. Son distintos a propósito.

---

### Archivo 1 de 4 — `index.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Solicitudes de Cambio
            </h2>
            <a href="{{ route('solicitudes-cambio.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Nueva Solicitud
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                            @forelse ($solicitudes as $solicitud)
                                <tr>
                                    <td class="px-6 py-4">{{ $solicitud->titulo }}</td>
                                    <td class="px-6 py-4">{{ $solicitud->proyecto?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $solicitud->solicitante?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($solicitud->estado) }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($solicitud->prioridad) }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('solicitudes-cambio.show', $solicitud) }}" class="text-blue-600 hover:underline">Ver</a>
                                        <a href="{{ route('solicitudes-cambio.edit', $solicitud) }}" class="text-yellow-600 hover:underline ml-3">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No hay solicitudes registradas aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $solicitudes->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 2 de 4 — `create.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nueva Solicitud de Cambio
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('solicitudes-cambio.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="titulo" value="Título" />
                        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción" />
                        <textarea id="descripcion" name="descripcion" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('descripcion') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado') == 'pendiente')>Pendiente</option>
                            <option value="aprobada" @selected(old('estado') == 'aprobada')>Aprobada</option>
                            <option value="rechazada" @selected(old('estado') == 'rechazada')>Rechazada</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="prioridad" value="Prioridad" />
                        <select id="prioridad" name="prioridad" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="baja" @selected(old('prioridad') == 'baja')>Baja</option>
                            <option value="media" @selected(old('prioridad') == 'media')>Media</option>
                            <option value="alta" @selected(old('prioridad') == 'alta')>Alta</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('prioridad')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id') == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div>
                        <x-input-label for="solicitado_por" value="Solicitado por" />
                        <select id="solicitado_por" name="solicitado_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un usuario —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('solicitado_por') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('solicitado_por')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('solicitudes-cambio.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar Solicitud</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 3 de 4 — `edit.blade.php`

> Acá la variable se llama **`$solicitud`**. Ya está puesta bien en el código, solo copialo.

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Solicitud de Cambio
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('solicitudes-cambio.update', $solicitud) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="titulo" value="Título" />
                        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo', $solicitud->titulo)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción" />
                        <textarea id="descripcion" name="descripcion" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('descripcion', $solicitud->descripcion) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado', $solicitud->estado) == 'pendiente')>Pendiente</option>
                            <option value="aprobada" @selected(old('estado', $solicitud->estado) == 'aprobada')>Aprobada</option>
                            <option value="rechazada" @selected(old('estado', $solicitud->estado) == 'rechazada')>Rechazada</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="prioridad" value="Prioridad" />
                        <select id="prioridad" name="prioridad" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="baja" @selected(old('prioridad', $solicitud->prioridad) == 'baja')>Baja</option>
                            <option value="media" @selected(old('prioridad', $solicitud->prioridad) == 'media')>Media</option>
                            <option value="alta" @selected(old('prioridad', $solicitud->prioridad) == 'alta')>Alta</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('prioridad')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id', $solicitud->proyecto_id) == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div>
                        <x-input-label for="solicitado_por" value="Solicitado por" />
                        <select id="solicitado_por" name="solicitado_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un usuario —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('solicitado_por', $solicitud->solicitado_por) == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('solicitado_por')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('solicitudes-cambio.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Actualizar Solicitud</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 4 de 4 — `show.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de la Solicitud de Cambio
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Título:</strong> {{ $solicitud->titulo }}</p>
                <p><strong>Descripción:</strong> {{ $solicitud->descripcion }}</p>
                <p><strong>Proyecto:</strong> {{ $solicitud->proyecto?->nombre ?? 'N/A' }}</p>
                <p><strong>Solicitado por:</strong> {{ $solicitud->solicitante?->name ?? 'N/A' }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($solicitud->estado) }}</p>
                <p><strong>Prioridad:</strong> {{ ucfirst($solicitud->prioridad) }}</p>

                <div class="pt-4 border-t">
                    <p class="font-semibold mb-2">Tareas generadas por esta solicitud:</p>
                    <ul class="list-disc ms-5">
                        @forelse ($solicitud->tareas as $t)
                            <li>{{ $t->titulo }}</li>
                        @empty
                            <li class="list-none text-gray-500">Sin tareas asociadas.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="pt-4 flex gap-4 border-t">
                    <a href="{{ route('solicitudes-cambio.edit', $solicitud) }}" class="text-yellow-600 hover:underline">Editar</a>
                    <a href="{{ route('solicitudes-cambio.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Jesús: comprobá que funciona

Entrá a `http://sistema.test/solicitudes-cambio` (con **guion medio** en la URL):

- [X] Se ve la lista con proyecto y solicitante
- [X] Podés crear una solicitud nueva
- [X] Al editar, los desplegables aparecen ya seleccionados
- [X] El detalle muestra la lista de tareas asociadas

---

## DANTE — Fase 2: Entregables IA

**Tu carpeta:** `resources/views/entregables/` — **crearla**

> ⚠️ La carpeta se llama **`entregables`**, sin el `_ia` al final.

---

### Archivo 1 de 4 — `index.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Entregables
            </h2>
            <a href="{{ route('entregables.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Nuevo Entregable
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Generado por</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                            @forelse ($entregables as $entregable)
                                <tr>
                                    <td class="px-6 py-4">{{ $entregable->titulo }}</td>
                                    <td class="px-6 py-4">{{ $entregable->proyecto?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($entregable->tipo) }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($entregable->estado) }}</td>
                                    <td class="px-6 py-4">{{ $entregable->generador?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('entregables.show', $entregable) }}" class="text-blue-600 hover:underline">Ver</a>
                                        <a href="{{ route('entregables.edit', $entregable) }}" class="text-yellow-600 hover:underline ml-3">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No hay entregables registrados aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $entregables->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 2 de 4 — `create.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nuevo Entregable
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('entregables.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="titulo" value="Título" />
                        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
                    </div>

                    <div>
                        <x-input-label for="contenido" value="Contenido" />
                        <textarea id="contenido" name="contenido" rows="8" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('contenido') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('contenido')" />
                    </div>

                    <div>
                        <x-input-label for="tipo" value="Tipo (ej: documento, informe, resumen)" />
                        <x-text-input id="tipo" name="tipo" type="text" class="mt-1 block w-full" :value="old('tipo', 'documento')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('tipo')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="borrador" @selected(old('estado') == 'borrador')>Borrador</option>
                            <option value="revisado" @selected(old('estado') == 'revisado')>Revisado</option>
                            <option value="aprobado" @selected(old('estado') == 'aprobado')>Aprobado</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id') == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div>
                        <x-input-label for="generado_por" value="Generado por" />
                        <select id="generado_por" name="generado_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un usuario —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('generado_por') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('generado_por')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('entregables.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar Entregable</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 3 de 4 — `edit.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Entregable
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('entregables.update', $entregable) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="titulo" value="Título" />
                        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo', $entregable->titulo)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
                    </div>

                    <div>
                        <x-input-label for="contenido" value="Contenido" />
                        <textarea id="contenido" name="contenido" rows="8" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('contenido', $entregable->contenido) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('contenido')" />
                    </div>

                    <div>
                        <x-input-label for="tipo" value="Tipo" />
                        <x-text-input id="tipo" name="tipo" type="text" class="mt-1 block w-full" :value="old('tipo', $entregable->tipo)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('tipo')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="borrador" @selected(old('estado', $entregable->estado) == 'borrador')>Borrador</option>
                            <option value="revisado" @selected(old('estado', $entregable->estado) == 'revisado')>Revisado</option>
                            <option value="aprobado" @selected(old('estado', $entregable->estado) == 'aprobado')>Aprobado</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id', $entregable->proyecto_id) == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div>
                        <x-input-label for="generado_por" value="Generado por" />
                        <select id="generado_por" name="generado_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un usuario —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('generado_por', $entregable->generado_por) == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('generado_por')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('entregables.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Actualizar Entregable</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Archivo 4 de 4 — `show.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Entregable
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Título:</strong> {{ $entregable->titulo }}</p>
                <p><strong>Proyecto:</strong> {{ $entregable->proyecto?->nombre ?? 'N/A' }}</p>
                <p><strong>Generado por:</strong> {{ $entregable->generador?->name ?? 'N/A' }}</p>
                <p><strong>Tipo:</strong> {{ ucfirst($entregable->tipo) }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($entregable->estado) }}</p>

                <div class="pt-4 border-t">
                    <p class="font-semibold mb-2">Contenido:</p>
                    <div class="whitespace-pre-wrap bg-gray-50 p-4 rounded">{{ $entregable->contenido }}</div>
                </div>

                <div class="pt-4 flex gap-4 border-t">
                    <a href="{{ route('entregables.edit', $entregable) }}" class="text-yellow-600 hover:underline">Editar</a>
                    <a href="{{ route('entregables.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

### Dante: comprobá que funciona

Entrá a `http://sistema.test/entregables`:

- [X] Se ve la lista con proyecto y quién lo generó
- [X] Podés crear un entregable nuevo
- [X] Al editar, el contenido largo aparece completo en el cuadro de texto
- [X] En el detalle, el contenido respeta los saltos de línea

---

## Cierre de la Fase 2

```bash
git add .
git commit -m "Fase 2: vistas de <tu-modulo>"
git push -u origin fase2-<tu-nombre>
```

Avisá al grupo. Cuando los tres terminaron, merge a `main` y todos:

```bash
git checkout main
git pull
php artisan migrate:fresh --seed
```

---

# FASE 3

| Marcos             | Jesús               | Dante     |
| ------------------ | -------------------- | --------- |
| Ayuda en el repaso | Menú de navegación | Dashboard |

En esta fase ya están los 7 CRUDs listos. Falta unir todo: el menú para llegar a los
módulos sin escribir direcciones, y el panel de inicio con los números.

> ⚠️ **Jesús va primero.** Los dos tocan archivos compartidos, así que Jesús hace su
> parte y la sube a `main`; recién ahí Dante arranca la suya. Si trabajan al mismo
> tiempo, git se confunde y se pierde trabajo.

**Marcos en esta fase:** no te toca archivo propio. Tu tarea es probar el sistema completo
mientras los otros dos trabajan, y anotar en un papel todo lo que falle: pantallas que no
abren, botones que no llevan a ningún lado, datos que se ven mal. Esa lista es el punto de
partida de la Fase 4. Si querés adelantar trabajo, empezá con el Paso 2 de la Fase 4
(agregar el botón Eliminar) en los módulos que hiciste vos: Hitos y Facturas.

Jesús y Dante crean su rama: `git checkout -b fase3-<tu-nombre>`

---

## JESÚS — Fase 3: Menú de navegación

> ⚠️ **Vos hacés esto primero y lo subís a `main` antes de que Dante empiece.**

**Tu archivo:** `resources/views/layouts/navigation.blade.php` — **ya existe**

Hoy el menú de arriba solo tiene Dashboard, Usuarios y Guía Técnica. Hay que agregar los
7 módulos para no tener que escribir las direcciones a mano.

### Cambio 1 de 2 — el menú de computadora

Abrí el archivo y buscá este bloque (está cerca del principio):

```blade
<x-nav-link :href="route('tutorial')" :active="request()->routeIs('tutorial')">
    {{ __('Guía Técnica') }}
</x-nav-link>
```

**Justo debajo** de ese bloque (antes del `</div>` que le sigue), pegá esto:

```blade
<x-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">
    {{ __('Clientes') }}
</x-nav-link>
<x-nav-link :href="route('proyectos.index')" :active="request()->routeIs('proyectos.*')">
    {{ __('Proyectos') }}
</x-nav-link>
<x-nav-link :href="route('tareas.index')" :active="request()->routeIs('tareas.*')">
    {{ __('Tareas') }}
</x-nav-link>
<x-nav-link :href="route('hitos.index')" :active="request()->routeIs('hitos.*')">
    {{ __('Hitos') }}
</x-nav-link>
<x-nav-link :href="route('solicitudes-cambio.index')" :active="request()->routeIs('solicitudes-cambio.*')">
    {{ __('Cambios') }}
</x-nav-link>
<x-nav-link :href="route('entregables.index')" :active="request()->routeIs('entregables.*')">
    {{ __('Entregables') }}
</x-nav-link>
<x-nav-link :href="route('facturas.index')" :active="request()->routeIs('facturas.*')">
    {{ __('Facturas') }}
</x-nav-link>
```

### Cambio 2 de 2 — el menú de celular

En el **mismo archivo**, más abajo, buscá este bloque:

```blade
<div class="pt-2 pb-3 space-y-1">
```

Adentro de ese bloque, después del último `<x-responsive-nav-link>` que encuentres,
pegá esto:

```blade
<x-responsive-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">
    {{ __('Clientes') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('proyectos.index')" :active="request()->routeIs('proyectos.*')">
    {{ __('Proyectos') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('tareas.index')" :active="request()->routeIs('tareas.*')">
    {{ __('Tareas') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('hitos.index')" :active="request()->routeIs('hitos.*')">
    {{ __('Hitos') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('solicitudes-cambio.index')" :active="request()->routeIs('solicitudes-cambio.*')">
    {{ __('Cambios') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('entregables.index')" :active="request()->routeIs('entregables.*')">
    {{ __('Entregables') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('facturas.index')" :active="request()->routeIs('facturas.*')">
    {{ __('Facturas') }}
</x-responsive-nav-link>
```

### Jesús: comprobá que funciona

- [X] Arriba de la pantalla aparecen los 7 módulos nuevos
- [X] Hacés clic en cada uno y te lleva a su listado
- [X] El que estás viendo aparece resaltado
- [X] Achicás la ventana del navegador hasta que aparezca el menú de tres rayitas
  → adentro también están los 7

---

## DANTE — Fase 3: Dashboard

> ⚠️ **Esperá a que Jesús suba su parte a `main`.** Después:
> `git checkout main && git pull && git checkout -b fase3-dante`

**Tu archivo:** `resources/views/dashboard.blade.php` — **ya existe**

Hoy es la pantalla vacía que viene por defecto. Abrilo, **borrá todo** y pegá esto:

```blade
@php
    $totalClientes = \App\Models\Cliente::count();
    $totalProyectos = \App\Models\Proyecto::count();
    $tareasPendientes = \App\Models\Tarea::where('estado', 'pendiente')->count();
    $facturasPendientes = \App\Models\Factura::where('estado', 'pendiente')->count();
    $totalHitos = \App\Models\Hito::count();
    $totalEntregables = \App\Models\EntregableIA::count();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel de Control
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-700">
                    Bienvenido, <strong>{{ Auth::user()->name }}</strong>.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <a href="{{ route('clientes.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-gray-900">{{ $totalClientes }}</div>
                    <div class="text-sm text-gray-500 mt-1">Clientes</div>
                </a>

                <a href="{{ route('proyectos.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-gray-900">{{ $totalProyectos }}</div>
                    <div class="text-sm text-gray-500 mt-1">Proyectos</div>
                </a>

                <a href="{{ route('tareas.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-orange-600">{{ $tareasPendientes }}</div>
                    <div class="text-sm text-gray-500 mt-1">Tareas pendientes</div>
                </a>

                <a href="{{ route('hitos.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-gray-900">{{ $totalHitos }}</div>
                    <div class="text-sm text-gray-500 mt-1">Hitos</div>
                </a>

                <a href="{{ route('entregables.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-gray-900">{{ $totalEntregables }}</div>
                    <div class="text-sm text-gray-500 mt-1">Entregables</div>
                </a>

                <a href="{{ route('facturas.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-red-600">{{ $facturasPendientes }}</div>
                    <div class="text-sm text-gray-500 mt-1">Facturas pendientes</div>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>
```

### Dante: comprobá que funciona

- [X] Entrás al Dashboard y ves 6 tarjetas con números
- [X] Los números coinciden con lo que hay en cada listado
- [X] Hacés clic en una tarjeta y te lleva a ese módulo
- [X] Creás un cliente nuevo, volvés al Dashboard y el número de Clientes subió en 1

---

## Cierre de la Fase 3

Igual que siempre: commit, push, avisar, merge a `main`, y todos
`git pull` + `php artisan migrate:fresh --seed`.

---

# SPRINT 2 — JESÚS: Buscador y filtros en los listados

> Esta sección corresponde a la tarjeta **“Buscador y filtros en los
> listados”**. No cambies ni crees otra rama: trabajá sobre la misma rama que
> ya usa el equipo.

## Qué vas a lograr

Vas a agregar búsqueda por texto, filtro de estado y paginación de 15 registros
en estos módulos:

1. Proyectos.
2. Tareas.
3. Solicitudes de Cambio.
4. Facturas, solamente si Marcos ya creó `resources/views/facturas/index.blade.php`.

Antes de editar, ejecutá `git pull`. No borres el scope `visiblePara`: ese scope
impide que un Cliente encuentre registros pertenecientes a otra empresa.

---

## Paso 1 — Modificar `ProyectoController`

Abrí `app/Http/Controllers/ProyectoController.php` y reemplazá solamente el
método `index` por este:

```php
public function index(Request $request)
{
    $proyectos = Proyecto::visiblePara($request->user())
        ->with(['cliente', 'pm'])
        ->when($request->filled('q'), function ($query) use ($request) {
            $texto = $request->string('q')->trim()->toString();

            $query->where(function ($subquery) use ($texto) {
                $subquery->where('nombre', 'like', "%{$texto}%")
                    ->orWhere('descripcion', 'like', "%{$texto}%");
            });
        })
        ->when($request->filled('estado'), fn ($query) =>
            $query->where('estado', $request->string('estado')->toString())
        )
        ->latest()
        ->paginate(15)
        ->withQueryString();

    return view('proyectos.index', compact('proyectos'));
}
```

Después, en `resources/views/proyectos/index.blade.php`, pegá este formulario
después del mensaje `session('success')` y antes de la tabla:

```blade
<form method="GET" action="{{ route('proyectos.index') }}"
      class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
    <div class="md:col-span-2">
        <label for="q" class="block text-sm font-medium text-gray-700">Buscar</label>
        <input id="q" name="q" type="text" value="{{ request('q') }}"
               placeholder="Nombre o descripción"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>
    <div>
        <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
        <select id="estado" name="estado"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">Todos</option>
            <option value="pendiente" @selected(request('estado') === 'pendiente')>Pendiente</option>
            <option value="en_progreso" @selected(request('estado') === 'en_progreso')>En progreso</option>
            <option value="completado" @selected(request('estado') === 'completado')>Completado</option>
            <option value="cancelado" @selected(request('estado') === 'cancelado')>Cancelado</option>
        </select>
    </div>
    <div class="flex gap-3">
        <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Filtrar
        </button>
        <a href="{{ route('proyectos.index') }}"
           class="px-4 py-2 text-gray-600 hover:underline">Limpiar</a>
    </div>
</form>
```

---

## Paso 2 — Modificar `TareaController`

Abrí `app/Http/Controllers/TareaController.php` y reemplazá solamente el método
`index` por este:

```php
public function index(Request $request)
{
    $tareas = Tarea::visiblePara($request->user())
        ->with(['proyecto', 'asignado'])
        ->when($request->filled('q'), function ($query) use ($request) {
            $texto = $request->string('q')->trim()->toString();

            $query->where(function ($subquery) use ($texto) {
                $subquery->where('titulo', 'like', "%{$texto}%")
                    ->orWhere('descripcion', 'like', "%{$texto}%");
            });
        })
        ->when($request->filled('estado'), fn ($query) =>
            $query->where('estado', $request->string('estado')->toString())
        )
        ->latest()
        ->paginate(15)
        ->withQueryString();

    return view('tareas.index', compact('tareas'));
}
```

En `resources/views/tareas/index.blade.php`, pegá antes de la tabla el mismo
formulario del paso anterior, pero cambiá la ruta por `tareas.index`, el texto
de ayuda por `Título o descripción` y usá estas opciones:

```blade
<option value="">Todos</option>
<option value="pendiente" @selected(request('estado') === 'pendiente')>Pendiente</option>
<option value="en_progreso" @selected(request('estado') === 'en_progreso')>En progreso</option>
<option value="completada" @selected(request('estado') === 'completada')>Completada</option>
<option value="cancelada" @selected(request('estado') === 'cancelada')>Cancelada</option>
```

---

## Paso 3 — Modificar `SolicitudCambioController`

Abrí `app/Http/Controllers/SolicitudCambioController.php` y reemplazá solamente
el método `index` por este:

```php
public function index(Request $request)
{
    $solicitudes = SolicitudCambio::visiblePara($request->user())
        ->with(['proyecto', 'solicitante'])
        ->when($request->filled('q'), function ($query) use ($request) {
            $texto = $request->string('q')->trim()->toString();

            $query->where(function ($subquery) use ($texto) {
                $subquery->where('titulo', 'like', "%{$texto}%")
                    ->orWhere('descripcion', 'like', "%{$texto}%");
            });
        })
        ->when($request->filled('estado'), fn ($query) =>
            $query->where('estado', $request->string('estado')->toString())
        )
        ->latest()
        ->paginate(15)
        ->withQueryString();

    return view('solicitudes_cambio.index', compact('solicitudes'));
}
```

En `resources/views/solicitudes_cambio/index.blade.php`, pegá antes de la tabla
el formulario del paso 1, cambiando la ruta por `solicitudes-cambio.index`, el
texto de ayuda por `Título o descripción` y usando estas opciones:

```blade
<option value="">Todos</option>
<option value="pendiente" @selected(request('estado') === 'pendiente')>Pendiente</option>
<option value="aprobada" @selected(request('estado') === 'aprobada')>Aprobada</option>
<option value="rechazada" @selected(request('estado') === 'rechazada')>Rechazada</option>
```

---

## Paso 4 — Facturas, solamente si la vista ya existe

Primero verificá si existe `resources/views/facturas/index.blade.php`.

- Si no existe, no inventes la vista: avisá al grupo y dejá Facturas para el
  repaso posterior.
- Si existe, reemplazá el método `index` de `FacturaController` aplicando el
  mismo patrón. Buscá en `numero` y `detalle`, filtrá por `estado`, usá
  `paginate(15)` y terminá con `withQueryString()`.
- Las opciones de estado son `pendiente`, `pagada` y `vencida`.
- Conservá `Factura::visiblePara($request->user())` al comienzo de la consulta.

---

## Paso 5 — Pruebas de Jesús

Ejecutá:

```bash
php artisan optimize:clear
php artisan view:cache
php artisan test
```

Después probá en el navegador:

- buscar por una palabra completa y por una parte del nombre;
- combinar texto y estado;
- usar **Limpiar** y comprobar que vuelvan todos los resultados permitidos;
- avanzar de página y confirmar que `q` y `estado` sigan en la URL;
- entrar como `jefe@example.com` y luego como `cliente@example.com`;
- confirmar que el Cliente jamás vea registros de otra empresa, aunque filtre.

Checklist:

- [ ] Buscador y filtro en Proyectos.
- [ ] Buscador y filtro en Tareas.
- [ ] Buscador y filtro en Solicitudes de Cambio.
- [ ] Facturas agregadas o informadas como pendientes porque falta su vista.
- [ ] Paginación de 15 con filtros conservados.
- [ ] Scoping del Cliente conservado.

Para subir sobre la misma rama del equipo:

```bash
git add app/Http/Controllers resources/views
git commit -m "Agregar buscador y filtros a los listados"
git push
```

---

# SPRINT 2 — DANTE: Panel de reportes del Jefe

> Esta sección corresponde a la tarjeta **“Panel de reportes del Jefe en el
> Dashboard”** de `tareas_sprint_jesus_dante.txt`.

## Qué vas a lograr

Vas a agregar arriba de las tarjetas actuales un panel con números globales:

- proyectos pendientes, en progreso, completados y cancelados;
- tareas pendientes, en progreso, completadas y canceladas;
- total facturado y total pendiente de cobro;
- cantidad de tareas vencidas.

El panel lo ven los roles internos `Jefe`, `PM`, `PO` y `Programador`. El rol
`Cliente` **no lo ve** y continúa viendo solamente los números de su empresa.

Para esta tarea vas a modificar solamente:

1. `routes/web.php`
2. `resources/views/dashboard.blade.php`

> **Excepción a la regla general:** en esta tarea sí está permitido modificar
> `routes/web.php`, porque ahí se calcularán los reportes. No modifiques ninguna
> otra ruta.

---

## Paso 1 — Actualizar la rama de trabajo

Abrí una terminal en la raíz de tu copia del proyecto. La ubicación puede ser
distinta en cada computadora; estás en la carpeta correcta si allí aparecen
`artisan`, `composer.json` y `package.json`.

Ejecutá:

```bash
git pull
```

> **No cambies ni crees otra rama.** Esta tarea se hace sobre la misma rama que
> ya usa el equipo. Antes de editar, `git pull` trae los últimos cambios de tus
> compañeros.

---

## Paso 2 — Calcular los datos fuera de la vista

Abrí `routes/web.php` y buscá este bloque cerca del principio:

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
```

Reemplazá **todo ese bloque** por este código:

```php
Route::get('/dashboard', function () {
    $usuario = auth()->user();
    $esCliente = $usuario->esCliente();

    // Números del dashboard actual. Para Cliente se limitan a su empresa.
    $datos = [
        'esCliente' => $esCliente,
        'totalClientes' => $esCliente ? null : \App\Models\Cliente::count(),
        'totalProyectos' => \App\Models\Proyecto::visiblePara($usuario)->count(),
        'tareasPendientes' => \App\Models\Tarea::visiblePara($usuario)
            ->where('estado', 'pendiente')->count(),
        'facturasPendientes' => \App\Models\Factura::visiblePara($usuario)
            ->where('estado', 'pendiente')->count(),
        'totalHitos' => \App\Models\Hito::visiblePara($usuario)->count(),
        'totalEntregables' => \App\Models\EntregableIA::visiblePara($usuario)->count(),
    ];

    // Los reportes son globales y nunca se calculan para el rol Cliente.
    if (! $esCliente) {
        $datos['proyectosPorEstado'] = [
            'pendiente' => \App\Models\Proyecto::where('estado', 'pendiente')->count(),
            'en_progreso' => \App\Models\Proyecto::where('estado', 'en_progreso')->count(),
            'completado' => \App\Models\Proyecto::where('estado', 'completado')->count(),
            'cancelado' => \App\Models\Proyecto::where('estado', 'cancelado')->count(),
        ];

        $datos['tareasPorEstado'] = [
            'pendiente' => \App\Models\Tarea::where('estado', 'pendiente')->count(),
            'en_progreso' => \App\Models\Tarea::where('estado', 'en_progreso')->count(),
            'completada' => \App\Models\Tarea::where('estado', 'completada')->count(),
            'cancelada' => \App\Models\Tarea::where('estado', 'cancelada')->count(),
        ];

        $datos['totalFacturado'] = \App\Models\Factura::sum('monto');

        // Pendiente de cobro incluye facturas pendientes y vencidas: ninguna
        // de las dos fue pagada todavía.
        $datos['totalPendienteCobro'] = \App\Models\Factura::whereIn(
            'estado',
            ['pendiente', 'vencida']
        )->sum('monto');

        $datos['tareasVencidas'] = \App\Models\Tarea::whereDate('fecha_limite', '<', today())
            ->whereNotIn('estado', ['completada', 'cancelada'])
            ->count();
    }

    return view('dashboard', $datos);
})->middleware(['auth', 'verified'])->name('dashboard');
```

Esto cumple una regla importante: las consultas se hacen con Eloquent en la
ruta y no con loops dentro del archivo Blade.

---

## Paso 3 — Quitar las consultas viejas del Dashboard

Abrí `resources/views/dashboard.blade.php`.

Al principio del archivo hay un bloque que comienza con `@php` y termina con
`@endphp`. Borrá **todo ese bloque**, porque esos datos ahora llegan desde la
ruta y no deben consultarse dentro de la vista.

Después de borrarlo, la primera línea del archivo debe ser:

```blade
<x-app-layout>
```

---

## Paso 4 — Agregar el panel de reportes

En el mismo `dashboard.blade.php`, buscá el bloque de bienvenida que termina
así:

```blade
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
```

Pegá el siguiente panel **entre esos dos bloques**, justo antes de la grilla de
tarjetas que ya existía:

```blade
@if (! $esCliente)
    <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Reportes generales</h3>
            <p class="text-sm text-gray-500">Resumen global para los roles internos.</p>
        </div>

        <div>
            <h4 class="font-semibold text-gray-700 mb-3">Proyectos por estado</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $proyectosPorEstado['pendiente'] }}</div>
                    <div class="text-sm text-gray-500">Pendientes</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-700">{{ $proyectosPorEstado['en_progreso'] }}</div>
                    <div class="text-sm text-gray-500">En progreso</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-700">{{ $proyectosPorEstado['completado'] }}</div>
                    <div class="text-sm text-gray-500">Completados</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-700">{{ $proyectosPorEstado['cancelado'] }}</div>
                    <div class="text-sm text-gray-500">Cancelados</div>
                </div>
            </div>
        </div>

        <div>
            <h4 class="font-semibold text-gray-700 mb-3">Tareas por estado</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $tareasPorEstado['pendiente'] }}</div>
                    <div class="text-sm text-gray-500">Pendientes</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-700">{{ $tareasPorEstado['en_progreso'] }}</div>
                    <div class="text-sm text-gray-500">En progreso</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-700">{{ $tareasPorEstado['completada'] }}</div>
                    <div class="text-sm text-gray-500">Completadas</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-700">{{ $tareasPorEstado['cancelada'] }}</div>
                    <div class="text-sm text-gray-500">Canceladas</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-indigo-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-indigo-700">
                    $ {{ number_format($totalFacturado, 2, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500">Total facturado</div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-yellow-700">
                    $ {{ number_format($totalPendienteCobro, 2, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500">Pendiente de cobro</div>
            </div>
            <div class="bg-red-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-red-700">{{ $tareasVencidas }}</div>
                <div class="text-sm text-gray-500">Tareas vencidas</div>
            </div>
        </div>
    </section>
@endif
```

No borres la grilla anterior: el nuevo panel debe quedar **arriba de lo que ya
había**, tal como pide la tarjeta.

---

## Paso 5 — Limpiar caché y comprobar el código

Ejecutá:

```bash
php artisan optimize:clear
php artisan view:cache
php artisan test
```

Si las dos pruebas viejas de `RegistrationTest` fallan porque `/register`
devuelve 404, no corresponde a esta tarea: el registro público está desactivado
a propósito.

---

## Paso 6 — Probar los roles en el navegador

### Prueba 1 — Jefe

1. Iniciá sesión con `jefe@example.com` / `1234`.
2. Entrá al Dashboard.
3. Comprobá que aparezca el título **Reportes generales**.
4. Verificá los cuatro estados de proyectos y los cuatro estados de tareas.
5. Verificá total facturado, pendiente de cobro y tareas vencidas.

### Prueba 2 — Cliente

1. Cerrá la sesión del Jefe.
2. Iniciá sesión con `cliente@example.com` / `1234`.
3. Entrá al Dashboard.
4. Comprobá que **no aparezca** el título **Reportes generales**.
5. Comprobá que el Cliente siga viendo las tarjetas normales con los datos de
   su propia empresa.

### Prueba 3 — Roles internos restantes

Repetí la entrada con `pm@example.com`, `po@example.com` y `dev@example.com`.
Los tres deben ver el panel de reportes generales.

Checklist final:

- [ ] Tarjetas de proyectos por estado.
- [ ] Tarjetas de tareas por estado.
- [ ] Total facturado y pendiente de cobro.
- [ ] Contador de tareas vencidas.
- [ ] El Cliente no ve el panel global.
- [ ] Jefe, PM, PO y Programador sí ven el panel global.
- [ ] El dashboard anterior sigue visible y funcionando.

---

## Paso 7 — Commit y push

Ejecutá exactamente:

```bash
git add routes/web.php resources/views/dashboard.blade.php
git commit -m "Agregar panel de reportes al dashboard"
git push
```

Después avisá al grupo para que otra persona haga `git pull` y pruebe el
Dashboard como Jefe y como Cliente.

---

# SPRINT 2 — Testing del sistema

> Esta es una tarea compartida. Dante realiza las pruebas y Jesús revisa los
> resultados; después pueden invertir los roles para la revisión cruzada. No
> cambien ni creen otra rama.

## Objetivo

Hacer un recorrido E2E (de punta a punta) como una persona real y dejar una red
básica de pruebas automáticas. El checklist de la tarjeta pide:

- probar el login;
- probar la creación de usuarios;
- detectar y corregir bugs;
- confirmar el renderizado condicional por rol.

Una prueba no se marca como terminada solo porque la pantalla abre: hay que
anotar qué usuario se usó, qué se esperaba y qué ocurrió.

---

## Paso 1 — Preparar cada computadora

Desde la raíz del proyecto:

```bash
git pull
composer install
npm install
php artisan migrate
php artisan db:seed
php artisan optimize:clear
npm run build
php artisan test
```

No uses `migrate:fresh` si tenés información local que quieras conservar. Si
los usuarios de prueba no existen y `db:seed` falla, mandá la captura completa
al grupo antes de borrar la base.

---

## Paso 2 — Probar el login

Probá las cinco cuentas, una por una:

| Rol | Email | Contraseña |
| --- | --- | --- |
| Jefe | `jefe@example.com` | `1234` |
| PM | `pm@example.com` | `1234` |
| PO | `po@example.com` | `1234` |
| Programador | `dev@example.com` | `1234` |
| Cliente | `cliente@example.com` | `1234` |

Para cada cuenta:

1. Abrí `/login`.
2. Ingresá email y contraseña.
3. Confirmá que redirija al Dashboard sin error 403, 404 o 500.
4. Verificá que arriba aparezca el nombre del usuario correcto.
5. Cerrá sesión antes de probar la cuenta siguiente.
6. Probá una contraseña incorrecta y confirmá que el sistema no permita entrar.

---

## Paso 3 — Probar la creación de usuarios

1. Entrá como `jefe@example.com`.
2. Abrí **Usuarios y Roles**.
3. Creá un usuario de prueba con un email que no exista.
4. Asignale un rol y comprobá que aparezca en el listado.
5. Cerrá sesión e iniciá sesión con la cuenta recién creada.
6. Confirmá que vea solamente las opciones correspondientes a su rol.
7. Volvé a entrar como Jefe y eliminá o desactivá el dato de prueba si el
   sistema ofrece esa acción. No borres usuarios directamente desde MySQL.

También hay que probar que PM, PO, Programador y Cliente no puedan abrir
`/usuarios/crear` escribiendo la URL manualmente. Deben recibir 403.

---

## Paso 4 — Confirmar el renderizado condicional por rol

Usá esta matriz como resultado esperado:

| Acción visible | Jefe | PM | PO | Programador | Cliente |
| --- | :---: | :---: | :---: | :---: | :---: |
| Usuarios y Roles | Sí | Sí, lectura | No | No | No |
| Crear usuarios / gestionar roles | Sí | No | No | No | No |
| Editar clientes y proyectos | Sí | Sí | No | No | No |
| Editar tareas y solicitudes | Sí | Sí | Sí | No | No |
| Editar entregables | Sí | Sí | Sí | Sí | No |
| Panel global de reportes | Sí | Sí | Sí | Sí | No |

Además:

- Cliente no debe ver el módulo Clientes.
- Cliente solo debe ver proyectos y registros de su propia empresa.
- Ocultar un botón no es suficiente: al escribir una URL no autorizada también
  debe aparecer 403.
- Si aparece un botón que lleva a 403, anotarlo como bug de renderizado.

---

## Paso 5 — Pruebas automáticas obligatorias

Crear pruebas dentro de `tests/Feature/` que cubran como mínimo:

1. Cliente ve sus proyectos, pero no proyectos de otras empresas.
2. Cliente recibe 403 al abrir directamente un proyecto ajeno.
3. Programador recibe 403 en `/clientes/create` y PM puede entrar.
4. Un PATCH válido a `/tareas/mover` realizado por PM actualiza `estado` y
   `orden`.
5. Jefe ve el texto `Reportes generales` en el Dashboard y Cliente no lo ve.

Los tests deben usar:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;
```

Dentro de cada clase se pueden cargar los datos conocidos con:

```php
use RefreshDatabase;

protected function setUp(): void
{
    parent::setUp();
    $this->seed();
}
```

Para autenticar una cuenta sembrada:

```php
$usuario = \App\Models\User::where('email', 'cliente@example.com')->firstOrFail();
$this->actingAs($usuario);
```

Al final ejecutá:

```bash
php artisan test
```

Todas las pruebas relacionadas con el comportamiento actual deben quedar en
verde. Si `RegistrationTest` espera que `/register` exista, hay que actualizar
ese test: el registro público está desactivado a propósito y las cuentas las
crea el Jefe.

---

## Paso 6 — Registrar y corregir bugs

Por cada error encontrado, anoten:

```text
Usuario/rol usado:
URL o módulo:
Acción realizada:
Resultado esperado:
Resultado obtenido:
Mensaje de error:
```

Después de corregirlo, repetir exactamente los mismos pasos y marcarlo como
resuelto solamente si ya produce el resultado esperado. También ejecutar otra
vez `php artisan test` para comprobar que la corrección no rompió otra parte.

Checklist final de Testing:

- [ ] Login correcto e incorrecto probado.
- [ ] Creación de usuarios probada.
- [ ] Permisos por URL probados.
- [ ] Renderizado condicional comprobado con los cinco roles.
- [ ] Scoping del Cliente comprobado.
- [ ] Bugs encontrados, documentados y corregidos.
- [ ] Pruebas automáticas agregadas.
- [ ] `php artisan test` completamente en verde.
- [ ] Revisión cruzada realizada.

Para subir sobre la misma rama:

```bash
git add tests app resources routes
git commit -m "Agregar pruebas y corregir regresiones"
git push
```

---

# FASE 4 — Repaso final entre los tres

Esta fase se hace **juntos**, en una sola sesión. No hace falta rama por persona: hagan
una sola rama `fase4-final` y trabajen sobre esa.

### Paso 1 — Recorrer las 28 pantallas

Entre los tres, abran cada módulo y prueben las 4 pantallas. Son 7 módulos:

- [ ] Clientes
- [ ] Proyectos
- [ ] Tareas
- [ ] Hitos
- [ ] Solicitudes de Cambio
- [ ] Entregables
- [ ] Facturas

De cada uno: ver la lista, crear uno, editarlo, ver el detalle. **Anoten en un papel todo
lo que falle.**

### Paso 2 — Agregar el botón Eliminar

En **cada uno de los 7 archivos `index.blade.php`**, buscá esta línea:

```blade
<a href="{{ route('clientes.edit', $cliente) }}" class="text-yellow-600 hover:underline ml-3">Editar</a>
```

Y **justo debajo** pegá esto (cambiando `clientes` y `$cliente` por los de ese módulo):

```blade
<form method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="inline"
      onsubmit="return confirm('¿Seguro que querés eliminar este registro? No se puede deshacer.');">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-red-600 hover:underline ml-3">Eliminar</button>
</form>
```

> ⚠️ **Cuidado:** borrar un proyecto borra también todas sus tareas, hitos y facturas.
> Por eso el cartel de confirmación es obligatorio.

### Paso 3 — Probar con otro usuario

Cerrá sesión y entrá con `dev@example.com` / `1234`. Recorré el sistema y fijate que todo
siga funcionando.

### Paso 4 — Commit final

```bash
git add .
git commit -m "Fase 4: repaso final y botones de eliminar"
git push -u origin fase4-final
```

---

---

## Si algo sale mal — soluciones rápidas

| Lo que ves en pantalla                  | Qué hacer                                                                      |
| --------------------------------------- | ------------------------------------------------------------------------------- |
| `View [hitos.index] not found`        | El archivo no existe o le falta`.blade.php`. Revisá el nombre de la carpeta. |
| `Route [...] not defined`             | Copiaste mal el código. Borrá todo y pegá el bloque completo de nuevo.       |
| `Undefined variable`                  | Pegaste el código de otro módulo. Fijate que sea el tuyo.                     |
| Error rojo largo con`syntax error`    | Copiaste solo una parte. Borrá todo y pegá el bloque entero.                  |
| `SQLSTATE... Connection refused`      | Laragon apagado →**Start All**.                                          |
| La página se ve sin colores            | Falta`npm run dev` corriendo en una terminal.                                 |
| `419 Page Expired`                    | Refrescá con F5 y volvé a intentar.                                           |
| Guardaste y no cambia nada              | `php artisan optimize:clear` y refrescá.                                     |
| El menú no muestra los módulos nuevos | Jesús todavía no subió la Fase 3, o te falta`git pull`.                    |

**Si nada lo soluciona:** captura de pantalla completa al grupo. No borres archivos.
