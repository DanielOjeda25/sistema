{{--
    Campos del formulario de Cliente, compartidos entre la pantalla completa
    (create/edit) y los modales del listado. $cliente es null al crear.
--}}
<div>
    <x-input-label for="{{ $prefijo ?? '' }}nombre" value="Nombre" />
    <x-text-input id="{{ $prefijo ?? '' }}nombre" name="nombre" type="text" class="mt-1 block w-full" :value="old('nombre', $cliente->nombre ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
</div>

<div>
    <x-input-label for="{{ $prefijo ?? '' }}apellido" value="Apellido" />
    <x-text-input id="{{ $prefijo ?? '' }}apellido" name="apellido" type="text" class="mt-1 block w-full" :value="old('apellido', $cliente->apellido ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('apellido')" />
</div>

<div>
    <x-input-label for="{{ $prefijo ?? '' }}email" value="Correo Electrónico" />
    <x-text-input id="{{ $prefijo ?? '' }}email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $cliente->email ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('email')" />
</div>

<div>
    <x-input-label for="{{ $prefijo ?? '' }}telefono" value="Teléfono (opcional)" />
    <x-text-input id="{{ $prefijo ?? '' }}telefono" name="telefono" type="text" class="mt-1 block w-full" :value="old('telefono', $cliente->telefono ?? '')" />
    <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
</div>

<div>
    <x-input-label for="{{ $prefijo ?? '' }}empresa" value="Empresa (opcional)" />
    <x-text-input id="{{ $prefijo ?? '' }}empresa" name="empresa" type="text" class="mt-1 block w-full" :value="old('empresa', $cliente->empresa ?? '')" />
    <x-input-error class="mt-2" :messages="$errors->get('empresa')" />
</div>

<div>
    <x-input-label for="{{ $prefijo ?? '' }}estado" value="Estado" />
    <select id="{{ $prefijo ?? '' }}estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
        <option value="activo" @selected(old('estado', $cliente->estado ?? 'activo') == 'activo')>Activo</option>
        <option value="inactivo" @selected(old('estado', $cliente->estado ?? 'activo') == 'inactivo')>Inactivo</option>
    </select>
    <x-input-error class="mt-2" :messages="$errors->get('estado')" />
</div>
