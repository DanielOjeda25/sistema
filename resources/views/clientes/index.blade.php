<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listado de Clientes
            </h2>
            @hasanyrole('Jefe|PM')
                <button type="button" data-abrir-modal="modal-cliente-crear"
                        class="inline-flex items-center px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63]">
                    + Nuevo Cliente
                </button>
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

                <form method="GET" action="{{ route('clientes.index') }}" class="mb-4 flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="q" class="block text-xs font-medium text-gray-500 uppercase mb-1">Buscar</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Nombre, email o empresa..."
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="estado" class="block text-xs font-medium text-gray-500 uppercase mb-1">Estado</label>
                        <select name="estado" id="estado" class="rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos</option>
                            @foreach (['activo', 'inactivo'] as $estado)
                                <option value="{{ $estado }}" @selected(request('estado') === $estado)>
                                    {{ ucfirst($estado) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63] inline-flex items-center gap-1.5">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                        Filtrar
                    </button>
                    @if (request()->hasAny(['q', 'estado']))
                        <a href="{{ route('clientes.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Limpiar</a>
                    @endif
                </form>

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
                                @php($valoresCliente = $cliente->only(['nombre', 'apellido', 'email', 'telefono', 'empresa', 'estado']))
                                <tr>
                                    <td class="px-6 py-4">{{ $cliente->nombre }} {{ $cliente->apellido }}</td>
                                    <td class="px-6 py-4">{{ $cliente->email }}</td>
                                    <td class="px-6 py-4">{{ $cliente->telefono ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $cliente->empresa ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ ucfirst($cliente->estado) }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('clientes.show', $cliente) }}" class="text-blue-600 hover:text-blue-800" title="Ver" aria-label="Ver">
                                                <x-heroicon-o-eye class="w-5 h-5" />
                                            </a>
                                            @hasanyrole('Jefe|PM')
                                                <button type="button" data-abrir-modal="modal-cliente-editar"
                                                        data-url="{{ route('clientes.update', $cliente) }}"
                                                        data-valores='@json($valoresCliente)'
                                                        class="text-yellow-600 hover:text-yellow-800" title="Editar" aria-label="Editar">
                                                    <x-heroicon-o-pencil-square class="w-5 h-5" />
                                                </button>
                                            @endhasanyrole
                                        </div>
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

    @hasanyrole('Jefe|PM')
        <x-crud-modal id="modal-cliente-crear" abrir-con-errores titulo="Nuevo Cliente">
            <form method="POST" action="{{ route('clientes.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="desde_modal" value="1">
                @include('clientes._campos', ['prefijo' => 'crear-'])
                <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                    <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                    <x-primary-button>Guardar Cliente</x-primary-button>
                </div>
            </form>
        </x-crud-modal>

        <x-crud-modal id="modal-cliente-editar" titulo="Editar Cliente">
            <form method="POST" action="{{ route('clientes.store') }}" class="space-y-4" data-crud-form>
                @csrf
                @method('PUT')
                <input type="hidden" name="desde_modal" value="1">
                @include('clientes._campos', ['prefijo' => 'editar-'])
                <div class="flex items-center justify-end space-x-4 pt-2 border-t border-gray-200">
                    <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                    <x-primary-button>Guardar Cambios</x-primary-button>
                </div>
            </form>
        </x-crud-modal>
    @endhasanyrole
</x-app-layout>
