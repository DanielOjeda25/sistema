<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Auditoría del sistema</h2>
        <p class="mt-1 text-sm text-gray-500">Quién hizo cada cambio y cuándo: creaciones, modificaciones y eliminaciones.</p>
    </x-slot>

    <div class="py-12">
        {{-- Aviso de modo historial: se llega desde el boton "Historial" de un listado --}}
        @if ($contexto)
            <div class="mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-[#00b87d]/20 bg-[#00b87d]/5 px-4 py-3">
                <x-heroicon-o-clock class="w-5 h-5 shrink-0 text-[#008c63]" />
                <p class="text-sm text-gray-700">
                    Estás viendo el historial de <span class="font-semibold text-gray-900">{{ $contexto }}</span>.
                </p>
                <a href="{{ route('auditoria.index') }}"
                   class="ml-auto text-xs font-semibold uppercase tracking-widest text-[#008c63] hover:underline">
                    &larr; Ver toda la actividad
                </a>
            </div>
        @endif

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
                @endif
                <button class="px-4 py-2.5 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63] inline-flex items-center gap-1.5">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                    Filtrar
                </button>
                @if (request()->filled('q') || request()->filled('usuario') || request()->filled('accion') || request()->filled('rol'))
                    <a href="{{ route('auditoria.index') }}" class="text-xs text-gray-500 hover:underline">Limpiar filtros</a>
                @endif
            </div>
        </form>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Actividad registrada</h3>
                <span class="text-xs text-gray-400">{{ $auditoria->total() }} {{ $auditoria->total() === 1 ? 'registro' : 'registros' }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-5 py-3">Fecha</th>
                            <th class="px-5 py-3">Usuario</th>
                            <th class="px-5 py-3">Evento</th>
                            <th class="px-5 py-3">Registro</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($auditoria as $registro)
                            @php($estilosEvento = [
                                'created' => 'bg-emerald-100 text-emerald-800',
                                'updated' => 'bg-blue-100 text-blue-800',
                                'deleted' => 'bg-red-100 text-red-800',
                            ])
                            @php($iconosEvento = [
                                'created' => 'heroicon-o-plus-circle',
                                'updated' => 'heroicon-o-pencil',
                                'deleted' => 'heroicon-o-trash',
                            ])
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-800">{{ $registro->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $registro->created_at->format('H:i') }} hs</div>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($registro->user)
                                        <div class="flex items-center gap-2.5">
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#00b87d]/10 text-xs font-bold text-[#008c63]">
                                                {{ mb_substr($registro->user->name, 0, 1).($registro->user->apellido ? mb_substr($registro->user->apellido, 0, 1) : '') }}
                                            </span>
                                            <div>
                                                <div class="font-medium text-gray-800">{{ $registro->user->name }}</div>
                                                <div class="text-xs text-gray-400">{{ $registro->user->roles->pluck('name')->implode(', ') ?: 'sin rol' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">Sistema</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold {{ $estilosEvento[$registro->event] ?? 'bg-gray-100 text-gray-700' }}">
                                        @if (isset($iconosEvento[$registro->event]))
                                            <x-dynamic-component :component="$iconosEvento[$registro->event]" class="w-3.5 h-3.5" />
                                        @endif
                                        {{ ['created' => 'Creación', 'updated' => 'Modificación', 'deleted' => 'Eliminación'][$registro->event] ?? $registro->event }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-gray-800">{{ $registro->titulo_auditoria }}</span>
                                    @if (! empty($registro->cambios_auditoria))
                                        <div class="mt-2 space-y-1 border-l-2 border-gray-100 pl-3">
                                            @foreach ($registro->cambios_auditoria as $cambio)
                                                <div class="flex flex-wrap items-baseline gap-1.5 text-xs">
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
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <x-heroicon-o-clock class="w-10 h-10 text-gray-300" />
                                        <p class="text-sm font-medium text-gray-500">
                                            @if (request('modelo'))
                                                Este elemento todavía no tuvo cambios registrados.
                                            @else
                                                No hay actividad con estos filtros.
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            @if (request('modelo'))
                                                Solo aparece en el historial después de crearse o editarse.
                                            @else
                                                Probá ajustar o limpiar los filtros.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $auditoria->links() }}
    </div>
</x-app-layout>
