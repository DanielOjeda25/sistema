{{--
    Campos de Factura, compartidos por los modales del listado. $factura es
    null al crear. Pensado para que cargar una factura tome lo menos posible:
    la fecha de emision arranca en hoy, "Emitida por" arranca en el usuario
    logueado y el vencimiento no admite fechas anteriores a la emision.
--}}
    <div>
        <div class="flex h-5 items-center justify-between gap-2">
            <x-input-label for="numero" value="Número de factura" />
            <span class="text-[11px] leading-5 text-gray-400">Único — ej: F-2026-0001</span>
        </div>
    <x-text-input maxlength="255" id="numero" name="numero" type="text" placeholder="F-2026-0001" class="mt-1 block w-full" :value="old('numero', $factura->numero ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('numero')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <div class="flex h-5 items-center justify-between gap-2">
            <x-input-label for="monto" value="Monto" />
            <span class="text-[11px] leading-5 text-gray-400">hasta $10.000.000</span>
        </div>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">$</span>
            <x-text-input id="monto" name="monto" type="number" min="0" max="10000000" step="0.01" placeholder="0,00" class="mt-1 block w-full pl-7" :value="old('monto', $factura->monto ?? '')" required />
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('monto')" />
    </div>
    <div>
        <x-input-label for="estado" value="Estado" />
        <x-buscador-select name="estado" :conBuscador="false" textoTodos=""
                           :opciones="['pendiente' => 'Pendiente', 'pagada' => 'Pagada', 'vencida' => 'Vencida']"
                           :seleccionado="old('estado', $factura->estado ?? 'pendiente')"
                           placeholder="Estado..." />
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
        <x-buscador-select name="proyecto_id"
                           :opciones="$proyectos->mapWithKeys(fn ($p) => [$p->id => $p->nombre])->all()"
                           :seleccionado="old('proyecto_id', $factura->proyecto_id ?? '')"
                           placeholder="Buscar proyecto..." textoTodos="Ninguno" />
        <x-input-error class="mt-2" :messages="$errors->get('proyecto_id')" />
    </div>
    <div>
        <x-input-label for="emitida_por" value="Emitida por" />
        <x-buscador-select name="emitida_por"
                           :opciones="$usuarios->mapWithKeys(fn ($u) => [$u->id => $u->name])->all()"
                           :seleccionado="old('emitida_por', $factura->emitida_por ?? auth()->id())"
                           placeholder="Buscar usuario..." textoTodos="Nadie" />
        <x-input-error class="mt-2" :messages="$errors->get('emitida_por')" />
    </div>
</div>
