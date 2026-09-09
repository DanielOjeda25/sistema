<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="32x32">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @if (request()->routeIs('dashboard'))
            {{ $slot }}
        @else
            <div class="min-h-screen bg-[#d9fff1] text-[#17191b]">
                <div class="flex h-screen w-full overflow-hidden bg-white shadow-sm">
                    <aside class="scroll-oscuro hidden w-60 shrink-0 overflow-y-auto bg-[#202225] px-4 py-5 text-slate-300 lg:block">
                        <a href="{{ route('dashboard') }}" class="block border-b border-white/10 px-3 pb-6">
                            <img src="{{ asset('images/cruznegra-logo-light.png') }}" alt="Cruz Negra" class="h-14 w-full object-contain object-left">
                        </a>

                        <nav class="mt-6 space-y-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs('dashboard') ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}"><x-heroicon-o-home class="h-5 w-5 shrink-0" /> Dashboard</a>
                            @hasanyrole('Jefe|PM')
                                <a href="{{ route('users.index') }}" class="flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs('users.*') ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}"><x-heroicon-o-users class="h-5 w-5 shrink-0" /> Usuarios y roles</a>
                            @endhasanyrole
                            <a href="{{ route('proyectos.index') }}" class="flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs('proyectos.*') ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}"><x-heroicon-o-squares-2x2 class="h-5 w-5 shrink-0" /> Proyectos</a>
                            <a href="{{ route('tareas.tablero') }}" class="flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs('tareas.tablero') ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}"><x-heroicon-o-check-circle class="h-5 w-5 shrink-0" /> Mi trabajo</a>
                            <a href="{{ route('tareas.index') }}" class="flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs('tareas.*') && ! request()->routeIs('tareas.tablero') ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}"><x-heroicon-o-queue-list class="h-5 w-5 shrink-0" /> Tareas</a>
                            <a href="{{ route('facturas.index') }}" class="flex items-center gap-3 rounded-lg border-l-4 px-3 py-2.5 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs('facturas.*') ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}"><x-heroicon-o-banknotes class="h-5 w-5 shrink-0" /> Facturas</a>
                        </nav>

                        <p class="mt-8 px-3 text-[10px] font-bold uppercase tracking-widest text-slate-500">Módulos</p>
                        <nav class="mt-2 space-y-1">
                            <a href="{{ route('sprints.index') }}" class="block rounded-lg border-l-4 px-3 py-2 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs('sprints.*') ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}">Sprints</a>
                            <a href="{{ route('hitos.index') }}" class="block rounded-lg border-l-4 px-3 py-2 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs('hitos.*') ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}">Hitos</a>
                            <a href="{{ route('solicitudes-cambio.index') }}" class="block rounded-lg border-l-4 px-3 py-2 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs('solicitudes-cambio.*') ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}">Cambios</a>
                            <a href="{{ route('entregables.index') }}" class="block rounded-lg border-l-4 px-3 py-2 text-sm transition hover:bg-white/10 hover:text-white {{ request()->routeIs('entregables.*') ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}">Entregables</a>
                        </nav>

                        <div class="mt-10 border-t border-white/10 pt-4">
                            <p class="px-3 text-xs font-semibold text-white">{{ Auth::user()->name }}</p>
                            <p class="px-3 pt-1 text-[11px] text-slate-500">{{ Auth::user()->getRoleNames()->first() ?? 'Usuario' }}</p>
                            <a href="{{ route('profile.edit') }}" class="mt-4 flex items-center gap-3 rounded-lg border-l-4 px-3 py-2 text-sm transition hover:border-[#00e5a0] hover:bg-white/10 hover:text-white {{ request()->routeIs('profile.*') ? 'border-[#00e5a0] bg-white/10 text-white' : 'border-transparent' }}">Perfil</a>
                            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-lg border-l-4 border-transparent px-3 py-2 text-left text-sm transition hover:border-[#00e5a0] hover:bg-white/10 hover:text-white">Cerrar sesión</button>
                            </form>
                        </div>
                    </aside>

                    <main class="flex min-w-0 flex-1 flex-col overflow-hidden bg-[#f5fffb]">
                        <header class="flex shrink-0 items-center justify-between border-b border-[#d7eee6] bg-white px-5 py-4 sm:px-8">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('dashboard') }}" class="lg:hidden">
                                    <img src="{{ asset('images/cruznegra-logo.png') }}" alt="Cruz Negra" class="h-8 w-24 translate-x-1 object-contain object-left">
                                </a>
                                <span class="hidden text-xs text-slate-400 sm:inline">{{ now()->translatedFormat('d \d\e F \d\e Y') }}</span>
                            </div>
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm text-slate-600 transition hover:bg-[#f0fff9] focus:outline-none">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#c9fbe8] text-xs font-bold text-[#008c63]">{{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}</span>
                                        <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                                        <x-heroicon-o-chevron-down class="h-4 w-4 shrink-0" />
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </header>

                        <x-mobile-nav class="shrink-0" />

                        @isset($header)
                            <div class="shrink-0 border-b border-[#d7eee6] bg-white px-5 py-5 sm:px-8">
                                {{ $header }}
                            </div>
                        @endisset

                        <div class="scroll-suave flex-1 overflow-y-auto p-5 sm:p-8">
                            {{ $slot }}
                        </div>
                    </main>
                </div>
            </div>
        @endif
    </body>
</html>
