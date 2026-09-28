{{--
    Campos del formulario de Sprint, compartidos entre la pantalla completa
    (create/edit) y los modales del listado. $sprint es null al crear.
    En el modal de edición los valores llegan por JS (data-valores), así que
    acá solo importan las opciones de proyecto y el old() tras un error.
--}}
<div>
    <x-input-label for="nombre" value="Nombre del Sprint" />
    <x-text-input maxlength="255" id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $sprint->nombre ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
</div>

<div>
    <x-input-label for="proyecto_id" value="Proyecto" />
    <x-buscador-select name="proyecto_id" label=""
                       :opciones="$proyectos->mapWithKeys(fn ($p) => [$p->id => $p->nombre])->all()"
                       :seleccionado="old('proyecto_id', $sprint->proyecto_id ?? '')"
                       placeholder="Buscar proyecto..." />
    <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="fecha_inicio" value="Fecha de inicio (opcional)" />
        <x-text-input id="fecha_inicio" name="fecha_inicio" type="date" class="mt-1 block w-full" :value="old('fecha_inicio', $sprint?->fecha_inicio?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_inicio')" />
    </div>
    <div>
        <x-input-label for="fecha_fin" value="Fecha de fin (opcional)" />
        <x-text-input id="fecha_fin" name="fecha_fin" type="date" class="mt-1 block w-full" :value="old('fecha_fin', $sprint?->fecha_fin?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_fin')" />
    </div>
</div>
