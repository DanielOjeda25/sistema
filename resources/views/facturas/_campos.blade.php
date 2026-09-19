{{--
    Campos de Factura, compartidos por los modales del listado. $factura es
    null al crear. Pensado para que cargar una factura tome lo menos posible:
    la fecha de emision arranca en hoy, "Emitida por" arranca en el usuario
    logueado y el vencimiento no admite fechas anteriores a la emision.
--}}
<div>
    <div class="flex items-baseline justify-between gap-2">
        <x-input-label for="numero" value="Número de factura" />
        <span class="text-[11px] text-gray-400">Único — ej: F-2026-0001</span>
    </div>
    <x-text-input maxlength="255" id="numero" name="numero" type="text" placeholder="F-2026-0001" class="mt-1 block w-full" :value="old('numero', $factura->numero ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('numero')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <div class="flex items-baseline justify-between gap-2">
            <x-input-label for="monto" value="Monto" />
            <span class="text-[11px] text-gray-400">hasta $10.000.000</span>
        </div>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">$</span>
            <x-text-input id="monto" name="monto" type="number" min="0" max="10000000" step="0.01" placeholder="0,00" class="mt-1 block w-full pl-7" :value="old('monto', $factura->monto ?? '')" required />
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('monto')" />
    </div>
    <div>
        <x-input-label for="estado" value="Estado" />
        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            @foreach (['pendiente' => 'Pendiente', 'pagada' => 'Pagada', 'vencida' => 'Vencida'] as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(old('estado', $factura->estado ?? 'pendiente') == $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="fecha_emision" value="Fecha de emisión" />
        <x-text-input id="fecha_emision" name="fecha_emision" type="date" autocomplete="off"
            :value="old('fecha_emision', $factura?->fecha_emision?->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_emision')" />
    </div>
    <div>
        <x-input-label for="fecha_vencimiento" value="Vencimiento (opcional)" />
        <x-text-input id="fecha_vencimiento" name="fecha_vencimiento" type="date" autocomplete="off"
            :value="old('fecha_vencimiento', $factura?->fecha_vencimiento?->format('Y-m-d'))"
            x-data x-effect="if (! $el.min) $el.min = document.getElementById('fecha_emision')?.value"
            @change="fecha_emision && ($el.min = fecha_emision)" />
        <x-input-error class="mt-2" :messages="$errors->get('fecha_vencimiento')" />
    </div>
</div>

<div>
    <x-input-label for="detalle" value="Detalle (opcional)" />
    <textarea id="detalle" name="detalle" rows="2" placeholder="Ej: pago único — anticipo del 50%" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm">{{ old('detalle', $factura->detalle ?? '') }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('detalle')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="proyecto_id" value="Proyecto" />
        <select id="proyecto_id" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            <option value="">— Seleccioná un proyecto —</option>
            @foreach ($proyectos as $p)
                <option value="{{ $p->id }}" @selected(old('proyecto_id', $factura->proyecto_id ?? '') == $p->id)>{{ $p->nombre }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
    </div>
    <div>
        <x-input-label for="emitida_por" value="Emitida por" />
        <select id="emitida_por" name="emitida_por" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            <option value="">— Seleccioná un usuario —</option>
            @foreach ($usuarios as $u)
                <option value="{{ $u->id }}" @selected(old('emitida_por', $factura->emitida_por ?? auth()->id()) == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('emitida_por')" />
    </div>
</div>
