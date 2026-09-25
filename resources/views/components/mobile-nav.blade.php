@php
    // El mismo menu centralizado de config/accesos.php que consume el
    // sidebar de escritorio; solo se filtran los que el rol no ve.
    $enlacesMenu = collect(\App\Support\Acceso::menu(auth()->user()))
        ->reject(fn ($e) => ($e['seccion'] ?? null) === 'Modulos')
        ->map(fn ($e) => [
            'ruta' => $e['ruta'],
            'label' => auth()->user()->esCliente() ? ($e['label_cliente'] ?? $e['label']) : $e['label'],
            'pattern' => str_replace('.index', '.*', $e['ruta']),
            'visible' => Route::has($e['ruta']) && ! (($e['interno'] ?? false) && auth()->user()?->esCliente()),
        ])
        ->values();

    $enlacesModulos = collect(\App\Support\Acceso::menu(auth()->user()))
        ->filter(fn ($e) => ($e['seccion'] ?? null) === 'Modulos')
        ->map(fn ($e) => [
            'ruta' => $e['ruta'],
            'label' => $e['label'],
            'pattern' => str_replace('.index', '.*', $e['ruta']),
            'visible' => Route::has($e['ruta']) && ! (($e['interno'] ?? false) && auth()->user()?->esCliente()),
        ])
        ->values();
@endphp

{{-- Menu lateral derecho para mobile: boton hamburguesa junto al logo + cajon con todos los elementos --}}
<div x-data="{ abierto: false }" {{ $attributes }}>
    {{-- Boton que abre el cajon (visible solo en mobile) --}}
    <button @click="abierto = true" class="rounded-lg p-2 text-slate-600 transition hover:bg-[#f0fff9] lg:hidden" aria-label="Abrir menú">
        <x-heroicon-o-bars-3-bottom-right class="h-6 w-6" />
    </button>

    {{-- Fondo oscurecido que cierra el cajon al tocarlo --}}
    <div x-show="abierto" x-cloak @click="abierto = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden" x-transition.opacity></div>

    {{-- Cajon lateral derecho --}}
    <aside x-show="abierto" x-cloak
           class="scroll-oscuro fixed inset-y-0 right-0 z-50 flex w-72 max-w-[85vw] flex-col overflow-y-auto bg-[#202225] px-4 py-5 text-slate-300 shadow-2xl lg:hidden"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full">

        <div class="flex items-center justify-between">
            <img src="{{ asset('images/cruznegra-logo.png') }}" alt="Cruz Negra" class="h-8 w-auto">
            <button @click="abierto = false" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-white/10 hover:text-white" aria-label="Cerrar menú">
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
        </div>

        <nav class="mt-6 space-y-1">
            @foreach ($enlacesMenu as $enlace)
                @if ($enlace['visible'])
                    <a href="{{ route($enlace['ruta']) }}" @click="abierto = false"
                       class="block rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs($enlace['pattern']) ? 'bg-[#00e5a0] text-[#17191b]' : 'hover:bg-white/10 hover:text-white' }}">
                        {{ $enlace['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        @if ($enlacesModulos->isNotEmpty())
            <p class="mt-8 px-3 text-[10px] font-bold uppercase tracking-widest text-slate-500">Módulos</p>
            <nav class="mt-2 space-y-1">
                @foreach ($enlacesModulos as $enlace)
                    @if ($enlace['visible'])
                        <a href="{{ route($enlace['ruta']) }}" @click="abierto = false"
                           class="block rounded-lg px-3 py-2.5 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs($enlace['pattern']) ? 'bg-white/10 text-white' : '' }}">
                            {{ $enlace['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        @endif

        {{-- Perfil y salida de sesión, como en el menu de usuario --}}
        <div class="mt-auto space-y-1 border-t border-white/10 pt-4">
            <a href="{{ route('profile.edit') }}" @click="abierto = false" class="block rounded-lg px-3 py-2.5 text-sm transition hover:bg-white/10 hover:text-white">
                Mi perfil
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-lg px-3 py-2.5 text-left text-sm text-red-400 transition hover:bg-white/10 hover:text-red-300">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>
</div>
