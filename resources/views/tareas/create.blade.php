<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nueva Tarea
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('tareas.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="titulo" value="Título" />
                        <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción (opcional)" />
                        <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado') == 'pendiente')>Pendiente</option>
                            <option value="en_progreso" @selected(old('estado') == 'en_progreso')>En progreso</option>
                            <option value="completada" @selected(old('estado') == 'completada')>Completada</option>
                            <option value="cancelada" @selected(old('estado') == 'cancelada')>Cancelada</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="prioridad" value="Prioridad" />
                        <select id="prioridad" name="prioridad" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="baja" @selected(old('prioridad') == 'baja')>Baja</option>
                            <option value="media" @selected(old('prioridad') == 'media')>Media</option>
                            <option value="alta" @selected(old('prioridad') == 'alta')>Alta</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('prioridad')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_limite" value="Fecha Límite (opcional)" />
                        <x-text-input id="fecha_limite" name="fecha_limite" type="date" class="mt-1 block w-full" :value="old('fecha_limite')" />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_limite')" />
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

                    <div>
                        <x-input-label for="asignado_a" value="Asignar a" />
                        <select id="asignado_a" name="asignado_a" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un responsable —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('asignado_a') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('asignado_a')" />
                    </div>

                    <div>
                        <x-input-label for="solicitud_cambio_id" value="Solicitud de Cambio (opcional)" />
                        <select id="solicitud_cambio_id" name="solicitud_cambio_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">— Ninguna —</option>
                            @foreach ($solicitudes as $s)
                                <option value="{{ $s->id }}" @selected(old('solicitud_cambio_id') == $s->id)>{{ $s->titulo }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('solicitud_cambio_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('tareas.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar Tarea</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>