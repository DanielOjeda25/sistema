<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('proyectos.index') }}" class="text-xs text-gray-500 hover:underline">← Mis proyectos</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $proyecto->nombre }}</h2>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold self-start
                {{ $proyecto->estado === 'completado' ? 'bg-green-100 text-green-700' : '' }}
                {{ $proyecto->estado === 'en_progreso' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $proyecto->estado === 'pendiente' ? 'bg-gray-100 text-gray-600' : '' }}
                {{ $proyecto->estado === 'cancelado' ? 'bg-red-100 text-red-700' : '' }}">
                {{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-6 space-y-6">

            {{-- Resumen de avance --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-semibold text-gray-700">Avance del proyecto</span>
                    <span class="text-2xl font-bold text-[#008c63]">{{ $avanceProyecto }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3">
                    <div class="bg-[#00b87d] h-3 rounded-full transition-all" style="width: {{ $avanceProyecto }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ $tareasHechas }} de {{ $totalTareas }} tareas completadas ·
                    PM a cargo: {{ $proyecto->pm?->name ?? '—' }}</p>
            </div>

            {{-- Linea de tiempo: hitos y sprints en orden cronologico --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-lg font-bold text-gray-800">Cómo viene el proyecto</h3>
                    <div class="hidden sm:flex items-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#00b87d]"></span> Completado</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-ping-slow"></span> En curso</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Atrasado</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span> Pendiente</span>
                    </div>
                </div>
                <p class="text-sm text-gray-500">Seguí el camino de izquierda a derecha.</p>

                @php
                    // progreso de la linea: hasta el ultimo hito/etapa completado
                    $ultimoHecho = 0;
                    foreach ($linea as $i => $item) { if ($item['hecho']) { $ultimoHecho = $i; } }
                    $llenado = $linea->count() > 1 ? round($ultimoHecho / ($linea->count() - 1) * 100) : 100;
                    $marcadoCurso = false;
                @endphp

                <div class="overflow-x-auto scroll-oculto mt-6 pb-2">
                    <div class="relative min-w-max px-6">
                        {{-- riel de fondo y riel de avance --}}
                        <div class="absolute top-6 left-8 right-8 h-1.5 bg-gray-200 rounded-full"></div>
                        <div class="absolute top-6 left-8 h-1.5 bg-gradient-to-r from-[#00b87d] to-[#00d99a] rounded-full animar-riel"
                             style="width: {{ $llenado }}%"></div>

                        <div class="flex items-start">
                            @foreach ($linea as $i => $item)
                                @php
                                    $esCurso = ! $item['hecho'] && ! $item['vencido'] && ! $marcadoCurso;
                                    if ($esCurso) { $marcadoCurso = true; }
                                    $claseNodo = $item['hecho'] ? 'bg-[#00b87d] text-white' : ($item['vencido'] ? 'bg-red-500 text-white' : ($esCurso ? 'bg-indigo-500 text-white animar-curso' : 'bg-gray-300 text-gray-500'));
                                    $icono = $item['hecho'] ? '✓' : ($item['vencido'] ? '!' : ($esCurso ? '▶' : $i + 1));
                                @endphp
                                <div class="relative w-44 shrink-0 px-2 text-center animar-item" style="animation-delay: {{ $i * 0.18 }}s">
                                    <p class="text-xs text-gray-400 h-4">{{ $item['fecha']?->format('d/m/Y') }}</p>
                                    <div class="relative mx-auto my-3 w-10 h-10">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold shadow-md {{ $claseNodo }}">{{ $icono }}</div>
                                    </div>
                                    <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full mb-1
                                        {{ $item['tipo'] === 'hito' ? 'bg-amber-100 text-amber-700' : 'bg-indigo-50 text-indigo-700' }}">
                                        {{ $item['tipo'] === 'hito' ? 'Hito' : 'Etapa' }}
                                    </span>
                                    <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $item['titulo'] }}</p>
                                    @if ($item['hecho'])
                                        <p class="text-xs text-[#008c63] font-semibold mt-0.5">✓ Completado</p>
                                    @elseif ($item['vencido'])
                                        <p class="text-xs text-red-600 font-semibold mt-0.5">Atrasado</p>
                                    @elseif ($esCurso)
                                        <p class="text-xs text-indigo-600 font-semibold mt-0.5">En curso</p>
                                    @endif
                                    @if ($item['detalle'])
                                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $item['detalle'] }}</p>
                                    @endif
                                    @if (isset($item['avance']))
                                        <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-400 rounded-full animar-riel" style="width: {{ $item['avance'] }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <style>
                    @keyframes crecerRiel { from { width: 0; } }
                    .animar-riel { animation: crecerRiel 1.2s ease-out; }
                    @keyframes aparecerItem { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
                    .animar-item { animation: aparecerItem .5s ease-out both; }
                    @keyframes latido { 0%,100% { box-shadow: 0 0 0 0 rgba(99,102,241,.45); } 50% { box-shadow: 0 0 0 8px rgba(99,102,241,0); } }
                    .animar-curso { animation: latido 2s infinite; }
                    .scroll-oculto::-webkit-scrollbar { height: 6px; }
                    .scroll-oculto::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
                </style>

            </div>

            {{-- Novedades visibles para el cliente --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Novedades del equipo</h3>
                @forelse ($novedades as $novedad)
                    <div class="border-b last:border-0 border-gray-100 py-3">
                        <p class="text-sm text-gray-700">{{ $novedad->contenido }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $novedad->autor?->name ?? 'Equipo' }} · {{ $novedad->fecha?->format('d/m/Y') }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Sin novedades por ahora.</p>
                @endforelse
            </div>

            {{-- Entregables aprobados --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Material aprobado para vos</h3>
                @forelse ($entregables as $entregable)
                    <div class="border-b last:border-0 border-gray-100 py-3 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $entregable->titulo }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ ucfirst($entregable->tipo) }} · {{ $entregable->generado_en?->format('d/m/Y') }}</p>
                        </div>
                        <a href="{{ route('entregables.show', $entregable) }}" class="text-sm text-indigo-600 hover:underline shrink-0">Ver</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Todavía no hay material aprobado para este proyecto.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
