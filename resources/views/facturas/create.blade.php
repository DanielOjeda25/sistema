<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nueva Factura
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('facturas.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="numero" value="Número de Factura (no se puede repetir)" />
                        <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full" :value="old('numero')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('numero')" />
                    </div>

                    <div>
                        <x-input-label for="monto" value="Monto" />
                        <x-text-input id="monto" name="monto" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('monto')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('monto')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_emision" value="Fecha de Emisión" />
                        <x-text-input id="fecha_emision" name="fecha_emision" type="date" class="mt-1 block w-full" :value="old('fecha_emision')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_emision')" />
                    </div>

                    <div>
                        <x-input-label for="fecha_vencimiento" value="Fecha de Vencimiento (opcional)" />
                        <x-text-input id="fecha_vencimiento" name="fecha_vencimiento" type="date" class="mt-1 block w-full" :value="old('fecha_vencimiento')" />
                        <x-input-error class="mt-2" :messages="$errors->get('fecha_vencimiento')" />
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="pendiente" @selected(old('estado') == 'pendiente')>Pendiente</option>
                            <option value="pagada" @selected(old('estado') == 'pagada')>Pagada</option>
                            <option value="vencida" @selected(old('estado') == 'vencida')>Vencida</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div>
                        <x-input-label for="detalle" value="Detalle (opcional)" />
                        <textarea id="detalle" name="detalle" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('detalle') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('detalle')" />
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
                        <x-input-label for="emitida_por" value="Emitida por" />
                        <select id="emitida_por" name="emitida_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un usuario —</option>
                            @foreach ($usuarios as $u)
                                <option value="{{ $u->id }}" @selected(old('emitida_por') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('emitida_por')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4">
                        <a href="{{ route('facturas.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <x-primary-button>Guardar Factura</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
