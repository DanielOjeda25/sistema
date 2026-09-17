<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Usuarios del Sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b border-gray-200">
                
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Listado de Usuarios</h3>
                    @hasrole('Jefe')
                        <button type="button" data-abrir-modal="modal-usuario-crear" class="inline-flex items-center px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63]">
                            + Nuevo Usuario
                        </button>
                    @endhasrole
                </div>

                <form method="GET" action="{{ route('users.index') }}" class="mb-4 flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="q" class="block text-xs font-medium text-gray-500 uppercase mb-1">Buscar</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Nombre o email..."
                               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63] inline-flex items-center gap-1.5">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                        Filtrar
                    </button>
                    @if (request()->filled('q'))
                        <a href="{{ route('users.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Limpiar</a>
                    @endif
                </form>

                <div class="overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nombre</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Roles Actuales</th>
                                <th scope="col" class="px-6 py-3">Empresa</th>
                                <th scope="col" class="px-6 py-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            @php
                                $rolActual = $user->roles->first()?->name;
                                $valoresUsuario = $user->only(['name', 'apellido', 'email', 'estado']) + [
                                    'rol' => $rolActual,
                                    'cliente_id' => $user->cliente_id,
                                ];
                            @endphp
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4">
                                    @forelse($user->roles as $role)
                                        <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-red-500 text-xs italic">Sin rol asignado</span>
                                    @endforelse
                                </td>
                                <td class="px-6 py-4">
                                    @if ($user->cliente)
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5">
                                            {{ $user->cliente->empresa ?? $user->cliente->nombre }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        @can('editar_roles')
                                        <button type="button" data-abrir-modal="modal-usuario-rol"
                                                data-url="{{ route('users.roles.update', $user) }}"
                                                data-valores='@json(["rol" => $rolActual])'
                                                class="text-indigo-600 hover:text-indigo-900" title="Cambiar rol" aria-label="Cambiar rol">
                                            <x-heroicon-o-user-group class="w-5 h-5" />
                                        </button>
                                        <button type="button" data-abrir-modal="modal-usuario-editar"
                                                data-url="{{ route('users.update', $user) }}"
                                                data-valores='@json($valoresUsuario)'
                                                class="text-yellow-600 hover:text-yellow-800" title="Editar" aria-label="Editar">
                                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                                        </button>
                                        @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('¿Querés eliminar este usuario? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Eliminar" aria-label="Eliminar">
                                                <x-heroicon-o-trash class="w-5 h-5" />
                                            </button>
                                        </form>
                                        @endif
                                        @else
                                            <span class="text-gray-400 italic">Solo lectura</span>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    @hasrole('Jefe')
        <x-crud-modal id="modal-usuario-crear" abrir-con-errores titulo="Nuevo Usuario">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="desde_modal" value="1">
                @include('users._campos')
                <div class="flex items-center justify-end gap-4 border-t border-gray-200 pt-3">
                    <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                    <x-primary-button>Crear Usuario</x-primary-button>
                </div>
            </form>
        </x-crud-modal>

        <x-crud-modal id="modal-usuario-editar" abrir-con-errores titulo="Editar Usuario">
            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="desde_modal" value="1">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="edit_name" value="Nombre" />
                        <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <x-input-label for="edit_apellido" value="Apellido" />
                        <x-text-input id="edit_apellido" name="apellido" type="text" class="mt-1 block w-full" :value="old('apellido')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('apellido')" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="edit_email" value="Correo electrónico" />
                        <x-text-input id="edit_email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required placeholder="usuario@example.com" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>
                    <div>
                        <x-input-label for="edit_rol" value="Rol" />
                        <select id="edit_rol" name="rol" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
                            <option value="">— Seleccioná un rol —</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->name }}" @selected(old('rol') === $rol->name)>{{ $rol->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('rol')" />
                    </div>
                    <div>
                        <x-input-label for="edit_estado" value="Estado" />
                        <select id="edit_estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
                            <option value="activo" @selected(old('estado', 'activo') === 'activo')>Activo</option>
                            <option value="inactivo" @selected(old('estado') === 'inactivo')>Inactivo</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                    </div>
                    <div class="sm:col-span-2 rounded-lg border border-emerald-100 bg-emerald-50 p-4">
                        <x-input-label for="edit_cliente_id" value="Empresa del cliente (solo rol Cliente)" />
                        <select id="edit_cliente_id" name="cliente_id" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm">
                            <option value="">— Sin empresa —</option>
                            @foreach ($clientes as $c)
                                <option value="{{ $c->id }}" @selected(old('cliente_id') == $c->id)>{{ $c->nombre }} {{ $c->apellido }}@if($c->empresa) · {{ $c->empresa }}@endif</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('cliente_id')" />
                    </div>
                    <div class="sm:col-span-2 border-t border-gray-200 pt-4">
                        <x-input-label for="edit_password" value="Nueva contraseña (dejá vacío para no cambiarla)" />
                        <x-password-input id="edit_password" name="password" :minimo="8" placeholder="••••••••" />
                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="edit_password_confirmation" value="Repetir nueva contraseña" />
                        <x-password-input id="edit_password_confirmation" name="password_confirmation" confirma-de="edit_password" placeholder="••••••••" />
                    </div>
                </div>
                <div class="flex items-center justify-end gap-4 border-t border-gray-200 pt-3">
                    <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                    <x-primary-button>Guardar Cambios</x-primary-button>
                </div>
            </form>
        </x-crud-modal>

        <x-crud-modal id="modal-usuario-rol" titulo="Cambiar rol">
            <form method="POST" action="{{ route('users.roles.update', ['user' => 0]) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <p class="text-sm text-gray-500">Elegí el rol del usuario. Un usuario tiene un solo rol en el sistema.</p>
                <div>
                    <x-input-label for="rol_usuario" value="Rol" />
                    <select id="rol_usuario" name="rol" class="mt-1 block w-full border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] rounded-md shadow-sm" required>
                        <option value="">— Seleccioná un rol —</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->name }}" @selected(old('rol') === $rol->name)>{{ $rol->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('rol')" />
                </div>
                <div class="flex items-center justify-end gap-4 border-t border-gray-200 pt-3">
                    <button type="button" data-crud-cerrar class="text-sm text-gray-600 hover:underline">Cancelar</button>
                    <x-primary-button>Guardar Rol</x-primary-button>
                </div>
            </form>
        </x-crud-modal>
    @endhasrole
</x-app-layout>