{{--
    Campos del formulario de Hito, compartidos entre la pantalla completa
    (create/edit) y los modales del listado. $hito es null al crear.
    En el modal de edición los valores llegan por JS (data-valores).
--}}
<div>
    <x-input-label for="nombre" value="Nombre del Hito" />
    <x-text-input maxlength="255" id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $hito->nombre ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
</div>

<div>
    <x-input-label for="descripcion" value="Descripción (opcional)" />
    <textarea id="descripcion" name="descripcion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm">{{ old('descripcion', $hito->descripcion ?? '') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="fecha_objetivo" value="Fecha Objetivo" />
        <x-text-input id="fecha_objetivo" name="fecha_objetivo" type="date" class="mt-1 block w-full" :value="old('fecha_objetivo', $hito?->fecha_objetivo?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_objetivo')" />
    </div>
    <div>
        <x-input-label for="completado" value="Estado" />
        <select id="completado" name="completado" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            <option value="0" @selected(old('completado', $hito->completado ?? '0') == '0')>Pendiente</option>
            <option value="1" @selected(old('completado', $hito->completado ?? '0') == '1')>Completado</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('completado')" />
    </div>
</div>

<div>
    <x-input-label for="proyecto_id" value="Proyecto" />
    <x-buscador-select name="proyecto_id"
                       :opciones="$proyectos->mapWithKeys(fn ($p) => [$p->id => $p->nombre])->all()"
                       :seleccionado="old('proyecto_id', $hito->proyecto_id ?? '')"
                       placeholder="Buscar proyecto..." textoTodos="Todos" />
    <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
</div>
