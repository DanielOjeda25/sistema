<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle del Hito
            </h2>
            <a href="{{ route('hitos.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="w-4 h-4" /> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $hito->nombre }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $hito->proyecto?->nombre ?? 'Sin proyecto' }}</p>
                    </div>
                    <x-estado-badge :estado="$hito->completado ? 'Completado' : 'Pendiente'" :crudo="false" />
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 px-6 py-5 text-sm">
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase">Descripción</dt>
                        <dd class="mt-1 text-gray-900">{{ $hito->descripcion ?? 'Sin descripción' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Fecha objetivo</dt>
                        <dd class="mt-1 text-gray-900">{{ $hito->fecha_objetivo?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Estado</dt>
                        <dd class="mt-1">{{ $hito->completado ? 'Completado' : 'Pendiente' }}</dd>
                    </div>
                </dl>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center gap-3">
                    @hasanyrole('Jefe|PM|PO')
                        @php($valoresHito = ['nombre' => $hito->nombre, 'descripcion' => $hito->descripcion, 'completado' => (string) $hito->completado, 'proyecto_id' => $hito->proyecto_id, 'fecha_objetivo' => $hito->fecha_objetivo?->format('Y-m-d')])
                        <button type="button" data-abrir-modal="modal-hito-editar"
                                data-url="{{ route('hitos.update', $hito) }}"
                                data-valores='@json($valoresHito)'
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            <x-heroicon-o-pencil-square class="w-4 h-4" /> Editar
                        </button>
                    @endhasanyrole
                    <a href="{{ route('hitos.index') }}" class="ml-auto text-sm text-gray-600 hover:text-gray-900">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    @hasanyrole('Jefe|PM|PO')
        @include('hitos._modal_editar')
    @endhasanyrole
</x-app-layout>
