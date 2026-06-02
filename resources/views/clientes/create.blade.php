<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Nuevo Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form method="POST" action="{{ route('clientes.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="nombre" :value="__('Nombre o Razón Social')" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Correo Electrónico')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <x-input-label for="telefono" :value="__('Teléfono (Opcional)')" />
                        <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono')" />
                        <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('clientes.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                            Cancelar
                        </a>
                        <x-primary-button>
                            {{ __('Guardar Cliente') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>