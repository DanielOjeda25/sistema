{{--
    Campos de Solicitud de Cambio, compartidos entre pantallas completas y
    modales del listado. $solicitud es null al crear.
--}}
<div>
    <x-input-label for="titulo" value="Título" />
    <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo', $solicitud->titulo ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
</div>

<div>
    <x-input-label for="descripcion" value="Descripción" />
    <textarea id="descripcion" name="descripcion" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('descripcion', $solicitud->descripcion ?? '') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="estado" value="Estado" />
        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @foreach (['pendiente' => 'Pendiente', 'aprobada' => 'Aprobada', 'rechazada' => 'Rechazada'] as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(old('estado', $solicitud->estado ?? 'pendiente') == $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
    </div>
    <div>
        <x-input-label for="prioridad" value="Prioridad" />
        <select id="prioridad" name="prioridad" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @foreach (['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta'] as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(old('prioridad', $solicitud->prioridad ?? 'media') == $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('prioridad')" />
    </div>
</div>

<div>
    <x-input-label for="proyecto_id" value="Proyecto" />
    <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">— Seleccioná un proyecto —</option>
        @foreach ($proyectos as $p)
            <option value="{{ $p->id }}" @selected(old('proyecto_id', $solicitud->proyecto_id ?? '') == $p->id)>{{ $p->nombre }}</option>
        @endforeach
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
</div>

<div>
    <x-input-label for="solicitado_por" value="Solicitado por" />
    <select id="solicitado_por" name="solicitado_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">— Seleccioná un usuario —</option>
        @foreach ($usuarios as $u)
            <option value="{{ $u->id }}" @selected(old('solicitado_por', $solicitud->solicitado_por ?? '') == $u->id)>{{ $u->name }}</option>
        @endforeach
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('solicitado_por')" />
</div>
