<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Solicitud de Cambio
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('solicitudes-cambio.update', $solicitud) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="titulo" value="Título" />
                        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo', $solicitud->titulo)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción" />
                        <textarea id="descripcion" name="descripcion" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('descripcion', $solicitud->descripcion) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado', $solicitud->estado) == 'pendiente')>Pendiente</option>
                            <option value="aprobada" @selected(old('estado', $solicitud->estado) == 'aprobada')>Aprobada</option>
                            <option value="rechazada" @selected(old('estado', $solicitud->estado) == 'rechazada')>Rechazada</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="prioridad" value="Prioridad" />
                        <select id="prioridad" name="prioridad" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="baja" @selected(old('prioridad', $solicitud->prioridad) == 'baja')>Baja</option>
                            <option value="media" @selected(old('prioridad', $solicitud->prioridad) == 'media')>Media</option>
                            <option value="alta" @selected(old('prioridad', $solicitud->prioridad) == 'alta')>Alta</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('prioridad')" />
                    </div>

                    <div>
                        <x-input-label for="proyecto_id" value="Proyecto" />
                        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un proyecto —</option>
                            @foreach ($proyectos as $p)
                                <option value="{{ $p->id }}" @selected(old('proyecto_id', $solicitud->proyecto_id) == $p->id)>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
                    </div>

                    <div>
                        <x-input-label for="solicitado_por" value="Solicitado por" />
                        <select id="solicitado_por" name="solicitado_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un usuario —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('solicitado_por', $solicitud->solicitado_por) == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('solicitado_por')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('solicitudes-cambio.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Actualizar Solicitud</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>