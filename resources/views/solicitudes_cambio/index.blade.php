<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Solicitudes de Cambio
            </h2>
            @hasanyrole('Jefe|PM|PO')
                <button type="button" data-abrir-modal="modal-solicitud-crear"
                        class="inline-flex items-center px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63]">
                + Nueva Solicitud
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

                <form method="GET" action="{{ route('solicitudes-cambio.index') }}" class="mb-4 rounded-xl border border-gray-100 bg-gray-50/60 p-4">
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
                        <div class="col-span-2 md:col-span-3 xl:col-span-2">
                            <label for="q" class="block text-xs font-medium text-gray-500 uppercase mb-1">Buscar</label>
                            <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Título o descripción..."
                                   class="w-full rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d]">
                        </div>
                        <div>
                            <label for="estado" class="block text-xs font-medium text-gray-500 uppercase mb-1">Estado</label>
                            <x-buscador-select name="estado" textoTodos="Todos"
                                               :opciones="['pendiente' => 'Pendiente', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada']"
                                               :seleccionado="request('estado')"
                                               placeholder="Estado..." />
                        </div>
                        <div>
                            <label for="prioridad" class="block text-xs font-medium text-gray-500 uppercase mb-1">Prioridad</label>
                            <x-buscador-select name="prioridad" textoTodos="Todas"
                                               :opciones="['alta' => 'Alta', 'media' => 'Media', 'baja' => 'Baja']"
                                               :seleccionado="request('prioridad')"
                                               placeholder="Prioridad..." />
                        </div>
                        <div>
                            <x-buscador-select name="proyecto_id" label="Proyecto" textoTodos="Todos"
                                               :opciones="$proyectos->mapWithKeys(fn ($p) => [$p->id => $p->nombre])->all()"
                                               :seleccionado="request('proyecto_id')"
                                               placeholder="Buscar proyecto..." />
                        </div>
                        <div>
                            <x-buscador-select name="solicitado_por" label="Solicitante" textoTodos="Todos"
                                               :opciones="$usuarios->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()"
                                               :seleccionado="request('solicitado_por')"
                                               placeholder="Buscar solicitante..." />
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-3 border-t border-gray-200/70 pt-3">
                        @if (request()->hasAny(['q', 'estado', 'prioridad', 'proyecto_id', 'solicitado_por']))
                            <a href="{{ route('solicitudes-cambio.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Limpiar</a>
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
                                @php($valoresSolicitud = $solicitud->only(['titulo', 'descripcion', 'estado', 'prioridad', 'proyecto_id', 'solicitado_por']))
                                @php($valoresVerSolicitud = [
                                    'id' => $solicitud->id,
                                    'puede_editar' => auth()->user()->hasAnyRole('Jefe', 'PM', 'PO'),
                                    'titulo' => $solicitud->titulo,
                                    'descripcion' => $solicitud->descripcion,
                                    'estado' => ucfirst($solicitud->estado),
                                    'estado_color' => match ($solicitud->estado) {
                                        'pendiente' => 'bg-amber-100 text-amber-800',
                                        'aprobada' => 'bg-emerald-100 text-emerald-800',
                                        'rechazada' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-700',
                                    },
                                    'prioridad' => ucfirst($solicitud->prioridad),
                                    'prioridad_color' => match ($solicitud->prioridad) {
                                        'alta' => 'bg-red-100 text-red-800',
                                        'media' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-gray-100 text-gray-700',
                                    },
                                    'proyecto' => $solicitud->proyecto?->nombre ?? 'Sin proyecto',
                                    'solicitante' => $solicitud->solicitante?->name ?? '—',
                                ])
                                <tr>
                                    <td class="px-6 py-4">{{ $solicitud->titulo }}</td>
                                    <td class="px-6 py-4">{{ $solicitud->proyecto?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $solicitud->solicitante?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4"><x-estado-badge :estado="$solicitud->estado" /></td>
                                    <td class="px-6 py-4">{{ ucfirst($solicitud->prioridad) }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <div class="flex items-center gap-3">
                                            @hasanyrole('Jefe')
                                                <a href="{{ route('auditoria.index', ['modelo' => 'App\Models\SolicitudCambio', 'registro' => $solicitud->id]) }}"
                                                   class="text-gray-400 hover:text-gray-700" title="Historial de cambios" aria-label="Historial">
                                                    <x-heroicon-o-clock class="w-5 h-5" />
                                                </a>
                                            @endhasanyrole
                                            <button type="button" data-dispatch="ver-solicitud" data-valores='@json($valoresVerSolicitud)' class="text-blue-600 hover:text-blue-800" title="Ver" aria-label="Ver">
                                                <x-heroicon-o-eye class="w-5 h-5" />
                                            </button>
                                            @hasanyrole('Jefe|PM|PO')
                                                {{-- id conocido por el boton "Editar" del modal de lectura --}}
                                                <button type="button" id="editar-solicitud-{{ $solicitud->id }}"
                                                        data-abrir-modal="modal-solicitud-editar"
                                                        data-url="{{ route('solicitudes-cambio.update', $solicitud) }}"
                                                        data-valores='@json($valoresSolicitud)'
                                                        class="text-yellow-600 hover:text-yellow-800" title="Editar" aria-label="Editar">
                                                    <x-heroicon-o-pencil-square class="w-5 h-5" />
                                                </button>
                                            
                                                @hasrole('Jefe')
                                                <form method="POST" action="{{ route('solicitudes-cambio.destroy', $solicitud) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" data-confirmar="¿Querés eliminar esta solicitud de cambio? Esta acción no se puede deshacer."
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

    @hasanyrole('Jefe|PM|PO')
    <x-crud-modal id="modal-solicitud-crear" abrir-con-errores titulo="Nueva Solicitud de Cambio" ancho="max-w-3xl">
        <form method="POST" action="{{ route('solicitudes-cambio.store') }}" class="flex flex-col gap-4">
            @csrf
            <input type="hidden" name="desde_modal" value="1">
            @include('solicitudes_cambio._campos', ['solicitud' => null])
            <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Solicitud</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
    @endhasanyrole

    @hasanyrole('Jefe|PM|PO')
    @include('solicitudes_cambio._modal_editar')
    @endhasanyrole
    <x-solicitud-view-modal />
    <x-confirmar-eliminar />
</x-app-layout>