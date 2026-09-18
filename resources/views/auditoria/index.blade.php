<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Auditoría del sistema</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="GET" class="mb-4 flex flex-wrap items-end gap-2">
            <div>
                <label for="q" class="block text-xs font-medium text-gray-500 uppercase mb-1">Buscar</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Evento o modelo..."
                       class="border-gray-300 rounded-lg w-56 focus:border-[#00b87d] focus:ring-[#00b87d]">
            </div>
            <div>
                <label for="rol" class="block text-xs font-medium text-gray-500 uppercase mb-1">Rol</label>
                <select id="rol" name="rol" onchange="filtrarUsuariosPorRol()" class="border-gray-300 rounded-lg focus:border-[#00b87d] focus:ring-[#00b87d]">
                    <option value="">Todos</option>
                    @foreach ($roles as $nombreRol)
                        <option value="{{ $nombreRol }}" @selected(request('rol') === $nombreRol)>{{ $nombreRol }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="usuario" class="block text-xs font-medium text-gray-500 uppercase mb-1">Usuario</label>
                <select id="usuario" name="usuario" class="border-gray-300 rounded-lg focus:border-[#00b87d] focus:ring-[#00b87d]">
                    <option value="">Todos</option>
                    @foreach ($usuarios as $u)
                        <option value="{{ $u->id }}" data-roles="{{ $u->roles->pluck('name')->implode(',') }}" @selected(request('usuario') == $u->id)>
                            {{ $u->name }} ({{ $u->roles->pluck('name')->implode(', ') ?: 'sin rol' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <script>
                // Al elegir un rol, el desplegable de usuario queda solo con
                // los que cumplen ese rol; si el usuario elegido no cumple,
                // se resetea a Todos.
                function filtrarUsuariosPorRol() {
                    const rol = document.getElementById('rol').value;
                    const select = document.getElementById('usuario');
                    let hayElegidoVisible = false;
                    select.querySelectorAll('option').forEach(opcion => {
                        if (opcion.value === '') { opcion.hidden = false; return; }
                        opcion.hidden = rol !== '' && !opcion.dataset.roles.split(',').includes(rol);
                        if (!opcion.hidden && opcion.selected) hayElegidoVisible = true;
                    });
                    if (!hayElegidoVisible) select.value = '';
                }
                document.addEventListener('DOMContentLoaded', filtrarUsuariosPorRol);
            </script>
            <div>
                <label for="accion" class="block text-xs font-medium text-gray-500 uppercase mb-1">Acción</label>
                <select id="accion" name="accion" class="border-gray-300 rounded-lg focus:border-[#00b87d] focus:ring-[#00b87d]">
                    <option value="">Todas</option>
                    @foreach (['created' => 'Creación', 'updated' => 'Modificación', 'deleted' => 'Eliminación'] as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected(request('accion') == $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
            @if (request('modelo'))
                <input type="hidden" name="modelo" value="{{ request('modelo') }}">
                <input type="hidden" name="registro" value="{{ request('registro') }}">
                <span class="text-xs text-gray-500 pb-2">Historial de {{ class_basename(request('modelo')) }} #{{ request('registro') }}</span>
            @endif
            <button class="px-4 py-2.5 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63] inline-flex items-center gap-1.5">
                <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                Filtrar
            </button>
            @if (request()->filled('q') || request()->filled('usuario') || request()->filled('accion') || request()->filled('rol'))
                <a href="{{ route('auditoria.index') }}" class="text-xs text-gray-500 hover:underline pb-2">Limpiar</a>
            @endif
        </form>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Evento</th>
                        <th class="px-4 py-3">Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($auditoria as $registro)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $registro->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $registro->user->name ?? 'Sistema' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                                    {{ $registro->event === 'created' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $registro->event === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $registro->event === 'deleted' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ['created' => 'Creación', 'updated' => 'Modificación', 'deleted' => 'Eliminación'][$registro->event] ?? $registro->event }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-900">{{ $registro->titulo_auditoria }}</span>
                                @if (! empty($registro->cambios_auditoria))
                                    <div class="mt-1 space-y-0.5 text-xs text-gray-600">
                                        @foreach ($registro->cambios_auditoria as $cambio)
                                            <div class="flex flex-wrap items-baseline gap-1">
                                                <span class="font-semibold text-gray-500">{{ $cambio['campo'] }}:</span>
                                                <span class="line-through decoration-red-400 text-gray-400">{{ $cambio['viejo'] }}</span>
                                                <span class="text-gray-400">&rarr;</span>
                                                <span class="font-medium text-[#008c63]">{{ $cambio['nuevo'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Sin registros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $auditoria->links() }}
    </div>
</x-app-layout>