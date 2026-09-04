<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sprints
            </h2>
            @hasanyrole('Jefe|PM|PO')
                <button type="button" data-abrir-modal="modal-sprint-crear"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    + Nuevo Sprint
                </button>
            @endhasanyrole
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <form method="GET" action="{{ route('sprints.index') }}" class="mb-4 p-4 flex flex-wrap gap-3 items-end">
                    <div>
                        <x-input-label for="q" value="Buscar" />
                        <x-text-input id="q" name="q" type="text" class="mt-1 block w-64" :value="request('q')" placeholder="Sprint o proyecto…" />
                    </div>
                    <div>
                        <x-input-label for="proyecto" value="Proyecto" />
                        <select id="proyecto" name="proyecto" class="mt-1 block w-56 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">— Todos —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(request('proyecto') == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button>Filtrar</x-primary-button>
                    <a href="{{ route('sprints.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Limpiar</a>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Sprint</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Proyecto</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Inicio</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Fin</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Tareas</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($sprints as $sprint)
                                @php($valoresSprint = ['nombre' => $sprint->nombre, 'proyecto_id' => $sprint->proyecto_id, 'fecha_inicio' => $sprint->fecha_inicio?->format('Y-m-d'), 'fecha_fin' => $sprint->fecha_fin?->format('Y-m-d')])
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ $sprint->nombre }}</td>
                                    <td class="px-6 py-4">{{ $sprint->proyecto?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $sprint->fecha_inicio?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-6 py-4">{{ $sprint->fecha_fin?->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-6 py-4">{{ $sprint->tareas_count }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('tareas.tablero', ['proyecto' => $sprint->proyecto_id, 'sprint' => $sprint->id]) }}"
                                               class="text-indigo-600 hover:text-indigo-800" title="Ver en el tablero" aria-label="Ver en el tablero">
                                                <x-heroicon-o-squares-2x2 class="w-5 h-5" />
                                            </a>
                                            @hasanyrole('Jefe|PM|PO')
                                                <button type="button" data-abrir-modal="modal-sprint-editar"
                                                        data-url="{{ route('sprints.update', $sprint) }}"
                                                        data-valores='@json($valoresSprint)'
                                                        class="text-yellow-600 hover:text-yellow-800" title="Editar" aria-label="Editar">
                                                    <x-heroicon-o-pencil-square class="w-5 h-5" />
                                                </button>
                                                <form method="POST" action="{{ route('sprints.destroy', $sprint) }}"
                                                      class="inline" data-confirmar-eliminar
                                                      data-mensaje="¿Eliminar el sprint “{{ $sprint->nombre }}”? Sus tareas quedan sin sprint, no se borran.">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Eliminar" aria-label="Eliminar">
                                                        <x-heroicon-o-trash class="w-5 h-5" />
                                                    </button>
                                                </form>
                                            @endhasanyrole
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No hay sprints registrados aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $sprints->links() }}
                </div>
            </div>
        </div>
    </div>

    <x-crud-modal id="modal-sprint-crear" abrir-con-errores titulo="Nuevo Sprint">
        <form method="POST" action="{{ route('sprints.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="desde_modal" value="1">
            @include('sprints._campos', ['sprint' => null])
            <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Sprint</x-primary-button>
            </div>
        </form>
    </x-crud-modal>

    <x-crud-modal id="modal-sprint-editar" titulo="Editar Sprint">
        <form method="POST" action="{{ route('sprints.store') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="desde_modal" value="1">
            @include('sprints._campos', ['sprint' => null])
            <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                <x-primary-button>Guardar Cambios</x-primary-button>
            </div>
        </form>
    </x-crud-modal>

    {{-- Confirmación de eliminación: modal propio en vez del confirm() nativo --}}
    <div id="modal-eliminar-sprint" class="hidden fixed inset-0 overflow-y-auto" style="z-index: 9999" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/50" style="z-index: -1" data-cancelar></div>

        <div class="min-h-full flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                <h3 class="font-semibold text-lg text-gray-800">Confirmar eliminación</h3>
                <p id="mensaje-eliminar-sprint" class="mt-1 text-sm text-gray-600"></p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" data-cancelar
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="button" id="btn-eliminar-sprint"
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @once
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('modal-eliminar-sprint');
                const mensaje = document.getElementById('mensaje-eliminar-sprint');
                let formularioPendiente = null;

                document.querySelectorAll('form[data-confirmar-eliminar]').forEach(form => {
                    form.addEventListener('submit', evento => {
                        evento.preventDefault();
                        formularioPendiente = form;
                        mensaje.textContent = form.dataset.mensaje;
                        modal.classList.remove('hidden');
                    });
                });

                const cerrar = () => {
                    modal.classList.add('hidden');
                    formularioPendiente = null;
                };

                modal.querySelectorAll('[data-cancelar]').forEach(el => el.addEventListener('click', cerrar));
                document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrar(); });
                document.getElementById('btn-eliminar-sprint').addEventListener('click', () => {
                    formularioPendiente?.submit();
                    cerrar();
                });
            });
        </script>
    @endonce
</x-app-layout>
