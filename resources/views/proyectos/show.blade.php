<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Proyecto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Nombre:</strong> {{ $proyecto->nombre }}</p>
                <p><strong>Descripción:</strong> {{ $proyecto->descripcion ?? 'Sin descripción' }}</p>
                <p><strong>Cliente:</strong> {{ $proyecto->cliente?->nombre ?? 'N/A' }} {{ $proyecto->cliente?->apellido }}</p>
                <p><strong>Project Manager:</strong> {{ $proyecto->pm?->name ?? 'N/A' }}</p>
                <p><strong>Estado:</strong> {{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}</p>
                <p><strong>Fecha de inicio:</strong> {{ $proyecto->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</p>
                <p><strong>Fecha de fin estimada:</strong> {{ $proyecto->fecha_fin_estimada?->format('d/m/Y') ?? 'N/A' }}</p>

                <div class="pt-4 border-t">
                    <p class="font-semibold mb-2">Tareas de este proyecto:</p>
                    <ul class="list-disc ms-5">
                        @forelse ($proyecto->tareas as $t)
                            <li>{{ $t->titulo }}</li>
                        @empty
                            <li class="list-none text-gray-500">Sin tareas todavía.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="pt-4 flex gap-4 border-t">
                    <a href="{{ route('proyectos.edit', $proyecto) }}" class="text-yellow-600 hover:underline">Editar</a>
                    <a href="{{ route('proyectos.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>