<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Cliente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('clientes.update', $cliente) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $cliente->nombre)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div>
                        <x-input-label for="apellido" value="Apellido" />
                        <x-text-input id="apellido" name="apellido" type="text" class="mt-1 block w-full" :value="old('apellido', $cliente->apellido)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('apellido')" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Correo Electrónico" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $cliente->email)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <x-input-label for="telefono" value="Teléfono (opcional)" />
                        <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono', $cliente->telefono)" />
                        <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
                    </div>

                    <div>
                        <x-input-label for="empresa" value="Empresa (opcional)" />
                        <x-text-input id="empresa" name="empresa" type="text" class="mt-1 block w-full" :value="old('empresa', $cliente->empresa)" />
                        <x-input-error class="mt-2" :messages="$errors->get('empresa')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="activo" @selected(old('estado', $cliente->estado) == 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado', $cliente->estado) == 'inactivo')>Inactivo</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('clientes.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Actualizar Cliente</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
