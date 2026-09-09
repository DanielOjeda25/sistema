<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Tareas
            </h2>
            @hasanyrole('Jefe|PM|PO')
                <button type="button" data-abrir-modal="modal-tarea-crear" class="inline-flex items-center px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63]">
                + Nueva Tarea
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

                <form method="GET" action="{{ route('tareas.index') }}" class="mb-4 flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="q" class="block text-xs font-medium text-gray-500 uppercase mb-1">Buscar</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Título o descripción..."
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="estado" class="block text-xs font-medium text-gray-500 uppercase mb-1">Estado</label>
                        <select name="estado" id="estado" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos</option>
                            @foreach (['pendiente', 'en_progreso', 'completada', 'cancelada'] as $estado)
                                <option value="{{ $estado }}" @selected(request('estado') === $estado)>
                                    {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63] inline-flex items-center gap-1.5">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                        Filtrar
                    </button>
                    @if (request()->hasAny(['q', 'estado']))
                        <a href="{{ route('tareas.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Limpiar</a>
                    @endif
                </form>

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
                                @php($valoresTarea = $tarea->only(['titulo', 'descripcion', 'estado', 'prioridad', 'proyecto_id', 'sprint_id', 'asignado_a', 'solicitud_cambio_id']) + ['fecha_limite' => $tarea->fecha_limite?->format('Y-m-d')])
                                <tr>
                                    <td class="px-6 py-4">{{ $tarea->titulo }}</td>
                                    <td class="px-6 py-4">{{ $tarea->proyecto?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $tarea->asignado?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ ucfirst(str_replace('_', ' ', $tarea->estado)) }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($tarea->prioridad) }}</td>
                                    <td class="px-6 py-4">{{ $tarea->fecha_limite?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('tareas.show', $tarea) }}" class="text-blue-600 hover:text-blue-800" title="Ver" aria-label="Ver">
                                                <x-heroicon-o-eye class="w-5 h-5" />
                                            </a>
                                            @hasanyrole('Jefe|PM|PO')
                                                <button type="button" data-abrir-modal="modal-tarea-editar" data-url="{{ route('tareas.update', $tarea) }}" data-valores='@json($valoresTarea)' class="text-yellow-600 hover:text-yellow-800" title="Editar" aria-label="Editar">
                                                    <x-heroicon-o-pencil-square class="w-5 h-5" />
                                                </button>
                                            @endhasanyrole
                                        </div>
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
    <x-crud-modal id="modal-tarea-crear" abrir-con-errores titulo="Nueva Tarea">
        <form method="POST" action="{{ route('tareas.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="desde_modal" value="1">
            @include('tareas._campos', ['tarea' => null])
            <div class="flex items-center justify-end gap-4 border-t border-gray-200 pt-3">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Tarea</x-primary-button>
            </div>
        </form>
    </x-crud-modal>

    <x-crud-modal id="modal-tarea-editar" titulo="Editar Tarea">
        <form method="POST" action="{{ route('tareas.store') }}" class="space-y-4" data-crud-form>
            @csrf
            @method('PUT')
            <input type="hidden" name="desde_modal" value="1">
            @include('tareas._campos', ['tarea' => null])
            <div class="flex items-center justify-end gap-4 border-t border-gray-200 pt-3">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Cambios</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
</x-app-layout>