{{--
    Campos de Entregable IA, compartidos entre pantallas completas y modales
    del listado. $entregable es null al crear.
--}}
<div>
    <x-input-label for="titulo" value="Título" />
    <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full" :value="old('titulo', $entregable->titulo ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
</div>

<div>
    <x-input-label for="contenido" value="Contenido" />
    <textarea id="contenido" name="contenido" rows="6" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('contenido', $entregable->contenido ?? '') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('contenido')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="tipo" value="Tipo (ej: documento, informe, resumen)" />
        <x-text-input id="tipo" name="tipo" type="text" class="mt-1 block w-full" :value="old('tipo', $entregable->tipo ?? 'documento')" required />
        <x-input-error class="mt-2" :messages="$errors->get('tipo')" />
    </div>
    <div>
        <x-input-label for="estado" value="Estado" />
        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @foreach (['borrador' => 'Borrador', 'revisado' => 'Revisado', 'aprobado' => 'Aprobado'] as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(old('estado', $entregable->estado ?? 'borrador') == $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="proyecto_id" value="Proyecto" />
        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            <option value="">— Seleccioná un proyecto —</option>
            @foreach ($proyectos as $p)
                <option value="{{ $p->id }}" @selected(old('proyecto_id', $entregable->proyecto_id ?? '') == $p->id)>{{ $p->nombre }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
    </div>
    <div>
        <x-input-label for="generado_por" value="Generado por" />
        <select id="generado_por" name="generado_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            <option value="">— Seleccioná un usuario —</option>
            @foreach ($usuarios as $u)
                <option value="{{ $u->id }}" @selected(old('generado_por', $entregable->generado_por ?? '') == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('generado_por')" />
    </div>
</div>
