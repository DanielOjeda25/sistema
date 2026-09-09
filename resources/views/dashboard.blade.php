<x-app-layout>
    @php
        $usuario = Auth::user();
        $rol = $usuario->getRoleNames()->first() ?? 'Usuario';
        $nombre = Str::of($usuario->name)->before(' ');
        $interno = ! $esCliente;
    @endphp

    <div class="min-h-[calc(100vh-4rem)] bg-[#d9fff1] text-[#17191b]">
        <div class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-[1240px] overflow-hidden rounded-none border-x border-[#bcebd9] bg-white shadow-sm lg:my-5 lg:min-h-[calc(100vh-6.5rem)] lg:rounded-2xl">
            <aside class="hidden w-60 shrink-0 bg-[#202225] px-4 py-5 text-slate-300 lg:block">
                <a href="{{ route('dashboard') }}" class="block border-b border-white/10 px-3 pb-6">
                    <span class="block overflow-hidden rounded-lg bg-white p-1"><img src="{{ asset('images/cruznegra-logo.png') }}" alt="Cruz Negra" class="h-16 w-full translate-x-1 object-contain object-left"></span>
                </a>

                <nav class="mt-6 space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg border-l-4 border-[#00e5a0] bg-white/10 px-3 py-2.5 text-sm font-semibold text-white">
                        <span>⌂</span> Dashboard
                    </a>
                    <a href="{{ route('proyectos.index') }}" class="flex items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2.5 text-sm transition hover:border-[#00e5a0] hover:bg-white/10 hover:text-white {{ request()->routeIs('proyectos.*') ? 'border-[#00e5a0] bg-white/10 text-white' : '' }}">
                        <span>▦</span> Proyectos
                    </a>
                    <a href="{{ route('tareas.tablero') }}" class="flex items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2.5 text-sm transition hover:border-[#00e5a0] hover:bg-white/10 hover:text-white {{ request()->routeIs('tareas.tablero') ? 'border-[#00e5a0] bg-white/10 text-white' : '' }}">
                        <span>✓</span> Mi trabajo
                    </a>
                    <a href="{{ route('tareas.index') }}" class="flex items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2.5 text-sm transition hover:border-[#00e5a0] hover:bg-white/10 hover:text-white {{ request()->routeIs('tareas.*') && ! request()->routeIs('tareas.tablero') ? 'border-[#00e5a0] bg-white/10 text-white' : '' }}">
                        <span>☷</span> Tareas
                    </a>
                    <a href="{{ route('facturas.index') }}" class="flex items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2.5 text-sm transition hover:border-[#00e5a0] hover:bg-white/10 hover:text-white {{ request()->routeIs('facturas.*') ? 'border-[#00e5a0] bg-white/10 text-white' : '' }}">
                        <span>$</span> Facturas
                    </a>
                </nav>

                <p class="mt-8 px-3 text-[10px] font-bold uppercase tracking-widest text-slate-500">Módulos</p>
                <nav class="mt-2 space-y-1">
                    <a href="{{ route('sprints.index') }}" class="block rounded-lg border-l-4 border-transparent px-3 py-2 text-sm transition hover:border-[#00e5a0] hover:bg-white/10 hover:text-white {{ request()->routeIs('sprints.*') ? 'border-[#00e5a0] bg-white/10 text-white' : '' }}">Sprints</a>
                    <a href="{{ route('hitos.index') }}" class="block rounded-lg border-l-4 border-transparent px-3 py-2 text-sm transition hover:border-[#00e5a0] hover:bg-white/10 hover:text-white {{ request()->routeIs('hitos.*') ? 'border-[#00e5a0] bg-white/10 text-white' : '' }}">Hitos</a>
                    <a href="{{ route('entregables.index') }}" class="block rounded-lg border-l-4 border-transparent px-3 py-2 text-sm transition hover:border-[#00e5a0] hover:bg-white/10 hover:text-white {{ request()->routeIs('entregables.*') ? 'border-[#00e5a0] bg-white/10 text-white' : '' }}">Entregables</a>
                </nav>

                <div class="mt-10 border-t border-white/10 pt-4">
                    <p class="px-3 text-xs font-semibold text-white">{{ $usuario->name }}</p>
                    <p class="px-3 pt-1 text-[11px] text-slate-500">{{ $rol }}</p>
                    <a href="{{ route('profile.edit') }}" class="mt-4 flex items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2 text-sm transition hover:border-[#00e5a0] hover:bg-white/10 hover:text-white {{ request()->routeIs('profile.*') ? 'border-[#00e5a0] bg-white/10 text-white' : '' }}">Perfil</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2 text-left text-sm transition hover:border-[#00e5a0] hover:bg-white/10 hover:text-white">Cerrar sesión</button>
                    </form>
                </div>
            </aside>

            <main class="min-w-0 flex-1 bg-[#f5fffb]">
                <header class="flex items-center justify-between border-b border-[#d7eee6] bg-white px-5 py-4 sm:px-8">
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-400">{{ now()->translatedFormat('d \d\e F \d\e Y') }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden text-right sm:block"><span class="block text-xs font-semibold text-slate-700">{{ $usuario->name }}</span><span class="block text-[11px] text-slate-400">{{ $rol }}</span></span>
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#c9fbe8] text-xs font-bold text-[#008c63]">{{ Str::upper(Str::substr($nombre, 0, 1)) }}</span>
                    </div>
                </header>

                <div class="mx-auto max-w-4xl space-y-5 p-5 sm:p-8">
                    <section class="rounded-xl border border-[#d7eee6] bg-white px-5 py-4 shadow-sm">
                        <p class="text-xs text-slate-400">Resumen de actividad</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Buen día, {{ $nombre }}.</h1>
                    </section>

                    <div class="max-w-3xl">
                        <section class="rounded-xl border border-[#d7eee6] bg-white p-5 shadow-sm">
                            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                                <div>
                                    <p class="text-xs font-medium text-slate-400">Resumen general</p>
                                    <p class="mt-1 text-3xl font-bold text-slate-900">{{ $totalProyectos + $tareasPendientes + $totalHitos }}</p>
                                    <p class="text-xs text-slate-500">elementos registrados para seguimiento</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('tareas.tablero') }}" class="rounded-lg bg-[#00b87d] px-3 py-2 text-xs font-semibold text-white hover:bg-[#008c63]">Abrir tablero</a>
                                    <a href="{{ route('proyectos.index') }}" class="rounded-lg border border-[#d7eee6] px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-[#f0fff9]">Ver proyectos</a>
                                </div>
                            </div>

                            <div class="mt-5 divide-y divide-[#edf7f3] rounded-lg border border-[#d7eee6]">
                            <a href="{{ route('proyectos.index') }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#f0fff9]">
                                <div class="flex items-center gap-3"><span class="h-2.5 w-2.5 rounded-full bg-[#00d99a]"></span><span><span class="block text-sm font-medium text-slate-700">Proyectos</span><span class="block text-xs text-slate-400">{{ $totalProyectos }} registrados</span></span></div>
                                <span class="text-sm font-semibold text-slate-700">{{ $totalProyectos }}</span>
                            </a>
                            <a href="{{ route('tareas.index', ['estado' => 'pendiente']) }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#f0fff9]">
                                <div class="flex items-center gap-3"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span><span><span class="block text-sm font-medium text-slate-700">Tareas pendientes</span><span class="block text-xs text-slate-400">Requieren seguimiento</span></span></div>
                                <span class="text-sm font-semibold text-slate-700">{{ $tareasPendientes }}</span>
                            </a>
                            <a href="{{ route('hitos.index') }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#f0fff9]">
                                <div class="flex items-center gap-3"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span><span><span class="block text-sm font-medium text-slate-700">Hitos</span><span class="block text-xs text-slate-400">Puntos de control</span></span></div>
                                <span class="text-sm font-semibold text-slate-700">{{ $totalHitos }}</span>
                            </a>
                            <a href="{{ route('entregables.index') }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#f0fff9]">
                                <div class="flex items-center gap-3"><span class="h-2.5 w-2.5 rounded-full bg-sky-500"></span><span><span class="block text-sm font-medium text-slate-700">Entregables</span><span class="block text-xs text-slate-400">Material del proyecto</span></span></div>
                                <span class="text-sm font-semibold text-slate-700">{{ $totalEntregables }}</span>
                            </a>
                            </div>
                        </section>

                        @if ($interno)
                            <section class="mt-5 space-y-5">
                                <div class="rounded-xl border border-[#d7eee6] bg-white p-5 shadow-sm">
                                    <div class="flex items-center justify-between"><div><h2 class="font-semibold text-slate-800">Estado de proyectos</h2><p class="mt-1 text-xs text-slate-400">Distribución actual</p></div><a href="{{ route('proyectos.index') }}" class="text-xs font-semibold text-[#009d70]">Ver todo</a></div>
                                    <div class="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-100">
                                        @foreach (['pendiente' => ['Pendientes', 'bg-slate-100 text-slate-700'], 'en_progreso' => ['En progreso', 'bg-blue-50 text-blue-700'], 'completado' => ['Completados', 'bg-emerald-50 text-emerald-700'], 'cancelado' => ['Cancelados', 'bg-rose-50 text-rose-700']] as $estado => [$label, $style])
                                            <a href="{{ route('proyectos.index', ['estado' => $estado]) }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#f0fff9]"><span class="text-sm text-slate-700">{{ $label }}</span><span class="{{ $style }} rounded-md px-2 py-1 text-xs font-bold">{{ $proyectosPorEstado[$estado] }}</span></a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="rounded-xl border border-[#d7eee6] bg-white p-5 shadow-sm">
                                    <div class="flex items-center justify-between"><div><h2 class="font-semibold text-slate-800">Estado de tareas</h2><p class="mt-1 text-xs text-slate-400">Rendimiento del equipo</p></div><a href="{{ route('tareas.index') }}" class="text-xs font-semibold text-[#009d70]">Ver todo</a></div>
                                    <div class="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-100">
                                        @foreach (['pendiente' => ['Pendientes', 'bg-slate-100 text-slate-700'], 'en_progreso' => ['En progreso', 'bg-blue-50 text-blue-700'], 'completada' => ['Completadas', 'bg-emerald-50 text-emerald-700'], 'cancelada' => ['Canceladas', 'bg-rose-50 text-rose-700']] as $estado => [$label, $style])
                                            <a href="{{ route('tareas.index', ['estado' => $estado]) }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#f0fff9]"><span class="text-sm text-slate-700">{{ $label }}</span><span class="{{ $style }} rounded-md px-2 py-1 text-xs font-bold">{{ $tareasPorEstado[$estado] }}</span></a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="divide-y divide-[#edf7f3] rounded-xl border border-[#d7eee6] bg-white shadow-sm">
                                    <a href="{{ route('facturas.index') }}" class="flex items-center justify-between px-5 py-4 transition hover:bg-[#f0fff9]"><span><span class="block text-sm font-medium text-slate-700">Total facturado</span><span class="block text-xs text-slate-400">Ver facturas</span></span><span class="text-lg font-bold text-[#009d70]">$ {{ number_format($totalFacturado, 2, ',', '.') }}</span></a>
                                    <a href="{{ route('facturas.index') }}" class="flex items-center justify-between px-5 py-4 transition hover:bg-[#f0fff9]"><span><span class="block text-sm font-medium text-slate-700">Pendiente de cobro</span><span class="block text-xs text-slate-400">Facturas pendientes y vencidas</span></span><span class="text-lg font-bold text-[#d97706]">$ {{ number_format($totalPendienteCobro, 2, ',', '.') }}</span></a>
                                    <a href="{{ route('tareas.index', ['estado' => 'pendiente']) }}" class="flex items-center justify-between px-5 py-4 transition hover:bg-[#f0fff9]"><span><span class="block text-sm font-medium text-slate-700">Tareas vencidas</span><span class="block text-xs text-slate-400">Requieren atención</span></span><span class="text-lg font-bold text-[#dc2626]">{{ $tareasVencidas }}</span></a>
                                </div>
                            </section>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
