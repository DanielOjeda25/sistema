{{--
    Footer global del sitio, con el logo y los datos del sistema.
    Se incluye en todos los layouts (app, dashboard, guest y landing).
--}}
@props([])
<footer {{ $attributes->merge(['class' => 'shrink-0 border-t border-white/5 bg-[#101416] px-6 py-4 text-xs text-slate-400 sm:px-10']) }}>
    <div class="mx-auto flex max-w-[1280px] flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/cruznegra-logo-light.png') }}" alt="Cruz Negra" class="h-7 w-auto">
            <span class="hidden sm:inline text-slate-500">|</span>
            <span class="hidden sm:inline">Sistema de Gesti&oacute;n Interna</span>
        </div>
        <span>&copy; {{ date('Y') }} CRUZNEGRA &middot; Gesti&oacute;n de clientes, proyectos y facturaci&oacute;n</span>
    </div>
</footer>
