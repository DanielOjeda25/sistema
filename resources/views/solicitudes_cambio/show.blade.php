<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle de la Solicitud de Cambio
            </h2>
            <a href="{{ route('solicitudes-cambio.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="w-4 h-4" /> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $solicitud->titulo }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $solicitud->proyecto?->nombre ?? 'Sin proyecto' }} &middot; Solicitada por {{ $solicitud->solicitante?->name ?? '-' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-estado-badge :estado="$solicitud->estado" />
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ ['baja' => 'bg-gray-100 text-gray-700', 'media' => 'bg-yellow-100 text-yellow-800', 'alta' => 'bg-red-100 text-red-800'][$solicitud->prioridad] ?? 'bg-gray-100 text-gray-700' }}">
                            Prioridad {{ ucfirst($solicitud->prioridad) }}
                        </span>
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 px-6 py-5 text-sm">
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase">Descripcion</dt>
                        <dd class="mt-1 text-gray-900">{{ $solicitud->descripcion }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Estado</dt>
                        <dd class="mt-1">{{ ucfirst($solicitud->estado) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Prioridad</dt>
                        <dd class="mt-1">{{ ucfirst($solicitud->prioridad) }}</dd>
                    </div>
                </dl>

                <div class="px-6 pb-5">
                    <p class="text-xs font-medium text-gray-500 uppercase mb-2">Tareas generadas por esta solicitud</p>
                    <ul class="bg-gray-50 border border-gray-100 rounded-lg p-4 space-y-1.5 text-sm text-gray-800">
                        @forelse ($solicitud->tareas as $t)
                            <li class="flex items-center gap-2">
                                <x-heroicon-o-check-circle class="w-4 h-4 text-[#00b87d] shrink-0" />
                                {{ $t->titulo }}
                            </li>
                        @empty
                            <li class="text-gray-500">Sin tareas asociadas.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center gap-3">
                    @hasanyrole('Jefe|PM|PO')
                        @php($valoresSolicitud = $solicitud->only(['titulo', 'descripcion', 'estado', 'prioridad', 'proyecto_id', 'solicitado_por']))
                        <button type="button" data-abrir-modal="modal-solicitud-editar"
                                data-url="{{ route('solicitudes-cambio.update', $solicitud) }}"
                                data-valores='@json($valoresSolicitud)'
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            <x-heroicon-o-pencil-square class="w-4 h-4" /> Editar
                        </button>
                    @endhasanyrole
                    <a href="{{ route('solicitudes-cambio.index') }}" class="ml-auto text-sm text-gray-600 hover:text-gray-900">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    @hasanyrole('Jefe|PM|PO')
        @include('solicitudes_cambio._modal_editar')
    @endhasanyrole
</x-app-layout>
