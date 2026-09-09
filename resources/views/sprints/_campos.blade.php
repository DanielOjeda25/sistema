{{--
    Campos del formulario de Sprint, compartidos entre la pantalla completa
    (create/edit) y los modales del listado. $sprint es null al crear.
    En el modal de edición los valores llegan por JS (data-valores), así que
    acá solo importan las opciones de proyecto y el old() tras un error.
--}}
<div>
    <x-input-label for="nombre" value="Nombre del Sprint" />
    <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $sprint->nombre ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
</div>

<div>
    <x-input-label for="proyecto_id" value="Proyecto" />
    <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
        @foreach ($proyectos as $p)
            <option value="{{ $p->id }}" @selected(old('proyecto_id', $sprint->proyecto_id ?? '') == $p->id)>{{ $p->nombre }}</option>
        @endforeach
    </select>
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
