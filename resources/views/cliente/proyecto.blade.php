<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('proyectos.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:underline"><x-heroicon-o-arrow-left class="h-3.5 w-3.5" /> Mis proyectos</a>
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $proyecto->nombre }}</h2>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $proyecto->estado === 'completado' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $proyecto->estado === 'en_progreso' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $proyecto->estado === 'pendiente' ? 'bg-gray-100 text-gray-600' : '' }}
                    {{ $proyecto->estado === 'cancelado' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        {{-- Una sola columna a lo ancho: la linea de tiempo es la protagonista
             y necesita todo el ancho para que las etapas no queden apretadas --}}
        <div class="max-w-7xl mx-auto px-6 space-y-6">

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
            <div class="bg-white rounded-2xl shadow-sm p-6" x-data="{ abierta: null, linea: @js($linea), abrir(i) { this.abierta = this.linea[i] } }">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-lg font-bold text-gray-800">Cómo viene el proyecto</h3>
                    <div class="hidden sm:flex items-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#00b87d]"></span> Completado</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#00d99a] animate-ping-slow"></span> En curso</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Atrasado</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span> Pendiente</span>
                    </div>
                </div>
                <p class="text-sm text-gray-500">Cada punto es una etapa del proyecto o una fecha clave. Tocá cualquiera para ver el detalle.</p>

                @php
                    // progreso de la linea: hasta el ultimo hito/etapa completado
                    $ultimoHecho = 0;
                    foreach ($linea as $i => $item) { if ($item['hecho']) { $ultimoHecho = $i; } }
                    $llenado = $linea->count() > 1 ? round($ultimoHecho / ($linea->count() - 1) * 100) : 100;
                    $marcadoCurso = false;
                @endphp

                {{-- Linea elastica: con pocos puntos estira todo el ancho; cuando hay
                     demasiados, cada punto conserva su minimo y aparece un scroll
                     con flechas en vez de comprimir todo --}}
                <div class="hidden md:block mt-6 pb-2"
                     x-data="{ desborde: false, medir() { this.desborde = this.$refs.riel.scrollWidth > this.$refs.riel.clientWidth + 4 }, desplazar(d) { this.$refs.riel.scrollBy({ left: d, behavior: 'smooth' }) } }"
                     x-init="medir()" @resize.window="medir()">
                    <div class="flex items-start gap-1">
                        <button type="button" x-show="desborde" x-cloak @click="desplazar(-460)"
                                class="mt-12 shrink-0 h-8 w-8 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700 flex items-center justify-center"
                                aria-label="Ver etapas anteriores">
                            <x-heroicon-o-chevron-left class="w-5 h-5" />
                        </button>

                        <div x-ref="riel" class="flex-1 min-w-0 overflow-x-auto scroll-oculto snap-x">
                            <div class="relative grid min-w-full"
                                 style="grid-template-columns: repeat({{ $linea->count() }}, minmax(min-content, 1fr));">
                                {{-- riel de fondo y riel de avance: dentro de la grilla para
                                     que abarquen tambien el ancho scrolleable --}}
                                <div class="absolute top-9 left-8 right-8 h-1.5 bg-gray-200 rounded-full"></div>
                                <div class="absolute top-9 left-8 h-1.5 bg-gradient-to-r from-[#00b87d] to-[#00d99a] rounded-full animar-riel"
                                     style="width: {{ $llenado }}%"></div>

                            @foreach ($linea as $i => $item)
                                @php
                                    $esCurso = ! $item['hecho'] && ! $item['vencido'] && ! $marcadoCurso;
                                    if ($esCurso) { $marcadoCurso = true; }
                                    $claseNodo = $item['hecho'] ? 'bg-[#00b87d] text-white' : ($item['vencido'] ? 'bg-red-500 text-white' : ($esCurso ? 'bg-[#00d99a] text-white animar-curso' : 'bg-gray-300 text-gray-500'));
                                    $estadoTexto = $item['estado'];
                                    $estadoClase = $item['estado_clase'];
                                    // Rango de fechas en palabras: los sprints cubren un periodo,
                                    // los hitos tienen una fecha clave.
                                    $rango = $item['tipo'] === 'sprint'
                                        ? trim($item['fecha_texto'].(! empty($item['fecha_fin_texto']) ? ' → '.$item['fecha_fin_texto'] : ''))
                                        : ($item['fecha_texto'] ? 'Fecha clave: '.$item['fecha_texto'] : '');
                                @endphp
                                <div class="relative px-2 text-center animar-item cursor-pointer snap-start"
                                     style="animation-delay: {{ $i * 0.18 }}s"
                                     @click="abrir({{ $i }})" title="Ver detalle">
                                    {{-- Marcador de donde esta el proyecto hoy --}}
                                    @if ($esCurso)
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-[#008c63]">▼ Estamos acá</p>
                                    @else
                                        <p class="h-[15px]"></p>
                                    @endif
                                    <div class="relative mx-auto mt-2 mb-4 w-10 h-10 transition-transform duration-200 hover:scale-110 cursor-pointer">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-md {{ $claseNodo }}">
                                            @if ($item['hecho'])
                                                <x-heroicon-o-check class="w-5 h-5" />
                                            @elseif ($item['vencido'])
                                                <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                                            @elseif ($esCurso)
                                                <x-heroicon-o-play class="w-4 h-4" />
                                            @else
                                                {{-- Punto gris liso: un numero suelto parecia un contador --}}
                                                <span class="w-2.5 h-2.5 rounded-full bg-white/90"></span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full mb-1
                                        {{ $item['tipo'] === 'hito' ? 'bg-amber-100 text-amber-700' : ($item['tipo'] === 'resumen' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-50 text-emerald-700') }}">
                                        {{ $item['tipo'] === 'hito' ? 'Hito' : ($item['tipo'] === 'resumen' ? 'Historial' : 'Etapa') }}
                                    </span>
                                    <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $item['titulo'] }}</p>
                                    <span class="inline-block mt-1 text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $estadoClase }}">
                                        {{ $estadoTexto }}
                                    </span>
                                    @if ($rango)
                                        <p class="text-[11px] text-gray-400 mt-1">{{ $rango }}</p>
                                    @endif
                                    @if ($item['detalle'])
                                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $item['detalle'] }}</p>
                                    @endif
                                    @if (isset($item['avance']))
                                        <div class="mt-2 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-[#00b87d] rounded-full animar-riel" style="width: {{ $item['avance'] }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            </div>
                        </div>

                        <button type="button" x-show="desborde" x-cloak @click="desplazar(460)"
                                class="mt-12 shrink-0 h-8 w-8 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700 flex items-center justify-center"
                                aria-label="Ver etapas siguientes">
                            <x-heroicon-o-chevron-right class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- Movil: linea de tiempo vertical --}}
                <div class="md:hidden">
                    @php
                        $marcadoMovil = false;
                    @endphp
                    @forelse ($linea as $item)
                        @php
                            $esCursoMovil = ! $item['hecho'] && ! $item['vencido'] && ! $marcadoMovil;
                            if ($esCursoMovil) { $marcadoMovil = true; }
                            $borde = $item['hecho'] ? 'border-[#00b87d]' : ($item['vencido'] ? 'border-red-400' : ($esCursoMovil ? 'border-[#00d99a]' : 'border-gray-300'));
                            $estadoTextoMovil = $item['estado'];
                            $estadoClaseMovil = $item['estado_clase'];
                            $rangoMovil = $item['tipo'] === 'sprint'
                                ? trim($item['fecha_texto'].(! empty($item['fecha_fin_texto']) ? ' → '.$item['fecha_fin_texto'] : ''))
                                : ($item['fecha_texto'] ? 'Fecha clave: '.$item['fecha_texto'] : '');
                        @endphp
                        <div class="mb-3 rounded-xl bg-white border-l-4 {{ $borde }} shadow-sm p-4 animar-item">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full
                                    {{ $item['tipo'] === 'hito' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ $item['tipo'] === 'hito' ? 'Hito' : 'Etapa' }}
                                </span>
                                @if ($esCursoMovil)
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-[#008c63]">▼ Estamos acá</span>
                                @endif
                            </div>
                            <p class="mt-1 font-semibold text-gray-800">{{ $item['titulo'] }}</p>
                            <div class="mt-1.5 flex flex-wrap items-center gap-2">
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $estadoClaseMovil }}">{{ $estadoTextoMovil }}</span>
                                @if ($rangoMovil)
                                    <span class="text-[11px] text-gray-400">{{ $rangoMovil }}</span>
                                @endif
                            </div>
                            @if ($item['detalle'])
                                <p class="text-xs text-gray-500 mt-1">{{ $item['detalle'] }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Sin hitos ni etapas cargados.</p>
                    @endforelse
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

                {{-- Pop-up con el detalle del hito o etapa --}}
                <div x-show="abierta" x-cloak class="fixed inset-0 overflow-y-auto" style="z-index: 9999" role="dialog" aria-modal="true" @keydown.escape.window="abierta = null">
                    <div class="fixed inset-0 bg-gray-900/60" @click="abierta = null"></div>
                    {{-- m-auto en la tarjeta: la centra cuando entra en pantalla y
                         permite scrollear hasta el borde cuando es mas alta --}}
                    <div class="min-h-full flex justify-center p-4">
                        {{-- h-fit: la tarjeta abraza su contenido; sin esto el flex la
                             estira hasta el alto del contenedor y sobra espacio abajo --}}
                        <div class="relative m-auto h-fit bg-white rounded-2xl shadow-xl w-full max-w-2xl p-5" x-show="abierta">
                            <button @click="abierta = null" class="absolute top-3 right-4 flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="Cerrar">
                                <x-heroicon-o-x-mark class="h-5 w-5" />
                            </button>
                            <template x-if="abierta">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                                            :class="abierta.tipo === 'hito' ? 'bg-amber-100 text-amber-700' : (abierta.tipo === 'resumen' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-50 text-emerald-700')"
                                            x-text="abierta.tipo === 'hito' ? 'Hito' : (abierta.tipo === 'resumen' ? 'Historial' : 'Etapa')"></span>
                                        <span class="text-xs text-gray-400" x-text="abierta.fecha_texto"></span>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-800" x-text="abierta.titulo"></h4>
                                    <span class="mt-2 inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full"
                                        :class="abierta.estado_clase"
                                        x-text="abierta.estado"></span>

                                    {{-- Nodo-resumen: lista el historial que quedo colapsado --}}
                                    <div x-show="abierta.historial" class="mt-3 space-y-2">
                                        <template x-for="(itemHistorial, idx) in (abierta.historial || [])" :key="idx">
                                            <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-100 bg-gray-50 px-3 py-2">
                                                <div class="min-w-0">
                                                    <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-full mr-2"
                                                        :class="itemHistorial.tipo === 'hito' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-50 text-emerald-700'"
                                                        x-text="itemHistorial.tipo === 'hito' ? 'Hito' : 'Etapa'"></span>
                                                    <span class="text-sm text-gray-800" x-text="itemHistorial.titulo"></span>
                                                </div>
                                                <span class="text-xs text-gray-400 shrink-0" x-text="itemHistorial.fecha_texto"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <p class="mt-3 text-sm text-gray-600 leading-relaxed" x-show="abierta.descripcion" x-text="abierta.descripcion"></p>

                                    <p class="mt-3 text-sm text-gray-600" x-show="abierta.fecha_fin_texto"
                                       x-text="'Etapa con inicio el ' + abierta.fecha_texto + ' y fin el ' + abierta.fecha_fin_texto"></p>

                                    {{-- Resumen del sprint redactado por el equipo con IA --}}
                                    <div x-show="abierta.resumen_ia" class="mt-3 rounded-xl border border-[#d7eee6] bg-[#f5fffb] p-4">
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-[#008c63] mb-1">Resumen del sprint para vos</p>
                                        <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed" x-text="abierta.resumen_ia"></p>
                                    </div>

                                    <div x-show="abierta.avance !== undefined && abierta.avance !== null" class="mt-3">
                                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                                            <span>Avance de la etapa</span>
                                            <span x-text="abierta.avance + '%'"></span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-2">
                                            <div class="bg-[#00b87d] h-2 rounded-full transition-all" :style="'width: ' + abierta.avance + '%'"></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Detalle del proyecto: las tres secciones en columnas iguales --}}
            <div class="grid gap-6 lg:grid-cols-3 lg:items-start">

            {{-- Novedades visibles para el cliente --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Novedades del equipo</h3>
                @forelse ($novedades as $novedad)
                    <div class="border-b last:border-0 border-gray-100 py-3">
                        <p class="text-sm font-medium text-gray-800">{{ $novedad->titulo }}</p>
                        <p class="text-sm text-gray-600 mt-0.5">{{ $novedad->descripcion }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $novedad->autor?->name ?? 'Equipo' }} · {{ $novedad->fecha?->format('d/m/Y') }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Sin novedades por ahora.</p>
                @endforelse
            </div>

            {{-- Cambios solicitados sobre este proyecto --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Cambios que pedimos</h3>
                @forelse ($cambios as $cambio)
                    <div class="border-b last:border-0 border-gray-100 py-3 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $cambio->titulo }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Pedida por {{ $cambio->solicitante?->name ?? '—' }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold shrink-0
                            {{ $cambio->estado === 'aprobada' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $cambio->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $cambio->estado === 'rechazada' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($cambio->estado) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Sin solicitudes de cambio para este proyecto.</p>
                @endforelse
            </div>

            {{-- Entregables aprobados --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Material aprobado para vos</h3>
                @forelse ($entregables as $entregable)
                    @php($valoresEntregable = ['titulo' => $entregable->titulo, 'contenido' => $entregable->contenido, 'tipo' => $entregable->tipo, 'estado' => $entregable->estado, 'proyecto' => $entregable->proyecto?->nombre, 'generador' => $entregable->generador?->name, 'fecha' => $entregable->generado_en?->format('d/m/Y')])
                    <div class="border-b last:border-0 border-gray-100 py-3 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $entregable->titulo }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ ucfirst($entregable->tipo) }} · {{ $entregable->generado_en?->format('d/m/Y') }}</p>
                        </div>
                        <button type="button" class="text-sm text-[#008c63] hover:underline shrink-0"
                                data-dispatch="ver-entregable" data-valores='@json($valoresEntregable)'>Ver</button>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Todavía no hay material aprobado para este proyecto.</p>
                @endforelse
            </div>
            </div>

        </div>
    </div>
    <x-entregable-view-modal />
</x-app-layout>
