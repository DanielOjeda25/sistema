<x-app-layout>
    @php
        /** @var \App\Models\User $usuario */
        $usuario = Auth::user();
        $rol = $usuario->getRoleNames()->first() ?? 'Usuario';
        $nombre = Str::of($usuario->name)->before(' ');
        $interno = ! $esCliente;
    @endphp

    <div class="min-h-screen bg-[#d9fff1] text-[#17191b]">
        <div class="flex h-screen w-full overflow-hidden bg-white shadow-sm">
            <aside x-data="{ colapsado: localStorage.getItem('sidebar-colapsado') === '1' }"
                   class="scroll-oscuro hidden shrink-0 overflow-y-auto bg-[#202225] text-slate-300 transition-all duration-200 lg:block"
                   :class="colapsado ? 'w-[68px] px-2' : 'w-60 px-4'">
                <div class="py-5" :class="colapsado ? 'px-0' : 'px-3'">
                    <a href="{{ route('dashboard') }}" class="block border-b border-white/10 pb-6" :class="colapsado ? 'flex justify-center' : ''">
                        <img src="{{ asset('images/cruznegra-logo-light.png') }}" alt="Cruz Negra" class="object-contain object-center" :class="colapsado ? 'h-9 w-9' : 'h-14 w-full'">
                    </a>
                </div>

