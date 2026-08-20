<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Entregable
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Título:</strong> {{ $entregable->titulo }}</p>
                <p><strong>Proyecto:</strong> {{ $entregable->proyecto?->nombre ?? 'N/A' }}</p>
                <p><strong>Generado por:</strong> {{ $entregable->generador?->name ?? 'N/A' }}</p>
                <p><strong>Tipo:</strong> {{ ucfirst($entregable->tipo) }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($entregable->estado) }}</p>

                <div class="pt-4 border-t">
                    <p class="font-semibold mb-2">Contenido:</p>
                    <div class="whitespace-pre-wrap bg-gray-50 p-4 rounded">{{ $entregable->contenido }}</div>
                </div>

                <div class="pt-4 flex gap-4 border-t">
                    <a href="{{ route('entregables.edit', $entregable) }}" class="text-yellow-600 hover:underline">Editar</a>
                    <a href="{{ route('entregables.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
