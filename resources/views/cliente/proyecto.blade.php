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
                <h3 class="text-lg font-bold text-gray-800 mb-1">Cómo viene el proyecto</h3>
                <p class="text-sm text-gray-500 mb-6">Los hitos acordados y las etapas de trabajo, en orden de tiempo.</p>

                @forelse ($linea as $item)
                    <div class="flex gap-4">
                        {{-- columna fecha + linea vertical --}}
                        <div class="flex flex-col items-center">
                            <div class="w-4 h-4 rounded-full mt-1.5
                                {{ $item['hecho'] ? 'bg-[#00b87d]' : ($item['vencido'] ? 'bg-red-500' : 'bg-gray-300') }}"></div>
                            @if (! $loop->last)
                                <div class="w-0.5 flex-1 bg-gray-200 my-1"></div>
                            @endif
                        </div>
                        <div class="flex-1 pb-6">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full
                                    {{ $item['tipo'] === 'hito' ? 'bg-amber-100 text-amber-700' : 'bg-indigo-100 text-indigo-700' }}">
                                    {{ $item['tipo'] === 'hito' ? 'Hito' : 'Etapa' }}
                                </span>
                                <span class="font-semibold text-gray-800">{{ $item['titulo'] }}</span>
                                @if ($item['hecho'])
                                    <span class="text-xs text-[#008c63] font-semibold">✓ Completado</span>
                                @elseif ($item['vencido'])
                                    <span class="text-xs text-red-600 font-semibold">Atrasado</span>
                                @endif
                            </div>
                            @if ($item['fecha'])
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item['fecha']->format('d/m/Y') }}</p>
                            @endif
                            @if ($item['detalle'])
                                <p class="text-sm text-gray-600 mt-1">{{ $item['detalle'] }}</p>
                            @endif
                            @if (isset($item['avance']))
                                <div class="mt-2 w-full max-w-xs bg-gray-100 rounded-full h-2">
                                    <div class="bg-indigo-400 h-2 rounded-full" style="width: {{ $item['avance'] }}%"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Todavía no hay hitos ni etapas cargados para este proyecto.</p>
                @endforelse
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
