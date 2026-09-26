<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Auditoría del sistema</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="GET" class="mb-4 rounded-xl border border-gray-100 bg-gray-50/60 p-4">
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <div>
                    <label for="q" class="block text-xs font-medium text-gray-500 uppercase mb-1">Buscar</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Evento o modelo..."
                           class="w-full border-gray-300 rounded-lg text-sm focus:border-[#00b87d] focus:ring-[#00b87d]">
                </div>
                {{-- Al elegir rol se recarga la pagina: el buscador de usuario queda solo con los que cumplen ese rol --}}
                <x-buscador-select name="rol" label="Rol" textoTodos="Todos"
                                   :opciones="$opcionesRol"
                                   :seleccionado="request('rol')"
                                   placeholder="Buscar rol..."
                                   autoEnviar />
                <x-buscador-select name="usuario" label="Usuario" textoTodos="Todos"
                                   :opciones="$opcionesUsuario"
                                   :seleccionado="$usuarioElegido"
                                   placeholder="Buscar usuario..." />
                <x-buscador-select name="accion" label="Acción" textoTodos="Todas"
                                   :opciones="['created' => 'Creación', 'updated' => 'Modificación', 'deleted' => 'Eliminación']"
                                   :seleccionado="request('accion')"
                                   placeholder="Buscar acción..." />
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                @if (request('modelo'))
                    <input type="hidden" name="modelo" value="{{ request('modelo') }}">
                    <input type="hidden" name="registro" value="{{ request('registro') }}">
                    <span class="text-xs text-gray-500">Historial de {{ class_basename(request('modelo')) }} #{{ request('registro') }}</span>
                @endif
                <button class="px-4 py-2.5 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63] inline-flex items-center gap-1.5">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    Filtrar
                </button>
                @if (request()->filled('q') || request()->filled('usuario') || request()->filled('accion') || request()->filled('rol'))
                    <a href="{{ route('auditoria.index') }}" class="text-xs text-gray-500 hover:underline">Limpiar</a>
                @endif
            </div>
        </form>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Rol</th>
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
                                @if ($registro->user)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-700">
                                        {{ $registro->user->roles->pluck('name')->implode(', ') ?: 'sin rol' }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
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
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            @if (request('modelo'))
                                Este elemento todavía no tuvo cambios registrados: solo aparece en el historial después de crearse o editarse.
                            @else
                                Sin registros.
                            @endif
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $auditoria->links() }}
    </div>
</x-app-layout>