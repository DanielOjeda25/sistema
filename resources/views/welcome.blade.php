<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'CRUZNEGRA') }} · Sistema de Gestión</title>
        <link rel="icon" href="/favicon.ico" sizes="32x32">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#101416] font-sans text-white antialiased">
        <main class="min-h-screen bg-[radial-gradient(circle_at_75%_45%,#202a2d_0%,#101416_42%,#0b0d0e_100%)]">
            <header class="border-b border-white/15 px-6 py-5 sm:px-10 lg:px-16">
                <div class="mx-auto flex max-w-[1280px] items-center justify-between">
                    <a href="/" class="flex items-center gap-3">
                        <span class="relative flex h-8 w-8 items-center justify-center text-3xl font-light leading-none text-white">
                            <span class="absolute h-8 w-2 bg-white"></span>
                            <span class="absolute h-2 w-8 bg-white"></span>
                        </span>
                        <span class="text-xl font-bold tracking-tight">CRUZNEGRA</span>
                    </a>
                </div>
            </header>

            <section class="mx-auto grid min-h-[calc(100vh-81px)] max-w-[1280px] items-center gap-12 px-6 py-14 sm:px-10 lg:grid-cols-[0.95fr_1.05fr] lg:gap-16 lg:px-16 lg:py-20">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.08em] text-slate-300">Sistema de gestión interna</p>
                    <h1 class="mt-6 max-w-xl text-5xl font-extrabold leading-[0.98] tracking-[-0.045em] sm:text-6xl lg:text-7xl">
                        Gestioná mejor.<br>Trabajá más rápido.
                    </h1>
                    <p class="mt-8 max-w-lg text-base leading-7 text-slate-300">
                        CRUZNEGRA reúne clientes, proyectos, tareas, hitos, entregables, control y facturación en un solo lugar.
                    </p>
                    <p class="mt-7 text-sm text-slate-300">El acceso es administrado por tu organización.</p>

                    <div class="mt-9 flex flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-[#101416] transition hover:bg-slate-200">
                            Ingresar <span class="ml-2 text-lg leading-none">→</span>
                        </a>
                        <a href="#vista-general" class="inline-flex items-center rounded-full border border-white/60 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white hover:text-[#101416]">
                            Más información
                        </a>
                    </div>
                </div>

                <div id="vista-general" class="relative mx-auto w-full max-w-[620px] lg:justify-self-end">
                    <div class="absolute -inset-12 rounded-full bg-[#39545a]/30 blur-3xl"></div>
                    <div class="relative rounded-[1.5rem] border-2 border-[#70797b] bg-[#171b1d] p-2 shadow-[0_30px_80px_rgba(0,0,0,0.65)]">
                        <div class="overflow-hidden rounded-[1rem] bg-[#f5f7f8] text-slate-900">
                            <div class="flex min-h-[300px] sm:min-h-[390px]">
                                <aside class="hidden w-32 shrink-0 bg-[#1d2023] p-4 text-[9px] text-slate-500 sm:block">
                                    <div class="text-[10px] font-bold text-white">CRUZNEGRA</div>
                                    <div class="mt-8 space-y-4">
                                        <p class="flex items-center gap-1.5 rounded bg-white/10 px-2 py-1 text-white"><x-heroicon-o-home class="h-3 w-3 shrink-0" /> Inicio</p>
                                        <p>Clientes</p>
                                        <p>Proyectos</p>
                                        <p>Tareas</p>
                                        <p>Equipo</p>
                                        <p>Archivos</p>
                                    </div>
                                </aside>
                                <div class="flex-1 p-5 sm:p-8">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-[9px] text-slate-400">Panel de gestión</p>
                                            <h2 class="mt-1 text-xl font-bold sm:text-2xl">Clientes. Proyectos</h2>
                                        </div>
                                        <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                                    </div>
                                    <p class="mt-3 max-w-xs text-[10px] leading-4 text-slate-500">Colaborá con el equipo y mantené toda la información organizada.</p>
                                    <div class="mt-7 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                        @foreach ([['Proyecto', 'bg-emerald-100 text-emerald-700', 'arrow-trending-up'], ['Conversaciones', 'bg-pink-100 text-pink-700', 'chat-bubble-left-right'], ['Equipo', 'bg-amber-100 text-amber-700', 'users'], ['Búsquedas', 'bg-sky-100 text-sky-700', 'magnifying-glass'], ['Trámites', 'bg-blue-100 text-blue-700', 'clipboard-document-list'], ['Configuración', 'bg-orange-100 text-orange-700', 'cog-6-tooth'], ['Archivos', 'bg-yellow-100 text-yellow-700', 'folder'], ['Seguimientos', 'bg-teal-100 text-teal-700', 'check-circle']] as [$titulo, $color, $icono])
                                            <div class="rounded-lg border border-slate-200 bg-white p-2.5 shadow-sm sm:p-3">
                                                <span class="flex h-7 w-7 items-center justify-center rounded-md {{ $color }}"><x-dynamic-component :component="'heroicon-o-'.$icono" class="h-4 w-4" /></span>
                                                <p class="mt-3 text-[8px] font-semibold sm:text-[9px]">{{ $titulo }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
