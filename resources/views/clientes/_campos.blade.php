{{--
    Campos del formulario de Cliente, compartidos por los modales del
    listado. $cliente es null al crear. Compacto: nombre y apellido van
    juntos, telefono con empresa, y el estado (siempre arranca Activo) con
    "activo" por defecto para que cargar un cliente tome segundos. Con
    autocomplete los navegadores rellenan solos cuando ya conocen los datos.
--}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="{{ $prefijo ?? '' }}nombre" value="Nombre" />
        <x-text-input maxlength="255" id="{{ $prefijo ?? '' }}nombre" name="nombre" type="text" placeholder="Patricia"
            autocomplete="given-name" class="mt-1 block w-full" :value="old('nombre', $cliente->nombre ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
    </div>
    <div>
        <x-input-label for="{{ $prefijo ?? '' }}apellido" value="Apellido" />
        <x-text-input maxlength="255" id="{{ $prefijo ?? '' }}apellido" name="apellido" type="text" placeholder="Martinez"
            autocomplete="family-name" class="mt-1 block w-full" :value="old('apellido', $cliente->apellido ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('apellido')" />
    </div>
</div>

<div>
    <x-input-label for="{{ $prefijo ?? '' }}email" value="Correo electronico" />
    <x-text-input id="{{ $prefijo ?? '' }}email" name="email" type="email" placeholder="patricia@empresa.com"
        autocomplete="email" class="mt-1 block w-full" :value="old('email', $cliente->email ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('email')" />
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <div class="flex items-baseline justify-between gap-2">
            <x-input-label for="{{ $prefijo ?? '' }}telefono" value="Telefono" />
            <span class="text-[11px] text-gray-400">opcional</span>
        </div>
        <x-text-input maxlength="50" id="{{ $prefijo ?? '' }}telefono" name="telefono" type="text" placeholder="0981-111-222"
            autocomplete="tel" class="mt-1 block w-full" :value="old('telefono', $cliente->telefono ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
    </div>
    <div>
        <div class="flex items-baseline justify-between gap-2">
            <x-input-label for="{{ $prefijo ?? '' }}empresa" value="Empresa" />
            <span class="text-[11px] text-gray-400">opcional</span>
        </div>
        <x-text-input maxlength="255" id="{{ $prefijo ?? '' }}empresa" name="empresa" type="text" placeholder="Constructora L&R"
            autocomplete="organization" class="mt-1 block w-full" :value="old('empresa', $cliente->empresa ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('empresa')" />
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="{{ $prefijo ?? '' }}estado" value="Estado" />
        <select id="{{ $prefijo ?? '' }}estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            <option value="activo" @selected(old('estado', $cliente->estado ?? 'activo') == 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado', $cliente->estado ?? 'activo') == 'inactivo')>Inactivo</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
    </div>
</div>
