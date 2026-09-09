{{--
    Campos de Proyecto, compartidos entre pantallas completas y modales del
    listado. $proyectoItem es null al crear (se evita $proyecto para no
    chocar con la variable del loop del listado).
--}}
<div>
    <x-input-label for="nombre" value="Nombre del Proyecto" />
    <x-text-input id="nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $proyectoItem->nombre ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
</div>

<div>
    <x-input-label for="descripcion" value="Descripción (opcional)" />
    <textarea id="descripcion" name="descripcion" rows="3" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm">{{ old('descripcion', $proyectoItem->descripcion ?? '') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="fecha_inicio" value="Fecha de Inicio" />
        <x-text-input id="fecha_inicio" name="fecha_inicio" type="date" class="mt-1 block w-full" :value="old('fecha_inicio', $proyectoItem?->fecha_inicio?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_inicio')" />
    </div>
    <div>
        <x-input-label for="fecha_fin_estimada" value="Fecha de Fin Estimada (opcional)" />
        <x-text-input id="fecha_fin_estimada" name="fecha_fin_estimada" type="date" class="mt-1 block w-full" :value="old('fecha_fin_estimada', $proyectoItem?->fecha_fin_estimada?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_fin_estimada')" />
    </div>
</div>

<div>
    <x-input-label for="estado" value="Estado" />
    <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
        @foreach (['pendiente' => 'Pendiente', 'en_progreso' => 'En progreso', 'completado' => 'Completado', 'cancelado' => 'Cancelado'] as $valor => $etiqueta)
            <option value="{{ $valor }}" @selected(old('estado', $proyectoItem->estado ?? 'pendiente') == $valor)>{{ $etiqueta }}</option>
        @endforeach
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('estado')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="cliente_id" value="Cliente" />
        <select id="cliente_id" name="cliente_id" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            <option value="">— Seleccioná un cliente —</option>
            @foreach ($clientes as $c)
                <option value="{{ $c->id }}" @selected(old('cliente_id', $proyectoItem->cliente_id ?? '') == $c->id)>{{ $c->nombre }} {{ $c->apellido }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('cliente_id')" />
    </div>
    <div>
        <x-input-label for="pm_id" value="Project Manager" />
        <select id="pm_id" name="pm_id" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            <option value="">— Seleccioná un PM —</option>
            @foreach ($usuarios as $u)
                <option value="{{ $u->id }}" @selected(old('pm_id', $proyectoItem->pm_id ?? '') == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('pm_id')" />
    </div>
</div>
