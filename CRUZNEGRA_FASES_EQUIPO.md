# CRUZNEGRA — Guía para Marcos, Jesús y Dante

## Cómo se usa esta guía

Buscá tu nombre. Cada tarea te dice:

1. **Qué archivo crear** (o abrir)
2. **Todo el código que va adentro** — copialo entero y pegalo
3. **Cómo comprobar que funciona**

**El código está completo y probado. No hay que inventar ni completar nada.**
Copiás, pegás, guardás y funciona.

> **Importante:** cuando el código dice "copiá TODO esto", quiere decir el bloque entero,
> desde la primera línea hasta la última. No copies la mitad.

---

## Quién hace qué

| Fase | Marcos | Jesús | Dante |
|------|--------|-------|-------|
| **1** | Hitos | Proyectos | Tareas |
| **2** | Facturas | Solicitudes de Cambio | Entregables IA |
| **3** | Ayudar en el repaso | Menú de navegación | Dashboard |
| **4** | Repaso final entre los tres | | |

Cada uno hace **4 archivos** por fase. Los 4 archivos son siempre lo mismo:

- `index.blade.php` → la lista
- `create.blade.php` → el formulario para crear
- `edit.blade.php` → el formulario para editar
- `show.blade.php` → la ficha con el detalle

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

Usuario y contraseña para entrar:

| Email | Contraseña |
|-------|-----------|
| `jefe@example.com` | `1234` |

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

| Si tenés que hacer... | Abrí este archivo y miralo |
|-----------------------|----------------------------|
| Una lista con tabla | `clientes/index.blade.php` |
| Un formulario de crear | `clientes/create.blade.php` |
| Un formulario de editar | `clientes/edit.blade.php` |
| Una ficha de detalle | `clientes/show.blade.php` |

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

- [ ] Se ve la lista con el nombre del cliente y del PM en cada fila
- [ ] Apretás "+ Nuevo Proyecto", elegís cliente y PM de las listas, guardás → vuelve con
      cartel verde
- [ ] Apretás "Editar" → las fechas y los dos desplegables aparecen **ya seleccionados**
- [ ] Apretás "Ver" → se ve el detalle con la lista de tareas del proyecto

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

- [ ] Se ve la lista con el proyecto y el responsable de cada tarea
- [ ] Creás una tarea dejando "Solicitud de Cambio" en **— Ninguna —** → se guarda igual
- [ ] Apretás "Editar" → los desplegables aparecen ya seleccionados
- [ ] Apretás "Ver" → se ve el detalle completo

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

# FASE 2

**Antes de empezar**, cada uno crea su rama nueva:

```bash
git checkout main
git pull
git checkout -b fase2-marcos
```

Las instrucciones de la Fase 2 (Facturas, Solicitudes de Cambio y Entregables IA) están en
el archivo **CRUZNEGRA_FASES_2Y3.md**, con el código completo igual que acá.

---

## Si algo sale mal — soluciones rápidas

| Lo que ves en pantalla | Qué hacer |
|------------------------|-----------|
| `View [tareas.index] not found` | El archivo no existe o le falta `.blade.php` al nombre. Revisá que esté en la carpeta correcta. |
| `Route [tareas.create] not defined` | Copiaste mal el código. Volvé a pegarlo entero. |
| `Undefined variable` | Pegaste el código de otro módulo. Fijate que sea el tuyo. |
| Página en blanco o error rojo largo | Copiaste solo una parte del bloque. Borrá todo y pegá de nuevo el bloque completo. |
| `SQLSTATE... Connection refused` | Laragon está apagado → **Start All**. |
| La página se ve sin colores, todo blanco y feo | Falta `npm run dev` corriendo en una terminal. |
| `419 Page Expired` | Refrescá la página con F5 y volvé a intentar. |
| Guardaste el archivo y no cambia nada | Corré `php artisan optimize:clear` y refrescá. |

**Si nada de esto lo soluciona:** sacá captura de la pantalla completa del error y mandala
al grupo. No borres archivos.
