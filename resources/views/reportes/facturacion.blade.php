<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('reportes.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:underline"><x-heroicon-o-arrow-left class="h-3.5 w-3.5" /> Reportes</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Facturación y cobranzas</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-6 space-y-6">

            {{-- Filtros --}}
            <form method="GET" action="{{ route('reportes.facturacion') }}" class="rounded-xl border border-gray-100 bg-gray-50/60 p-4">
                <div class="grid grid-cols-2 gap-3 md:grid-cols-4 xl:grid-cols-6">
                    <div>
                        <label for="desde" class="block text-xs font-medium text-gray-500 uppercase mb-1">Emitidas desde</label>
                        <input type="date" name="desde" id="desde" value="{{ request('desde') }}"
                               class="w-full h-11 rounded-lg border-gray-300 focus:border-[#00b87d] focus:ring-[#00b87d] shadow-sm">
                    </div>
                    <div>
                        <label for="hasta" class="block text-xs font-medium text-gray-500 uppercase mb-1">Emitidas hasta</label>
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
                        <x-buscador-select name="proyecto_id" label="Proyecto" textoTodos="Todos"
                                           :opciones="$proyectosLista->pluck('nombre', 'id')->all()"
                                           :seleccionado="request('proyecto_id')"
                                           placeholder="Buscar proyecto..." />
                    </div>
                    <div class="col-span-2 flex items-end justify-end gap-3">
                        @if (request()->filled('desde') || request()->filled('hasta') || request()->filled('cliente_id') || request()->filled('proyecto_id'))
                            <a href="{{ route('reportes.facturacion') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Limpiar</a>
                        @endif
                        <button type="submit" class="px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63] inline-flex items-center gap-1.5">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>

            {{-- Totales en guita --}}
            <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
                @foreach ([
                    'Facturas emitidas' => [$resumen['facturas'], false],
                    'Total facturado' => ['$ ' . number_format($resumen['facturado'], 0, ',', '.'), false],
                    'Cobrado' => ['$ ' . number_format($resumen['pagado'], 0, ',', '.'), false],
                    'Pendiente de cobro' => ['$ ' . number_format($resumen['pendiente'], 0, ',', '.'), false],
                    'Vencido' => ['$ ' . number_format($resumen['vencido'], 0, ',', '.'), true],
                ] as $titulo => [$valor, $alerta])
                    <div class="rounded-xl border {{ $alerta ? 'border-red-100 bg-red-50/60' : 'border-[#d7eee6] bg-white' }} p-4 shadow-sm">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">{{ $titulo }}</p>
                        <p class="mt-1 text-xl font-bold {{ $alerta ? 'text-red-600' : 'text-[#008c63]' }}">{{ $valor }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Graficos --}}
            <div class="grid gap-5 xl:grid-cols-2">
                <div class="rounded-xl border border-[#d7eee6] bg-white p-5 shadow-sm">
                    <h3 class="font-semibold text-slate-800">Monto por estado</h3>
                    <p class="text-xs text-slate-400 mt-1">Distribución del dinero del conjunto filtrado</p>
                    <div class="mt-4 h-56">
                        <canvas data-grafico="dona" data-valores='@json($donutMontos)'></canvas>
                    </div>
                </div>
                <div class="rounded-xl border border-[#d7eee6] bg-white p-5 shadow-sm">
                    <h3 class="font-semibold text-slate-800">Facturación por mes</h3>
                    <p class="text-xs text-slate-400 mt-1">Últimos 6 meses del conjunto filtrado</p>
                    <div class="mt-4 h-56">
                        <canvas data-grafico="barras-h" data-valores='@json($barrasMes)'></canvas>
                    </div>
                </div>
            </div>

            {{-- Tabla por proyecto --}}
            <div class="rounded-xl border border-[#d7eee6] bg-white shadow-sm overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-[#d7eee6]">
                    <div>
                        <h3 class="font-semibold text-slate-800">Montos por proyecto</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $proyectos->count() }} proyecto(s) con facturación en el conjunto filtrado</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('reportes.facturacion.exportar', array_merge(request()->query(), ['formato' => 'csv'])) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700 uppercase tracking-wider hover:bg-gray-50">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> CSV
                        </a>
                        <a href="{{ route('reportes.facturacion.exportar', array_merge(request()->query(), ['formato' => 'pdf'])) }}"
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Facturas</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Facturado</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cobrado</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pendiente</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Vencido</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-gray-700">
                            @forelse ($proyectos as $fila)
                                <tr>
                                    <td class="px-4 py-3">
                                        @if ($fila['id'])
                                            <a href="{{ route('proyectos.show', $fila['id']) }}" class="font-medium text-gray-800 hover:text-[#008c63]">{{ $fila['nombre'] }}</a>
                                        @else
                                            {{ $fila['nombre'] }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $fila['cliente'] }}</td>
                                    <td class="px-4 py-3">{{ $fila['facturas'] }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">$ {{ number_format($fila['facturado'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-[#008c63]">$ {{ number_format($fila['pagado'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right text-amber-600">$ {{ number_format($fila['pendiente'], 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-right {{ $fila['vencido'] > 0 ? 'font-bold text-red-600' : 'text-gray-300' }}">$ {{ number_format($fila['vencido'], 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No hay facturas para los filtros aplicados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
