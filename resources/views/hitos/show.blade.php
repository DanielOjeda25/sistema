<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Hito
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Nombre:</strong> {{ $hito->nombre }}</p>
                <p><strong>Descripción:</strong> {{ $hito->descripcion ?? 'Sin descripción' }}</p>
                <p><strong>Proyecto:</strong> {{ $hito->proyecto?->nombre ?? 'N/A' }}</p>
                <p><strong>Fecha objetivo:</strong> {{ $hito->fecha_objetivo?->format('d/m/Y') ?? 'N/A' }}</p>
                <p><strong>Estado:</strong> {{ $hito->completado ? 'Completado' : 'Pendiente' }}</p>

                <div class="pt-4 flex gap-4 border-t">
                    @php($valoresHito = ['nombre' => $hito->nombre, 'descripcion' => $hito->descripcion, 'completado' => (string) $hito->completado, 'proyecto_id' => $hito->proyecto_id, 'fecha_objetivo' => $hito->fecha_objetivo?->format('Y-m-d')])
                    <button type="button" data-abrir-modal="modal-hito-editar"
                            data-url="{{ route('hitos.update', $hito) }}"
                            data-valores='@json($valoresHito)'
                            class="text-yellow-600 hover:text-yellow-800 inline-flex" title="Editar" aria-label="Editar">
                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                    </button>
                    <a href="{{ route('hitos.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
    @hasanyrole('Jefe|PM|PO')
    @include('hitos._modal_editar')
    @endhasanyrole
</x-app-layout>
