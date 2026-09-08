{{--
    Campos de Factura, compartidos entre pantallas completas y modales del
    listado. $factura es null al crear.
--}}
<div>
    <x-input-label for="numero" value="Número de Factura (no se puede repetir)" />
    <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full" :value="old('numero', $factura->numero ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('numero')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="monto" value="Monto (máximo $10.000.000)" />
        <x-text-input id="monto" name="monto" type="number" step="0.01" min="0" max="10000000" class="mt-1 block w-full" :value="old('monto', $factura->monto ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('monto')" />
    </div>
    <div>
        <x-input-label for="estado" value="Estado" />
        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @foreach (['pendiente' => 'Pendiente', 'pagada' => 'Pagada', 'vencida' => 'Vencida'] as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(old('estado', $factura->estado ?? 'pendiente') == $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="fecha_emision" value="Fecha de Emisión (DD/MM/AAAA)" />
        <x-text-input id="fecha_emision" name="fecha_emision" type="date" lang="es-AR" autocomplete="off" class="mt-1 block w-full" :value="old('fecha_emision', $factura?->fecha_emision?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_emision')" />
    </div>
    <div>
        <x-input-label for="fecha_vencimiento" value="Fecha de Vencimiento (DD/MM/AAAA, opcional)" />
        <x-text-input id="fecha_vencimiento" name="fecha_vencimiento" type="date" lang="es-AR" autocomplete="off" class="mt-1 block w-full" :value="old('fecha_vencimiento', $factura?->fecha_vencimiento?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_vencimiento')" />
    </div>
</div>

<div>
    <x-input-label for="detalle" value="Detalle (opcional)" />
    <textarea id="detalle" name="detalle" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('detalle', $factura->detalle ?? '') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('detalle')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="proyecto_id" value="Proyecto" />
        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            <option value="">— Seleccioná un proyecto —</option>
            @foreach ($proyectos as $p)
                <option value="{{ $p->id }}" @selected(old('proyecto_id', $factura->proyecto_id ?? '') == $p->id)>{{ $p->nombre }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
    </div>
    <div>
        <x-input-label for="emitida_por" value="Emitida por" />
        <select id="emitida_por" name="emitida_por" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            <option value="">— Seleccioná un usuario —</option>
            @foreach ($usuarios as $u)
                <option value="{{ $u->id }}" @selected(old('emitida_por', $factura->emitida_por ?? '') == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('emitida_por')" />
    </div>
</div>
