<div class="grid gap-4 sm:grid-cols-2" x-data="{ rol: '{{ old('rol', '') }}' }">
    <div>
        <x-input-label for="name" value="Nombre" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="apellido" value="Apellido" />
        <x-text-input id="apellido" name="apellido" type="text" class="mt-1 block w-full" :value="old('apellido')" required />
        <x-input-error class="mt-2" :messages="$errors->get('apellido')" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="email" value="Correo electrónico" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required placeholder="usuario@example.com" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <x-input-label for="rol" value="Rol" />
        <select id="rol" name="rol" x-model="rol" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            <option value="">— Seleccioná un rol —</option>
            @foreach ($roles as $rol)
                <option value="{{ $rol->name }}" @selected(old('rol') === $rol->name)>{{ $rol->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('rol')" />
    </div>

    <div>
        <x-input-label for="estado" value="Estado" />
        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
            <option value="activo" @selected(old('estado', 'activo') === 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
    </div>

    <div x-show="rol === 'Cliente'" x-cloak class="sm:col-span-2 rounded-lg border border-emerald-100 bg-emerald-50 p-4">
        <x-input-label for="cliente_id" value="Empresa del cliente" />
        <select id="cliente_id" name="cliente_id" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm">
            <option value="">— Seleccioná la empresa —</option>
            @foreach ($clientes as $c)
                <option value="{{ $c->id }}" @selected(old('cliente_id') == $c->id)>{{ $c->nombre }} {{ $c->apellido }}@if($c->empresa) · {{ $c->empresa }}@endif</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('cliente_id')" />
    </div>

    <div class="sm:col-span-2 border-t border-gray-200 pt-4">
        <x-input-label for="password" value="Contraseña provisional" />
        <x-password-input id="password" name="password" :minimo="8" required placeholder="••••••••" />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="password_confirmation" value="Repetir contraseña" />
        <x-password-input id="password_confirmation" name="password_confirmation" confirma-de="password" required placeholder="••••••••" />
        <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
    </div>
</div>
