<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Clientes
            </h2>
            @hasanyrole('Jefe|PM')
                <a href="{{ route('clientes.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                + Nuevo Cliente
            </a>
            @endhasanyrole
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-gray-700">
                            @forelse ($clientes as $cliente)
                                <tr>
                                    <td class="px-6 py-4">{{ $cliente->nombre }} {{ $cliente->apellido }}</td>
                                    <td class="px-6 py-4">{{ $cliente->email }}</td>
                                    <td class="px-6 py-4">{{ $cliente->telefono ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $cliente->empresa ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($cliente->estado) }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('clientes.show', $cliente) }}" class="text-blue-600 hover:underline">Ver</a>
                                        @hasanyrole('Jefe|PM')
                                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-yellow-600 hover:underline ml-3">Editar</a>
                                        @endhasanyrole
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No hay clientes registrados aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $clientes->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
