{{--
    Campos del formulario de Usuario (crear y editar). Con Alpine, al elegir
    el rol "Cliente" aparece el selector de ficha: la cuenta se vincula a una
    fila del modulo Clientes (la empresa que contrata) y ese vinculo es lo
    que define que proyectos y facturas va a ver. La contrasena usa el
    componente x-password-input (mostrar/ocultar + aviso en vivo).
--}}
<div class="grid gap-4 sm:grid-cols-2" x-data="{ rol: '{{ old('rol', '') }}' }"
     @change="if ($event.target.name === 'rol') rol = $event.target.value">
    <div>
        <x-input-label for="name" value="Nombre" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus  maxlength="255" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="apellido" value="Apellido" />
        <x-text-input id="apellido" name="apellido" type="text" class="mt-1 block w-full" :value="old('apellido')" required  maxlength="255" />
        <x-input-error class="mt-2" :messages="$errors->get('apellido')" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="email" value="Correo electrónico" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required placeholder="usuario@example.com" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <x-input-label for="rol" value="Rol" />
        <x-buscador-select name="rol" :conBuscador="false" textoTodos=""
                           :opciones="$roles->pluck('name', 'name')->all()"
                           :seleccionado="old('rol')"
                           placeholder="Selecciona un rol..." />
        <x-input-error class="mt-2" :messages="$errors->get('rol')" />
    </div>

    <div>
        <x-input-label for="estado" value="Estado" />
        <x-buscador-select name="estado" :conBuscador="false" textoTodos=""
                           :opciones="['activo' => 'Activo', 'inactivo' => 'Inactivo']"
                           :seleccionado="old('estado', 'activo')"
                           placeholder="Estado..." />
        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
    </div>

    <div x-show="rol === 'Cliente'" x-cloak class="sm:col-span-2 rounded-lg border border-emerald-100 bg-emerald-50 p-4">
        <x-input-label for="cliente_id" value="Cuenta para la ficha de cliente" />
        <x-buscador-select name="cliente_id"
                           :opciones="$clientes->mapWithKeys(fn ($c) => [$c->id => trim($c->nombre.' '.$c->apellido).(($c->empresa ?? '') !== '' ? ' · '.$c->empresa : '')])->all()"
                           :seleccionado="old('cliente_id')"
                           placeholder="Buscar cliente..." textoTodos="Ninguna" />
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
