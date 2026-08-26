<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nuevo Proyecto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('proyectos.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="nombre" value="Nombre del Proyecto" />
                        <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" value="Descripción (opcional)" />
                        <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_inicio" value="Fecha de Inicio" />
                        <x-text-input id="fecha_inicio" name="fecha_inicio" type="date" class="mt-1 block w-full" :value="old('fecha_inicio')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_inicio')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_fin_estimada" value="Fecha de Fin Estimada (opcional)" />
                        <x-text-input id="fecha_fin_estimada" name="fecha_fin_estimada" type="date" class="mt-1 block w-full" :value="old('fecha_fin_estimada')" />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_fin_estimada')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado') == 'pendiente')>Pendiente</option>
                            <option value="en_progreso" @selected(old('estado') == 'en_progreso')>En progreso</option>
                            <option value="completado" @selected(old('estado') == 'completado')>Completado</option>
                            <option value="cancelado" @selected(old('estado') == 'cancelado')>Cancelado</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="cliente_id" value="Cliente" />
                        <select id="cliente_id" name="cliente_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un cliente —</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c->id }}" @selected(old('cliente_id') == $c->id)>{{ $c->nombre }} {{ $c->apellido }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('cliente_id')" />
                    </div>

                    <div>
                        <x-input-label for="pm_id" value="Project Manager" />
                        <select id="pm_id" name="pm_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un PM —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('pm_id') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('pm_id')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('proyectos.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar Proyecto</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>