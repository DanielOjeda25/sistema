<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Hitos
            </h2>
            @hasanyrole('Jefe|PM|PO')
            <button type="button" data-abrir-modal="modal-hito-crear"
                    class="inline-flex items-center px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63]">
                + Nuevo Hito
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

                <form method="GET" action="{{ route('hitos.index') }}"
                      class="mb-4 rounded-xl border border-gray-100 bg-gray-50/60 p-4"
                      x-data="{ fechaObjetivo: '{{ request('fecha_objetivo') }}' }"
                      @change="if ($event.target.name === 'fecha_objetivo') fechaObjetivo = $event.target.value">
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4 xl:grid-cols-6">
                        <div class="col-span-2 md:col-span-2 xl:col-span-2">
                            <label for="q" class="block text-xs font-medium text-gray-500 uppercase mb-1">Buscar</label>
                            <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Nombre o descripción..."
                                   class="w-full rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d]">
                        </div>
                        <div>
                            <x-buscador-select name="proyecto_id" label="Proyecto" textoTodos="Todos"
                                               :opciones="$proyectos->mapWithKeys(fn ($p) => [$p->id => $p->nombre])->all()"
                                               :seleccionado="request('proyecto_id')"
                                               placeholder="Buscar proyecto..." />
                        </div>
                        <div>
                            <label for="estado" class="block text-xs font-medium text-gray-500 uppercase mb-1">Estado</label>
                            <x-buscador-select name="estado" textoTodos="Todos"
                                               :opciones="['completado' => 'Completado', 'pendiente' => 'Pendiente']"
                                               :seleccionado="request('estado')"
                                               placeholder="Estado..." />
                        </div>
                        <div>
                            <label for="fecha_objetivo" class="block text-xs font-medium text-gray-500 uppercase mb-1">Fecha objetivo</label>
                            <x-buscador-select name="fecha_objetivo" textoTodos="Todas"
                                               :opciones="['vencidos' => 'Vencidos', 'proximos_7_dias' => 'Próximos 7 días', 'rango' => 'Rango personalizado']"
                                               :seleccionado="request('fecha_objetivo')"
                                               placeholder="Fecha objetivo..." />
                        </div>
                        <div x-show="fechaObjetivo === 'rango'" x-cloak class="col-span-2 grid grid-cols-2 gap-3 md:col-span-2">
                            <div>
                                <label for="fecha_desde" class="block text-xs font-medium text-gray-500 uppercase mb-1">Desde</label>
                                <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}"
                                       min="{{ $limitesFecha->desde }}" max="{{ $limitesFecha->hasta }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d]">
                            </div>
                            <div>
                                <label for="fecha_hasta" class="block text-xs font-medium text-gray-500 uppercase mb-1">Hasta</label>
                                <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}"
                                       min="{{ $limitesFecha->desde }}" max="{{ $limitesFecha->hasta }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d]">
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-end gap-3 border-t border-gray-200/70 pt-3">
                        @if (request()->hasAny(['q', 'estado', 'proyecto_id', 'fecha_objetivo', 'fecha_desde', 'fecha_hasta']))
                            <a href="{{ route('hitos.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Limpiar</a>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha objetivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                            @forelse ($hitos as $hito)
                                @php($valoresHito = ['nombre' => $hito->nombre, 'descripcion' => $hito->descripcion, 'completado' => (string) $hito->completado, 'proyecto_id' => $hito->proyecto_id, 'fecha_objetivo' => $hito->fecha_objetivo?->format('Y-m-d')])
                                @php($verHitoFila = [
                                    'nombre' => $hito->nombre,
                                    'descripcion' => $hito->descripcion,
                                    'completado' => (bool) $hito->completado,
                                    'proyecto' => $hito->proyecto?->nombre ?? '—',
                                    'fecha' => $hito->fecha_objetivo?->format('d/m/Y'),
                                ])
                                <tr>
                                    <td class="px-6 py-4">{{ $hito->nombre }}</td>
                                    <td class="px-6 py-4">{{ $hito->proyecto?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $hito->fecha_objetivo?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td class="px-6 py-4"><x-estado-badge :estado="$hito->completado ? 'Completado' : 'Pendiente'" :crudo="false" /></td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <div class="flex items-center gap-3">
                                            @hasanyrole('Jefe')
                                                <a href="{{ route('auditoria.index', ['modelo' => 'App\Models\Hito', 'registro' => $hito->id]) }}"
                                                   class="text-gray-400 hover:text-gray-700" title="Historial de cambios" aria-label="Historial">
                                                    <x-heroicon-o-clock class="w-5 h-5" />
                                                </a>
                                            @endhasanyrole
                                            <button type="button" data-dispatch="ver-hito" data-valores='@json($verHitoFila)' class="text-blue-600 hover:text-blue-800" title="Ver" aria-label="Ver">
                                                <x-heroicon-o-eye class="w-5 h-5" />
                                            </button>
                                            <button type="button" data-abrir-modal="modal-hito-editar"
                                                    data-url="{{ route('hitos.update', $hito) }}"
                                                    data-valores='@json($valoresHito)'
                                                    class="text-yellow-600 hover:text-yellow-800" title="Editar" aria-label="Editar">
                                                <x-heroicon-o-pencil-square class="w-5 h-5" />
                                            </button>
                                            @hasrole('Jefe')
                                            <form method="POST" action="{{ route('hitos.destroy', $hito) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" data-confirmar="¿Querés eliminar este hito? Esta acción no se puede deshacer."
                                                        class="text-red-600 hover:text-red-800" title="Eliminar" aria-label="Eliminar">
                                                    <x-heroicon-o-trash class="w-5 h-5" />
                                                </button>
                                            </form>
                                            @endhasrole
                                        </div>
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

    @hasanyrole('Jefe|PM|PO')
    <x-crud-modal id="modal-hito-crear" abrir-con-errores titulo="Nuevo Hito">
        <form method="POST" action="{{ route('hitos.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="desde_modal" value="1">
            @include('hitos._campos', ['hito' => null])
            <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Hito</x-primary-button>
            </div>
        </form>
    </x-crud-modal>
    @endhasanyrole

    @hasanyrole('Jefe|PM|PO')
    @include('hitos._modal_editar')
    @endhasanyrole
    <x-hito-view-modal />
    <x-confirmar-eliminar />
</x-app-layout>
