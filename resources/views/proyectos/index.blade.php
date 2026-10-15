<x-app-layout>
    {{-- Encabezado del módulo; el alta solo está disponible para Jefe y PM. --}}
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Proyectos
            </h2>
            @hasanyrole('Jefe|PM')
                <button type="button" data-abrir-modal="modal-proyecto-crear" class="inline-flex items-center px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63]">
                + Nuevo Proyecto
            </button>
            @endhasanyrole
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[1600px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Confirmación mostrada después de crear, editar o eliminar. --}}
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Buscador y filtros combinables del listado interno. --}}
                @php
                    $filtrosActivos = collect(['q', 'estado', 'cliente_id', 'pm_id', 'fecha_desde', 'fecha_hasta'])
                        ->filter(fn ($filtro) => filled(request($filtro)))
                        ->count();
                @endphp
                <form method="GET" action="{{ route('proyectos.index') }}" class="mb-5 overflow-hidden rounded-xl border border-gray-200 bg-gray-50/60">
                    <div class="flex flex-col gap-2 border-b border-gray-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-funnel class="h-5 w-5 text-[#00a873]" />
                                <h3 class="font-semibold text-gray-800">Buscar y filtrar proyectos</h3>
                                @if ($filtrosActivos)
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700">
                                        {{ $filtrosActivos }} {{ $filtrosActivos === 1 ? 'activo' : 'activos' }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Podés combinar varios campos para precisar el resultado.</p>
                        </div>
                        @if ($filtrosActivos)
                            <a href="{{ route('proyectos.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-red-600">
                                <x-heroicon-o-x-mark class="h-4 w-4" />
                                Limpiar filtros
                            </a>
                        @endif
                    </div>

                    <div class="space-y-4 p-5">
                        <div class="grid gap-4 lg:grid-cols-12">
                            <div class="lg:col-span-5">
                                <label for="q" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Nombre o descripción</label>
                                <div class="relative">
                                    <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                                    <input type="search" name="q" id="q" value="{{ request('q') }}" placeholder="Ej.: Portal del socio"
                                           class="w-full rounded-lg border-gray-300 pl-10 focus:border-[#00b87d] focus:ring-[#00b87d]">
                                </div>
                            </div>
                            <div class="lg:col-span-2">
                                <label for="estado" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500">Estado</label>
                                <x-buscador-select name="estado" textoTodos="Todos los estados"
                                                   :opciones="['pendiente' => 'Pendiente', 'en_progreso' => 'En progreso', 'completado' => 'Completado', 'cancelado' => 'Cancelado']"
                                                   :seleccionado="request('estado')" placeholder="Elegir estado..." />
                            </div>
                            <div class="lg:col-span-3">
                                <x-buscador-select name="cliente_id" label="Cliente" textoTodos="Todos los clientes"
                                                   :opciones="$clientes->mapWithKeys(fn ($c) => [$c->id => trim($c->nombre.' '.$c->apellido)])->all()"
                                                   :seleccionado="request('cliente_id')" placeholder="Buscar por cliente..." />
                            </div>
                            <div class="lg:col-span-2">
                                <x-buscador-select name="pm_id" label="Responsable (PM)" textoTodos="Todos los PM"
                                                   :opciones="$pms->mapWithKeys(fn ($p) => [$p->id => trim($p->name.' '.$p->apellido)])->all()"
                                                   :seleccionado="request('pm_id')" placeholder="Buscar PM..." />
                            </div>
                        </div>

                        <div class="flex flex-col gap-4 border-t border-gray-200 pt-4 xl:flex-row xl:items-end xl:justify-between">
                            <fieldset>
                                <legend class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Fecha de inicio</legend>
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <div>
                                        <label for="fecha_desde" class="mb-1 block text-xs text-gray-500">Desde</label>
                                        <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}"
                                               min="{{ $limitesFecha->desde }}" max="{{ $limitesFecha->hasta }}"
                                               class="w-full rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] sm:w-48">
                                    </div>
                                    <span class="mt-5 hidden text-gray-400 sm:block">—</span>
                                    <div>
                                        <label for="fecha_hasta" class="mb-1 block text-xs text-gray-500">Hasta</label>
                                        <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}"
                                               min="{{ $limitesFecha->desde }}" max="{{ $limitesFecha->hasta }}"
                                               class="w-full rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] sm:w-48">
                                    </div>
                                </div>
                            </fieldset>

                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-transparent bg-[#00b87d] px-5 py-2.5 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition hover:bg-[#008c63] focus:outline-none focus:ring-2 focus:ring-[#00b87d] focus:ring-offset-2 sm:w-auto">
                                <x-heroicon-o-funnel class="h-4 w-4" />
                                Aplicar filtros
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Tabla paginada de proyectos y acciones permitidas por rol. --}}
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
                                {{-- Datos serializados que consumen los modales de edición y vista rápida. --}}
                                @php($valoresProyecto = $proyecto->only(['nombre', 'descripcion', 'estado', 'cliente_id', 'pm_id']) + ['fecha_inicio' => $proyecto->fecha_inicio?->format('Y-m-d'), 'fecha_fin_estimada' => $proyecto->fecha_fin_estimada?->format('Y-m-d')])
                                @php($valoresVerProyecto = [
                                    'nombre' => $proyecto->nombre,
                                    'cliente' => $proyecto->cliente?->empresa ?: trim(($proyecto->cliente?->nombre ?? '').' '.($proyecto->cliente?->apellido ?? '')) ?: 'Sin cliente',
                                    'estado' => ucfirst(str_replace('_', ' ', $proyecto->estado)),
                                    'avance' => $proyecto->tareas->count() > 0 ? (int) round($proyecto->tareas->where('estado', 'completada')->count() * 100 / $proyecto->tareas->count()) : 0,
                                    'pm' => $proyecto->pm?->name ?? 'Sin asignar',
                                    'fechas' => trim(($proyecto->fecha_inicio?->format('d/m/Y') ?? 'Sin inicio').' — '.($proyecto->fecha_fin_estimada?->format('d/m/Y') ?? 'sin fecha estimada')),
                                    'tareas' => $proyecto->tareas->where('estado', 'completada')->count().' de '.$proyecto->tareas->count().' listas',
                                    'detalle' => route('proyectos.show', $proyecto),
                                ])
                                <tr>
                                    <td class="px-6 py-4">{{ $proyecto->nombre }}</td>
                                    <td class="px-6 py-4">{{ $proyecto->cliente?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $proyecto->pm?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4"><x-estado-badge :estado="$proyecto->estado" /></td>
                                    <td class="px-6 py-4">{{ $proyecto->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            {{-- El historial y la eliminación son exclusivos del Jefe. --}}
                                            @hasanyrole('Jefe')
                                                <a href="{{ route('auditoria.index', ['modelo' => 'App\Models\Proyecto', 'registro' => $proyecto->id]) }}"
                                                   class="text-gray-400 hover:text-gray-700" title="Historial de cambios" aria-label="Historial">
                                                    <x-heroicon-o-clock class="w-5 h-5" />
                                                </a>
                                            @endhasanyrole
                                            <button type="button" data-dispatch="ver-proyecto" data-valores='@json($valoresVerProyecto)' class="text-blue-600 hover:text-blue-800" title="Ver" aria-label="Ver">
                                                    <x-heroicon-o-eye class="w-5 h-5" />
                                                </button>
                                            <a href="{{ route('tareas.tablero', ['proyecto' => $proyecto->id]) }}" class="text-indigo-600 hover:text-indigo-800" title="Ver tablero de tareas" aria-label="Ver tablero de tareas">
                                                <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                                            </a>
                                            {{-- Jefe y PM pueden abrir el formulario de edición. --}}
                                            @hasanyrole('Jefe|PM')
                                                <button type="button" data-abrir-modal="modal-proyecto-editar"
                                                        data-url="{{ route('proyectos.update', $proyecto) }}"
                                                        data-valores='@json($valoresProyecto)'
                                                        class="text-yellow-600 hover:text-yellow-800" title="Editar" aria-label="Editar">
                                                    <x-heroicon-o-pencil-square class="w-5 h-5" />
                                                </button>
                                            
                                                @hasrole('Jefe')
                                                <form method="POST" action="{{ route('proyectos.destroy', $proyecto) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" data-confirmar="¿Querés eliminar este proyecto? Sus tareas y facturas asociadas quedan sin dueño. Esta acción no se puede deshacer."
                                                            class="text-red-600 hover:text-red-800" title="Eliminar" aria-label="Eliminar">
                                                        <x-heroicon-o-trash class="w-5 h-5" />
                                                    </button>
                                                </form>
                                                @endhasrole
                                                @endhasanyrole
                                        </div>
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

                {{-- Conserva los filtros actuales al navegar entre páginas. --}}
                <div class="mt-4">
                    {{ $proyectos->links() }}
                </div>

            </div>
        </div>
    </div>

    {{-- Modal de creación reutilizando los campos compartidos del proyecto. --}}
    @hasanyrole('Jefe|PM')
    <x-crud-modal id="modal-proyecto-crear" abrir-con-errores titulo="Nuevo Proyecto">
        <form method="POST" action="{{ route('proyectos.store') }}" class="flex flex-col gap-4" data-proyecto-form="crear" novalidate>
            @csrf
            <input type="hidden" name="desde_modal" value="1">
            @include('proyectos._campos', ['proyectoItem' => null, 'esCreacion' => true])
            <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Proyecto</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
    @endhasanyrole

    {{-- Modales compartidos para editar, consultar y confirmar eliminaciones. --}}
    @hasanyrole('Jefe|PM')
    @include('proyectos._modal_editar')
    @endhasanyrole
    <x-proyecto-view-modal />
    <x-confirmar-eliminar />
    <x-panel-modal />
</x-app-layout>
