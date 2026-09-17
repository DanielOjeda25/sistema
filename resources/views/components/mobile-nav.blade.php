@php
    // interno = planificacion del equipo: no se muestra al rol Cliente.
    $enlaces = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'pattern' => 'dashboard'],
        ['route' => 'proyectos.index', 'label' => 'Proyectos', 'pattern' => 'proyectos.*'],
        ['route' => 'tareas.tablero', 'label' => 'Mi trabajo', 'pattern' => 'tareas.tablero', 'interno' => true],
        ['route' => 'tareas.index', 'label' => 'Tareas', 'pattern' => 'tareas.*', 'interno' => true],
        ['route' => 'facturas.index', 'label' => 'Facturas', 'pattern' => 'facturas.*'],
        ['route' => 'sprints.index', 'label' => 'Sprints', 'pattern' => 'sprints.*', 'interno' => true],
        ['route' => 'hitos.index', 'label' => 'Hitos', 'pattern' => 'hitos.*'],
        ['route' => 'entregables.index', 'label' => 'Entregables', 'pattern' => 'entregables.*'],
    ];
@endphp

<nav {{ $attributes->merge(['class' => 'scroll-oculto flex shrink-0 gap-1 overflow-x-auto border-b border-[#d7eee6] bg-[#202225] px-3 py-2 lg:hidden']) }}>
    @foreach ($enlaces as $enlace)
        @if (Route::has($enlace['route']) && ! (($enlace['interno'] ?? false) && auth()->user()?->esCliente()))
            <a href="{{ route($enlace['route']) }}"
               class="shrink-0 rounded-lg px-3 py-1.5 text-xs font-medium transition {{ request()->routeIs($enlace['pattern']) ? 'bg-[#00e5a0] text-[#17191b]' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                {{ $enlace['label'] }}
            </a>
        @endif
    @endforeach
</nav>
