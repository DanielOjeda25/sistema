# CRUZNEGRA — Fases 2, 3 y 4

> **Antes de empezar la Fase 2**, la Fase 1 tiene que estar terminada y juntada en `main`.
> Si no leíste **CRUZNEGRA_FASES_EQUIPO.md**, empezá por ahí.

Funciona igual que la guía anterior: buscá tu nombre, copiá el código completo, pegalo,
guardá y comprobá con el checklist.

---

## Antes de arrancar (los tres)

```bash
git checkout main
git pull
php artisan migrate:fresh --seed
```

Después, cada uno crea su rama (cambiando el nombre):

```bash
git checkout -b fase2-marcos
```

---

# FASE 2

| Marcos | Jesús | Dante |
|--------|-------|-------|
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

- [ ] Se ve la lista con proyecto y solicitante
- [ ] Podés crear una solicitud nueva
- [ ] Al editar, los desplegables aparecen ya seleccionados
- [ ] El detalle muestra la lista de tareas asociadas

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

- [ ] Se ve la lista con proyecto y quién lo generó
- [ ] Podés crear un entregable nuevo
- [ ] Al editar, el contenido largo aparece completo en el cuadro de texto
- [ ] En el detalle, el contenido respeta los saltos de línea

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

| Marcos | Jesús | Dante |
|--------|-------|-------|
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

- [ ] Arriba de la pantalla aparecen los 7 módulos nuevos
- [ ] Hacés clic en cada uno y te lleva a su listado
- [ ] El que estás viendo aparece resaltado
- [ ] Achicás la ventana del navegador hasta que aparezca el menú de tres rayitas
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

- [ ] Entrás al Dashboard y ves 6 tarjetas con números
- [ ] Los números coinciden con lo que hay en cada listado
- [ ] Hacés clic en una tarjeta y te lleva a ese módulo
- [ ] Creás un cliente nuevo, volvés al Dashboard y el número de Clientes subió en 1

---

## Cierre de la Fase 3

Igual que siempre: commit, push, avisar, merge a `main`, y todos
`git pull` + `php artisan migrate:fresh --seed`.

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

## Si algo sale mal — soluciones rápidas

| Lo que ves en pantalla | Qué hacer |
|------------------------|-----------|
| `View [hitos.index] not found` | El archivo no existe o le falta `.blade.php`. Revisá el nombre de la carpeta. |
| `Route [...] not defined` | Copiaste mal el código. Borrá todo y pegá el bloque completo de nuevo. |
| `Undefined variable` | Pegaste el código de otro módulo. Fijate que sea el tuyo. |
| Error rojo largo con `syntax error` | Copiaste solo una parte. Borrá todo y pegá el bloque entero. |
| `SQLSTATE... Connection refused` | Laragon apagado → **Start All**. |
| La página se ve sin colores | Falta `npm run dev` corriendo en una terminal. |
| `419 Page Expired` | Refrescá con F5 y volvé a intentar. |
| Guardaste y no cambia nada | `php artisan optimize:clear` y refrescá. |
| El menú no muestra los módulos nuevos | Jesús todavía no subió la Fase 3, o te falta `git pull`. |

**Si nada lo soluciona:** captura de pantalla completa al grupo. No borres archivos.
