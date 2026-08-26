<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Solicitudes de Cambio
            </h2>
            <a href="{{ route('solicitudes-cambio.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Nueva Solicitud
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                            @forelse ($solicitudes as $solicitud)
                                <tr>
                                    <td class="px-6 py-4">{{ $solicitud->titulo }}</td>
                                    <td class="px-6 py-4">{{ $solicitud->proyecto?->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $solicitud->solicitante?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($solicitud->estado) }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($solicitud->prioridad) }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('solicitudes-cambio.show', $solicitud) }}" class="text-blue-600 hover:underline">Ver</a>
                                        <a href="{{ route('solicitudes-cambio.edit', $solicitud) }}" class="text-yellow-600 hover:underline ml-3">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No hay solicitudes registradas aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $solicitudes->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>