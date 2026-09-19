@php
    // El mismo menu centralizado de config/accesos.php que consume el
    // sidebar de escritorio; solo se filtran los que el rol no ve.
    $enlaces = collect(\App\Support\Acceso::menu(auth()->user()))
        ->reject(fn ($e) => ($e['seccion'] ?? null) === 'Modulos')
        ->map(fn ($e) => [
            'route' => $e['ruta'],
            'label' => auth()->user()->esCliente() ? ($e['label_cliente'] ?? $e['label']) : $e['label'],
            'pattern' => str_replace('.index', '.*', $e['ruta']),
        ])
        ->values()
        ->all();
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
