<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de la Tarea
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Título:</strong> {{ $tarea->titulo }}</p>
                <p><strong>Descripción:</strong> {{ $tarea->descripcion ?? 'Sin descripción' }}</p>
                <p><strong>Proyecto:</strong> {{ $tarea->proyecto?->nombre ?? 'N/A' }}</p>
                <p><strong>Asignado a:</strong> {{ $tarea->asignado?->name ?? 'N/A' }}</p>
                <p><strong>Estado:</strong> {{ ucfirst(str_replace('_', ' ', $tarea->estado)) }}</p>
                <p><strong>Prioridad:</strong> {{ ucfirst($tarea->prioridad) }}</p>
                <p><strong>Fecha límite:</strong> {{ $tarea->fecha_limite?->format('d/m/Y') ?? 'Sin fecha' }}</p>
                <p><strong>Solicitud de cambio:</strong> {{ $tarea->solicitudCambio?->titulo ?? 'Ninguna' }}</p>

                <div class="pt-4 flex gap-4 border-t">
                    <a href="{{ route('tareas.edit', $tarea) }}" class="text-yellow-600 hover:underline">Editar</a>
                    <a href="{{ route('tareas.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

