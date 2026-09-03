<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Cliente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Nombre:</strong> {{ $cliente->nombre }} {{ $cliente->apellido }}</p>
                <p><strong>Email:</strong> {{ $cliente->email }}</p>
                <p><strong>Teléfono:</strong> {{ $cliente->telefono ?? 'N/A' }}</p>
                <p><strong>Empresa:</strong> {{ $cliente->empresa ?? 'N/A' }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($cliente->estado) }}</p>

                <div class="pt-4 flex gap-4 border-t">
                    @hasanyrole('Jefe|PM')
                    <a href="{{ route('clientes.edit', $cliente) }}" class="text-yellow-600 hover:underline">Editar</a>
                    @endhasanyrole
                    <a href="{{ route('clientes.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
