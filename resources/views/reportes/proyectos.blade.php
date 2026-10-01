<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('reportes.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:underline"><x-heroicon-o-arrow-left class="h-3.5 w-3.5" /> Reportes</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Estado y avance de proyectos</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-6 space-y-6">

            {{-- Filtros del reporte --}}
            <form method="GET" action="{{ route('reportes.proyectos') }}" class="rounded-xl border border-gray-100 bg-gray-50/60 p-4">
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4 xl:grid-cols-7">
                    <div>
                        <label for="desde" class="block text-xs font-medium text-gray-500 uppercase mb-1">Desde</label>
                        <input type="date" name="desde" id="desde" value="{{ request('desde') }}"
                               class="w-full h-11 rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] shadow-sm">
                    </div>
                    <div>
                        <label for="hasta" class="block text-xs font-medium text-gray-500 uppercase mb-1">Hasta</label>
                        <input type="date" name="hasta" id="hasta" value="{{ request('hasta') }}"
                               class="w-full h-11 rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] shadow-sm">
                    </div>
                    <div>
                        <x-buscador-select name="cliente_id" label="Cliente" textoTodos="Todos"
                                           :opciones="$clientes->pluck('nombre', 'id')->all()"
                                           :seleccionado="request('cliente_id')"
                                           placeholder="Buscar cliente..." />
                    </div>
                    <div>
                        <x-buscador-select name="pm_id" label="PM" textoTodos="Todos"
                                           :opciones="$pms->pluck('name', 'id')->all()"
                                           :seleccionado="request('pm_id')"
                                           placeholder="Buscar PM..." />
                    </div>
                    <div>
                        <x-buscador-select name="estado" label="Estado" textoTodos="Todos"
                                           :opciones="$estados"
                                           :seleccionado="request('estado')"
                                           placeholder="Estado..." />
                    </div>
                    <div class="col-span-2 flex items-end justify-end gap-3">
                        @if (request()->filled('desde') || request()->filled('hasta') || request()->filled('cliente_id') || request()->filled('pm_id') || request()->filled('estado'))
                            <a href="{{ route('reportes.proyectos') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Limpiar</a>
                        @endif
                        <button type="submit" class="px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63] inline-flex items-center gap-1.5">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>

            {{-- Totales del conjunto filtrado --}}
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4 xl:grid-cols-8">
                @foreach ([
                    'Proyectos' => $resumen['total'],
                    'Completados' => $resumen['completados'],
                    'En progreso' => $resumen['en_progreso'],
                    'Retrasados' => $resumen['retrasados'],
                    'Avance global' => $resumen['avance_global'] . '%',
                    'Tareas vencidas' => $resumen['tareas_vencidas'],
                    'Hitos vencidos' => $resumen['hitos_vencidos'],
                    'Cambios pendientes' => $resumen['solicitudes_pendientes'],
                ] as $titulo => $valor)
                    <div class="rounded-xl border {{ str_contains($titulo, 'vencid') || $titulo === 'Retrasados' ? 'border-red-100 bg-red-50/60' : 'border-[#d7eee6] bg-white' }} p-4 shadow-sm">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">{{ $titulo }}</p>
                        <p class="mt-1 text-2xl font-bold {{ str_contains($titulo, 'vencid') || $titulo === 'Retrasados' ? 'text-red-600' : 'text-[#008c63]' }}">{{ $valor }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Graficos del reporte --}}
            <div class="grid gap-5 xl:grid-cols-2">
                <div class="rounded-xl border border-[#d7eee6] bg-white p-5 shadow-sm">
                    <h3 class="font-semibold text-slate-800">Proyectos por estado</h3>
                    <p class="text-xs text-slate-400 mt-1">Distribución del conjunto filtrado</p>
                    <div class="mt-4 h-56">
                        <canvas data-grafico="dona" data-valores='@json($donutEstados)'></canvas>
                    </div>
                </div>
                <div class="rounded-xl border border-[#d7eee6] bg-white p-5 shadow-sm">
                    <h3 class="font-semibold text-slate-800">Avance por proyecto</h3>
                    <p class="text-xs text-slate-400 mt-1">Porcentaje de tareas completadas</p>
                    <div class="mt-4 h-56">
                        @if ($filas->isNotEmpty())
                            <canvas data-grafico="barras-h" data-valores='@json($avancePorProyecto)'></canvas>
                        @else
                            <p class="text-sm text-slate-400 text-center mt-20">Sin proyectos para el filtro actual.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tabla detallada por proyecto --}}
            <div class="rounded-xl border border-[#d7eee6] bg-white shadow-sm overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-[#d7eee6]">
                    <div>
                        <h3 class="font-semibold text-slate-800">Detalle por proyecto</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $proyectos->total() }} proyecto(s) en el conjunto filtrado</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('reportes.proyectos.exportar', array_merge(request()->query(), ['formato' => 'csv'])) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 uppercase tracking-wider hover:bg-gray-50">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> CSV
                        </a>
                        <a href="{{ route('reportes.proyectos.exportar', array_merge(request()->query(), ['formato' => 'pdf'])) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 uppercase tracking-wider hover:bg-gray-50">
                            <x-heroicon-o-document-arrow-down class="w-4 h-4" /> PDF
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PM</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avance</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tareas</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencidas</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hitos venc./próx.</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cambios pend.</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                            @forelse ($proyectos as $fila)
                                <tr class="{{ $fila['retrasado'] ? 'bg-red-50/40' : '' }}">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('proyectos.show', $fila['id']) }}" class="font-medium text-gray-800 hover:text-[#008c63]">{{ $fila['nombre'] }}</a>
                                        @if ($fila['retrasado'])
                                            <span class="ml-1.5 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold uppercase text-red-700">Retrasado</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $fila['cliente'] }}</td>
                                    <td class="px-4 py-3">{{ $fila['pm'] }}</td>
                                    <td class="px-4 py-3"><x-estado-badge :estado="$fila['estado_texto']" /></td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-[#00b87d] rounded-full" style="width: {{ $fila['avance'] }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold">{{ $fila['avance'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">{{ $fila['tareas_completadas'] }}/{{ $fila['tareas_total'] }}</td>
                                    <td class="px-4 py-3">
                                        @if ($fila['tareas_vencidas'] > 0)
                                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700">{{ $fila['tareas_vencidas'] }}</span>
                                        @else
                                            <span class="text-gray-300">0</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="{{ $fila['hitos_vencidos'] > 0 ? 'font-bold text-red-600' : 'text-gray-400' }}">{{ $fila['hitos_vencidos'] }}</span>
                                        <span class="text-gray-300"> / </span>
                                        <span class="{{ $fila['hitos_proximos'] > 0 ? 'font-semibold text-amber-600' : 'text-gray-400' }}">{{ $fila['hitos_proximos'] }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($fila['solicitudes_pendientes'] > 0)
                                            {{ $fila['solicitudes_pendientes'] }}
                                        @else
                                            <span class="text-gray-300">0</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-500">Ningún proyecto cumple con los filtros aplicados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $proyectos->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
