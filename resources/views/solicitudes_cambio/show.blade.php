<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de la Solicitud de Cambio
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Título:</strong> {{ $solicitud->titulo }}</p>
                <p><strong>Descripción:</strong> {{ $solicitud->descripcion }}</p>
                <p><strong>Proyecto:</strong> {{ $solicitud->proyecto?->nombre ?? 'N/A' }}</p>
                <p><strong>Solicitado por:</strong> {{ $solicitud->solicitante?->name ?? 'N/A' }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($solicitud->estado) }}</p>
                <p><strong>Prioridad:</strong> {{ ucfirst($solicitud->prioridad) }}</p>

                <div class="pt-4 border-t">
                    <p class="font-semibold mb-2">Tareas generadas por esta solicitud:</p>
                    <ul class="list-disc ms-5">
                        @forelse ($solicitud->tareas as $t)
                            <li>{{ $t->titulo }}</li>
                        @empty
                            <li class="list-none text-gray-500">Sin tareas asociadas.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="pt-4 flex gap-4 border-t">
                    @hasanyrole('Jefe|PM|PO')
                    <a href="{{ route('solicitudes-cambio.edit', $solicitud) }}" class="text-yellow-600 hover:underline">Editar</a>
                    @endhasanyrole
                    <a href="{{ route('solicitudes-cambio.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
