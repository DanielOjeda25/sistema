<x-app-layout>
<x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nuevo Hito
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('hitos.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="nombre" value="Nombre del Hito" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción (opcional)" />
                        <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_objetivo" value="Fecha Objetivo" />
                        <x-text-input id="fecha_objetivo" name="fecha_objetivo" type="date" class="mt-1 block w-full" :value="old('fecha_objetivo')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_objetivo')" />
                    </div>

                    <div>
                        <x-input-label for="completado" value="Estado" />
                        <select id="completado" name="completado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="0" @selected(old('completado') == '0')>Pendiente</option>
                            <option value="1" @selected(old('completado') == '1')>Completado</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('completado')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id') == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('hitos.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar Hito</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
