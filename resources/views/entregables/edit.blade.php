<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Entregable
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('entregables.update', $entregable) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="titulo" value="Título" />
                        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo', $entregable->titulo)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
                    </div>

                    <div>
                        <x-input-label for="contenido" value="Contenido" />
                        <textarea id="contenido" name="contenido" rows="8" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('contenido', $entregable->contenido) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('contenido')" />
                    </div>

                    <div>
                        <x-input-label for="tipo" value="Tipo" />
                        <x-text-input id="tipo" name="tipo" type="text" class="mt-1 block w-full" :value="old('tipo', $entregable->tipo)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('tipo')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="borrador" @selected(old('estado', $entregable->estado) == 'borrador')>Borrador</option>
                            <option value="revisado" @selected(old('estado', $entregable->estado) == 'revisado')>Revisado</option>
                            <option value="aprobado" @selected(old('estado', $entregable->estado) == 'aprobado')>Aprobado</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id', $entregable->proyecto_id) == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div>
                        <x-input-label for="generado_por" value="Generado por" />
                        <select id="generado_por" name="generado_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un usuario —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('generado_por', $entregable->generado_por) == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('generado_por')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('entregables.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Actualizar Entregable</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
