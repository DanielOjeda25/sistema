<x-app-layout>
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="GET" action="{{ route('proyectos.index') }}" class="mb-5 rounded-xl border border-gray-100 bg-gray-50/60 p-4">
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
                        <div class="col-span-2 md:col-span-3 xl:col-span-2">
                            <label for="q" class="block text-xs font-medium text-gray-500 uppercase mb-1">Buscar</label>
                            <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Nombre o descripción..."
                                   class="w-full rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d]">
                        </div>
                        <div>
                            <label for="estado" class="block text-xs font-medium text-gray-500 uppercase mb-1">Estado</label>
                            <select name="estado" id="estado" class="w-full rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d]">
                                <option value="">Todos</option>
                                @foreach (['pendiente', 'en_progreso', 'completado', 'cancelado'] as $estado)
                                    <option value="{{ $estado }}" @selected(request('estado') === $estado)>
                                        {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-buscador-select name="cliente_id" label="Cliente" textoTodos="Todos"
                                               :opciones="$clientes->mapWithKeys(fn ($c) => [$c->id => trim($c->nombre.' '.$c->apellido)])->all()"
                                               :seleccionado="request('cliente_id')" placeholder="Buscar cliente..." />
                        </div>
                        <div>
                            <x-buscador-select name="pm_id" label="PM" textoTodos="Todos"
                                               :opciones="$pms->mapWithKeys(fn ($p) => [$p->id => trim($p->name.' '.$p->apellido)])->all()"
                                               :seleccionado="request('pm_id')" placeholder="Buscar PM..." />
                        </div>
                        <div>
                            <label for="fecha_desde" class="block text-xs font-medium text-gray-500 uppercase mb-1">Inicio desde</label>
                            <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d]">
                        </div>
                        <div>
                            <label for="fecha_hasta" class="block text-xs font-medium text-gray-500 uppercase mb-1">Inicio hasta</label>
                            <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d]">
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-3 border-t border-gray-200/70 pt-3">
                        @if (request()->hasAny(['q', 'estado', 'cliente_id', 'pm_id', 'fecha_desde', 'fecha_hasta']))
                            <a href="{{ route('proyectos.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Limpiar filtros</a>
                        @endif
                        <button type="submit" class="px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63] inline-flex items-center gap-1.5">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                            Filtrar
                        </button>
                    </div>
                </form>

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
                                @php($valoresProyecto = $proyecto->only(['nombre', 'descripcion', 'estado', 'cliente_id', 'pm_id']) + ['fecha_inicio' => $proyecto->fecha_inicio?->format('Y-m-d'), 'fecha_fin_estimada' => $proyecto->fecha_fin_estimada?->format('Y-m-d')])
                                <tr>
                                    <td class="px-6 py-4">{{ $proyecto->nombre }}</td>
                                    <td class="px-6 py-4">{{ $proyecto->cliente?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $proyecto->pm?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4"><x-estado-badge :estado="$proyecto->estado" /></td>
                                    <td class="px-6 py-4">{{ $proyecto->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <div class="flex items-center gap-3">
                                            @hasanyrole('Jefe')
                                                <a href="{{ route('auditoria.index', ['modelo' => 'App\Models\Proyecto', 'registro' => $proyecto->id]) }}"
                                                   class="text-gray-400 hover:text-gray-700" title="Historial de cambios" aria-label="Historial">
                                                    <x-heroicon-o-clock class="w-5 h-5" />
                                                </a>
                                            @endhasanyrole
                                            <button type="button" data-panel="{{ route('proyectos.show', $proyecto) }}" class="text-blue-600 hover:text-blue-800" title="Ver" aria-label="Ver">
                                                    <x-heroicon-o-eye class="w-5 h-5" />
                                                </button>
                                            <a href="{{ route('tareas.tablero', ['proyecto' => $proyecto->id]) }}" class="text-indigo-600 hover:text-indigo-800" title="Ver tablero de tareas" aria-label="Ver tablero de tareas">
                                                <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                                            </a>
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

                <div class="mt-4">
                    {{ $proyectos->links() }}
                </div>

            </div>
        </div>
    </div>

    @hasanyrole('Jefe|PM')
    <x-crud-modal id="modal-proyecto-crear" abrir-con-errores titulo="Nuevo Proyecto">
        <form method="POST" action="{{ route('proyectos.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="desde_modal" value="1">
            @include('proyectos._campos', ['proyectoItem' => null])
            <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Proyecto</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
    @endhasanyrole

    @hasanyrole('Jefe|PM')
    @include('proyectos._modal_editar')
    @endhasanyrole
    <x-confirmar-eliminar />
    <x-panel-modal />
</x-app-layout>