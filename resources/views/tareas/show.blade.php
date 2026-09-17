{{--
    Detalle de una Tarea. Solo lectura: la edicion se hace desde el mismo
    modal del listado (x-crud-modal), que recibe los valores via data-valores.
--}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle de la Tarea
            </h2>
            <a href="{{ route('tareas.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="w-4 h-4" /> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $tarea->titulo }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $tarea->proyecto?->nombre ?? 'Sin proyecto' }}@if($tarea->sprint) &middot; Sprint: {{ $tarea->sprint?->nombre }}@endif</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-estado-badge :estado="$tarea->estado" />
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ ['baja' => 'bg-gray-100 text-gray-700', 'media' => 'bg-yellow-100 text-yellow-800', 'alta' => 'bg-red-100 text-red-800'][$tarea->prioridad] ?? 'bg-gray-100 text-gray-700' }}">
                            Prioridad {{ ucfirst($tarea->prioridad) }}
                        </span>
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 px-6 py-5 text-sm">
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase">Descripcion</dt>
                        <dd class="mt-1 text-gray-900">{{ $tarea->descripcion ?? 'Sin descripcion' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Asignado a</dt>
                        <dd class="mt-1 text-gray-900">{{ $tarea->asignado?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Fecha limite</dt>
                        <dd class="mt-1 text-gray-900">{{ $tarea->fecha_limite?->format('d/m/Y') ?? 'Sin fecha' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Estado</dt>
                        <dd class="mt-1">{{ ucfirst(str_replace('_', ' ', $tarea->estado)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Solicitud de cambio</dt>
                        <dd class="mt-1 text-gray-900">{{ $tarea->solicitudCambio?->titulo ?? 'Ninguna' }}</dd>
                    </div>
                </dl>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center gap-3">
                    @hasanyrole('Jefe|PM|PO')
                        @php($valoresTarea = $tarea->only(['titulo', 'descripcion', 'estado', 'prioridad', 'proyecto_id', 'sprint_id', 'asignado_a', 'solicitud_cambio_id']) + ['fecha_limite' => $tarea->fecha_limite?->format('Y-m-d')])
                        <button type="button" data-abrir-modal="modal-tarea-editar"
                                data-url="{{ route('tareas.update', $tarea) }}"
                                data-valores='@json($valoresTarea)'
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            <x-heroicon-o-pencil-square class="w-4 h-4" /> Editar
                        </button>
                    @endhasanyrole
                    <a href="{{ route('tareas.index') }}" class="ml-auto text-sm text-gray-600 hover:text-gray-900">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    @hasanyrole('Jefe|PM|PO')
        @include('tareas._modal_editar')
    @endhasanyrole
</x-app-layout>