@php($menuAccesos = \App\Support\Acceso::menu($usuario))
                <nav class="space-y-1" :class="colapsado ? '' : 'mt-2'">
                    @foreach ($menuAccesos as $enlace)
                        @continue(isset($enlace['seccion']))
                        @php($etiqueta = $usuario->esCliente() ? ($enlace['label_cliente'] ?? $enlace['label']) : $enlace['label'])
                        <a href="{{ route($enlace['ruta']) }}" title="{{ $etiqueta }}"
                           class="flex items-center rounded-lg border-l-4 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs(str_replace('.index', '.*', $enlace['ruta'])) ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }} {{ request()->routeIs('dashboard') ? '!border-[#00e5a0] !bg-white/10 !text-white' : '' }}"
                           :class="colapsado ? 'justify-center border-l-0 px-2 py-2.5' : 'gap-3 px-3 py-2.5'">
                            @if ($enlace['icono'])
                                <x-dynamic-component :component="$enlace['icono']" class="h-5 w-5 shrink-0" />
                            @endif
                            <span x-show="!colapsado">{{ $etiqueta }}</span>
                        </a>
                    @endforeach
                </nav>

                @if (collect($menuAccesos)->contains(fn ($e) => ($e['seccion'] ?? null) === 'Modulos'))
                <p class="mt-8 px-3 text-[10px] font-bold uppercase tracking-widest text-slate-500" x-show="!colapsado">Módulos</p>
                <div class="mt-8 border-t border-white/10" x-show="colapsado" x-cloak></div>
                {{-- Modulos en grilla 2x2 cuando el menu esta expandido --}}
                <nav class="mt-2 grid gap-1" x-bind:class="colapsado ? 'grid-cols-1' : 'grid-cols-2'">
                    @foreach ($menuAccesos as $enlace)
                        @continue(($enlace['seccion'] ?? null) !== 'Modulos')
                        <a href="{{ route($enlace['ruta']) }}" title="{{ $enlace['label'] }}"
                           class="flex items-center gap-2 rounded-lg border-l-4 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs(str_replace('.index', '.*', $enlace['ruta'])) ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}"
                           :class="colapsado ? 'justify-center border-l-0 px-2 py-2.5' : 'px-2.5 py-2 text-xs'">
                            @if ($enlace['icono'])
                                <x-dynamic-component :component="$enlace['icono']" class="h-5 w-5 shrink-0" />
                            @endif
                            <span x-show="!colapsado">{{ $enlace['label'] }}</span>
                        </a>
                    @endforeach
                </nav>
                @endif

                {{-- Boton para colapsar / expandir el menu --}}
                <div class="mt-6 border-t border-white/10 pt-4" :class="colapsado ? 'flex justify-center' : ''">
                    <button @click="colapsado = !colapsado; localStorage.setItem('sidebar-colapsado', colapsado ? '1' : '0')"
                            class="rounded-lg p-2 text-slate-400 transition hover:bg-white/10 hover:text-white"
                            :title="colapsado ? 'Expandir menú' : 'Colapsar menú'"
                            aria-label="Colapsar menú">
                        <x-heroicon-o-chevron-double-left class="h-5 w-5 transition-transform" x-bind:class="colapsado ? 'rotate-180' : ''" />
                    </button>
                </div>

            </aside>

            <main class="flex min-w-0 flex-1 flex-col overflow-hidden bg-[#f5fffb]">
                <header class="flex shrink-0 items-center justify-between border-b border-[#d7eee6] bg-white px-5 py-4 sm:px-8">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard') }}" class="lg:hidden">
                            <img src="{{ asset('images/cruznegra-logo.png') }}" alt="Cruz Negra" class="h-8 w-24 translate-x-1 object-contain object-left">
                        </a>
                        <x-mobile-nav class="lg:hidden" />
                        <span class="hidden text-xs text-slate-400 sm:inline">{{ now()->translatedFormat('d \d\e F \d\e Y') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-slate-600 transition hover:bg-[#f0fff9] focus:outline-none">
                                    <span class="hidden text-right sm:block"><span class="block text-xs font-semibold text-slate-700">{{ $usuario->name }}</span><span class="block text-[11px] text-slate-400">{{ $rol }}</span></span>
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#c9fbe8] text-xs font-bold text-[#008c63]">{{ Str::upper(Str::substr($nombre, 0, 1)) }}</span>
                                    <x-heroicon-o-chevron-down class="h-4 w-4 shrink-0" />
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar sesión</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                <div class="scroll-suave flex w-full flex-1 flex-col space-y-5 overflow-y-auto p-5 sm:p-8 animar-entrada">
                    <div>
                        <section class="rounded-xl border border-[#d7eee6] bg-white p-5 shadow-sm">
                            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                                <div>
                                    <p class="text-xs font-medium text-slate-400">Resumen general</p>
                                    <p class="mt-1 text-3xl font-bold text-slate-900">
                                        @if ($usuario->esCliente())
                                            {{ $totalProyectos + $totalHitos + $totalEntregables }}
                                        @else
                                            {{ $totalProyectos + $tareasPendientes + $totalHitos }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        @if ($usuario->esCliente())
                                            proyectos, hitos y material para vos
                                        @else
                                            elementos registrados para seguimiento
                                        @endif
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    @if ($usuario->esCliente())
                                    <a href="{{ route('entregables.index') }}" class="rounded-lg bg-[#00b87d] px-3 py-2 text-xs font-semibold text-white hover:bg-[#008c63]">Ver entregables</a>
                                    @else
                                    <a href="{{ route('tareas.tablero') }}" class="rounded-lg bg-[#00b87d] px-3 py-2 text-xs font-semibold text-white hover:bg-[#008c63]">Abrir tablero</a>
                                    @endif
                                    <a href="{{ route('proyectos.index') }}" class="rounded-lg border border-[#d7eee6] px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-[#f0fff9]">Ver proyectos</a>
                                </div>
                            </div>

                            <div class="mt-5 divide-y divide-[#edf7f3] rounded-lg border border-[#d7eee6]">
                            <a href="{{ route('proyectos.index') }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#f0fff9]">
                                <div class="flex items-center gap-3"><span class="h-2.5 w-2.5 rounded-full bg-[#00d99a]"></span><span><span class="block text-sm font-medium text-slate-700">Proyectos</span><span class="block text-xs text-slate-400">{{ $totalProyectos }} registrados</span></span></div>
                                <span class="text-sm font-semibold text-slate-700">{{ $totalProyectos }}</span>
                            </a>
                            @unless ($usuario->esCliente())
                            <a href="{{ route('tareas.index', ['estado' => 'pendiente']) }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#f0fff9]">
                                <div class="flex items-center gap-3"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span><span><span class="block text-sm font-medium text-slate-700">Tareas pendientes</span><span class="block text-xs text-slate-400">Requieren seguimiento</span></span></div>
                                <span class="text-sm font-semibold text-slate-700">{{ $tareasPendientes }}</span>
                            </a>
                            @endunless
                            @if ($usuario->esCliente())
                            <a href="{{ route('facturas.index') }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#f0fff9]">
                                <div class="flex items-center gap-3"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span><span><span class="block text-sm font-medium text-slate-700">Facturas pendientes de pago</span><span class="block text-xs text-slate-400">Tenés {{ $facturasPendientes }} en curso</span></span></div>
                                <span class="text-sm font-semibold text-slate-700">{{ $facturasPendientes }}</span>
                            </a>
                            @endif
                            <a href="{{ route('hitos.index') }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#f0fff9]">
                                <div class="flex items-center gap-3"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span><span><span class="block text-sm font-medium text-slate-700">Hitos</span><span class="block text-xs text-slate-400">Puntos de control</span></span></div>
                                <span class="text-sm font-semibold text-slate-700">{{ $totalHitos }}</span>
                            </a>
                            <a href="{{ route('entregables.index') }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#f0fff9]">
                                <div class="flex items-center gap-3"><span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span><span><span class="block text-sm font-medium text-slate-700">Entregables</span><span class="block text-xs text-slate-400">{{ $usuario->esCliente() ? 'Material aprobado para vos' : 'Material del proyecto' }}</span></span></div>
                                <span class="text-sm font-semibold text-slate-700">{{ $totalEntregables }}</span>
                            </a>
                            </div>
                        </section>

                        @if ($interno)
                            <section class="mt-5 grid gap-5 xl:grid-cols-2">
                                <div class="rounded-xl border border-[#d7eee6] bg-white p-5 shadow-sm">
                                    <div class="flex items-center justify-between"><div><h2 class="font-semibold text-slate-800">Estado de proyectos</h2><p class="mt-1 text-xs text-slate-400">Distribución actual</p></div><a href="{{ route('proyectos.index') }}" class="text-xs font-semibold text-[#009d70]">Ver todo</a></div>
                                    <div class="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-100">
                                        @php($maxProyectos = max($proyectosPorEstado) ?: 1)
                                        @foreach (['pendiente' => ['Pendientes', 'bg-slate-100 text-slate-700'], 'en_progreso' => ['En progreso', 'bg-blue-50 text-blue-700'], 'completado' => ['Completados', 'bg-emerald-50 text-emerald-700'], 'cancelado' => ['Cancelados', 'bg-rose-50 text-rose-700']] as $estado => [$label, $style])
                                            <a href="{{ route('proyectos.index', ['estado' => $estado]) }}" class="block px-4 py-2.5 hover:bg-[#f0fff9]">
                                                <div class="flex items-center justify-between"><span class="text-sm text-slate-700">{{ $label }}</span><span class="{{ $style }} rounded-md px-2 py-0.5 text-xs font-bold">{{ $proyectosPorEstado[$estado] }}</span></div>
                                                <div class="mt-1.5 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-[#00b87d] rounded-full animar-alto" style="width: {{ round($proyectosPorEstado[$estado] / $maxProyectos * 100) }}%"></div></div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="rounded-xl border border-[#d7eee6] bg-white p-5 shadow-sm">
                                    <div class="flex items-center justify-between"><div><h2 class="font-semibold text-slate-800">Estado de tareas</h2><p class="mt-1 text-xs text-slate-400">Rendimiento del equipo</p></div><a href="{{ route('tareas.index') }}" class="text-xs font-semibold text-[#009d70]">Ver todo</a></div>
                                    <div class="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-100">
                                        @php($maxTareas = max($tareasPorEstado) ?: 1)
                                        @foreach (['pendiente' => ['Pendientes', 'bg-slate-100 text-slate-700'], 'en_progreso' => ['En progreso', 'bg-blue-50 text-blue-700'], 'completada' => ['Completadas', 'bg-emerald-50 text-emerald-700'], 'cancelada' => ['Canceladas', 'bg-rose-50 text-rose-700']] as $estado => [$label, $style])
                                            <a href="{{ route('tareas.index', ['estado' => $estado]) }}" class="block px-4 py-2.5 hover:bg-[#f0fff9]">
                                                <div class="flex items-center justify-between"><span class="text-sm text-slate-700">{{ $label }}</span><span class="{{ $style }} rounded-md px-2 py-0.5 text-xs font-bold">{{ $tareasPorEstado[$estado] }}</span></div>
                                                <div class="mt-1.5 h-1.5 bg-slate-100 rounded-full overflow-hidden"><div class="h-full bg-[#00b87d] rounded-full animar-alto" style="width: {{ round($tareasPorEstado[$estado] / $maxTareas * 100) }}%"></div></div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="divide-y divide-[#edf7f3] rounded-xl border border-[#d7eee6] bg-white shadow-sm xl:col-span-2">
                                    <a href="{{ route('facturas.index') }}" class="flex items-center justify-between px-5 py-4 transition hover:bg-[#f0fff9]"><span><span class="block text-sm font-medium text-slate-700">Total facturado</span><span class="block text-xs text-slate-400">Ver facturas</span></span><span class="text-lg font-bold text-[#009d70]">$ {{ number_format($totalFacturado, 2, ',', '.') }}</span></a>
                                    <a href="{{ route('facturas.index') }}" class="flex items-center justify-between px-5 py-4 transition hover:bg-[#f0fff9]"><span><span class="block text-sm font-medium text-slate-700">Pendiente de cobro</span><span class="block text-xs text-slate-400">Facturas pendientes y vencidas</span></span><span class="text-lg font-bold text-[#d97706]">$ {{ number_format($totalPendienteCobro, 2, ',', '.') }}</span></a>
                                    <a href="{{ route('tareas.index', ['estado' => 'pendiente']) }}" class="flex items-center justify-between px-5 py-4 transition hover:bg-[#f0fff9]"><span><span class="block text-sm font-medium text-slate-700">Tareas vencidas</span><span class="block text-xs text-slate-400">Requieren atención</span></span><span class="text-lg font-bold text-[#dc2626]">{{ $tareasVencidas }}</span></a>
                                </div>

                                <div class="rounded-xl border border-[#d7eee6] bg-white p-5 shadow-sm xl:col-span-2">
                                    <div class="flex items-center justify-between"><h2 class="font-semibold text-slate-800">Facturación por mes</h2><p class="text-xs text-slate-400">Últimos 6 meses</p></div>
                                    @php($maxMes = max($facturacionPorMes->pluck('total')->max(), 1))
                                    <div class="mt-4 flex items-end gap-3 h-36">
                                        @foreach ($facturacionPorMes as $m)
                                            <div class="flex flex-1 flex-col items-center justify-end h-full">
                                                <span class="text-[10px] text-slate-500 mb-1">{{ $m['total'] > 0 ? '$ ' . number_format($m['total'] / 1000, 0) . 'k' : '—' }}</span>
                                                <div class="w-full bg-[#00b87d] rounded-t-md animar-alto" style="height: {{ max(3, round($m['total'] / $maxMes * 100)) }}%"></div>
                                                <span class="text-[10px] text-slate-400 mt-1">{{ $m['mes'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </section>

                            <style>
                                @keyframes crecerAlto { from { height: 3%; } }
                                .animar-alto { animation: crecerAlto 1s ease-out; }
                            </style>
                        @endif

@if ($interno && isset($hitosProximos) && $hitosProximos->isNotEmpty())
    <section class="mt-5 rounded-xl border border-[#d7eee6] bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#d7eee6]">
            <div>
                <h2 class="font-semibold text-slate-800">Hitos por vencer</h2>
                <p class="mt-1 text-xs text-slate-400">Puntos de control próximos</p>
            </div>
            <a href="{{ route('hitos.index') }}" class="text-xs font-semibold text-[#009d70]">Ver todo</a>
        </div>
        <ul class="divide-y divide-[#edf7f3]">
            @foreach ($hitosProximos as $hito)
                <li class="flex items-center justify-between px-5 py-3.5">
                    <div>
                        <p class="text-sm font-medium text-slate-700">{{ $hito->nombre }}</p>
                        <p class="text-xs text-slate-400">{{ $hito->proyecto?->nombre }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $hito->fecha_objetivo->isPast() ? 'bg-red-100 text-red-700 font-bold' : 'bg-amber-100 text-amber-800' }}">
                        @if ($hito->fecha_objetivo->isPast())
                            VENCIDO
                        @else
                            {{ $hito->fecha_objetivo->diffForHumans() }}
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    </section>
@endif

                    </div>
                    <x-footer-sitio class="mt-auto !bg-[#202225] !border-white/10" />
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
