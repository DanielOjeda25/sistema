<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Sprint
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('sprints.update', $sprint) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nombre" value="Nombre del Sprint" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $sprint->nombre)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id', $sprint->proyecto_id) == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="fecha_inicio" value="Fecha de inicio (opcional)" />
                            <x-text-input id="fecha_inicio" name="fecha_inicio" type="date" class="mt-1 block w-full" :value="old('fecha_inicio', $sprint->fecha_inicio?->format('Y-m-d'))" />
                            <x-input-error class="mt-2" :messages="$errors->get('fecha_inicio')" />
                        </div>
                        <div>
                            <x-input-label for="fecha_fin" value="Fecha de fin (opcional)" />
                            <x-text-input id="fecha_fin" name="fecha_fin" type="date" class="mt-1 block w-full" :value="old('fecha_fin', $sprint->fecha_fin?->format('Y-m-d'))" />
                            <x-input-error class="mt-2" :messages="$errors->get('fecha_fin')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('sprints.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar Cambios</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
