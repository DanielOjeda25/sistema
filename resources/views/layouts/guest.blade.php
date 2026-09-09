<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CRUZNEGRA') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="32x32">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col lg:flex-row">

            {{-- Panel de marca --}}
            <div class="lg:w-5/12 bg-[radial-gradient(circle_at_75%_45%,#202a2d_0%,#101416_42%,#0b0d0e_100%)] text-white px-8 py-10 lg:px-12 lg:py-16 flex flex-col justify-between">

                <div>
                    <a href="/" class="flex items-center gap-3 group">
                        <img src="{{ asset('images/cruznegra-logo-light.png') }}" alt="Cruz Negra" class="h-14 w-48 object-contain object-left">
                    </a>

                    <h1 class="mt-10 lg:mt-16 text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight">
                        Sistema de Gestión Interna
                    </h1>

                    <p class="mt-4 text-slate-300 text-base lg:text-lg max-w-md">
                        Centralizá clientes, proyectos, tareas y facturación en un solo lugar.
                    </p>

                    <ul class="mt-8 space-y-3 text-slate-300 hidden lg:block">
                        @foreach (['Clientes y proyectos', 'Tareas e hitos', 'Entregables y facturación'] as $item)
                            <li class="flex items-center gap-3">
                                <x-heroicon-o-check-circle class="h-5 w-5 shrink-0 text-[#00e5a0]" />
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <p class="hidden lg:block text-sm text-slate-400 mt-10">
                    &copy; {{ date('Y') }} CRUZNEGRA
                </p>
            </div>

            {{-- Panel del formulario --}}
            <div class="lg:w-7/12 bg-gray-50 flex items-center justify-center px-6 py-12 lg:px-12">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </body>
</html>
