<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Usuario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="text-sm text-gray-500 mb-6">
                    La contraseña que pongas acá es <strong>provisional</strong>. Pasásela a la persona
                    junto con su correo: cuando entre, la cambia desde su perfil.
                </p>

                <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
                    @csrf

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

                    <div>
                        <x-input-label for="email" value="Correo electrónico" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required placeholder="usuario@example.com" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        <p class="mt-1 text-xs text-gray-500">Con este correo va a iniciar sesión.</p>
                    </div>

                    <div>
                        <x-input-label for="rol" value="Rol" />
                        <select id="rol" name="rol" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un rol —</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->name }}" @selected(old('rol') === $rol->name)>{{ $rol->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('rol')" />
                        <p class="mt-1 text-xs text-gray-500">Define a qué partes del sistema puede entrar.</p>
                    </div>

                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select id="estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="activo" @selected(old('estado', 'activo') === 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>

                    <div class="pt-4 border-t">
                        <x-input-label for="password" value="Contraseña provisional" />
                        <x-password-input id="password" name="password" :minimo="8" required placeholder="••••••••" />
                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Repetir contraseña" />
                        <x-password-input id="password_confirmation" name="password_confirmation" confirma-de="password" required placeholder="••••••••" />
                        <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-2">
                        <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:underline">Cancelar</a>
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 transition">
                            Crear Usuario
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
