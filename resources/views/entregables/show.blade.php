<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle del Entregable
            </h2>
            <a href="{{ route('entregables.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="w-4 h-4" /> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $entregable->titulo }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $entregable->proyecto?->nombre ?? 'Sin proyecto' }} · {{ ucfirst($entregable->tipo) }}</p>
                    </div>
                    <x-estado-badge :estado="$entregable->estado" />
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 px-6 py-5 text-sm">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Generado por</dt>
                        <dd class="mt-1 text-gray-900">{{ $entregable->generador?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Estado</dt>
                        <dd class="mt-1">{{ ucfirst($entregable->estado) }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase">Contenido</dt>
                        <dd class="mt-1 whitespace-pre-wrap bg-gray-50 border border-gray-100 p-4 rounded-lg text-gray-800 leading-relaxed">{{ $entregable->contenido }}</dd>
                    </div>
                </dl>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center gap-3">
                    @hasanyrole('Jefe|PM|PO|Programador')
                        @php($valoresEntregable = $entregable->only(['titulo', 'contenido', 'tipo', 'estado', 'proyecto_id', 'generado_por']))
                        <button type="button" data-abrir-modal="modal-entregable-editar"
                                data-url="{{ route('entregables.update', $entregable) }}"
                                data-valores='@json($valoresEntregable)'
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            <x-heroicon-o-pencil-square class="w-4 h-4" /> Editar
                        </button>
                    @endhasanyrole
                    <a href="{{ route('entregables.index') }}" class="ml-auto text-sm text-gray-600 hover:text-gray-900">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    @hasanyrole('Jefe|PM|PO|Programador')
        @include('entregables._modal_editar')
    @endhasanyrole
</x-app-layout>
