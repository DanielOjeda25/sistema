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
            <div class="lg:w-5/12 bg-indigo-700 text-white px-8 py-10 lg:px-12 lg:py-16 flex flex-col justify-between">

                <div>
                    <a href="/" class="flex items-center gap-3 group">
                        <svg class="w-9 h-9 text-indigo-200 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="font-extrabold text-2xl tracking-tight">CRUZNEGRA</span>
                    </a>

                    <h1 class="mt-10 lg:mt-16 text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight">
                        Sistema de Gestión Interna
                    </h1>

                    <p class="mt-4 text-indigo-100 text-base lg:text-lg max-w-md">
                        Centralizá clientes, proyectos, tareas y facturación en un solo lugar.
                    </p>

                    <ul class="mt-8 space-y-3 text-indigo-100 hidden lg:block">
                        @foreach (['Clientes y proyectos', 'Tareas e hitos', 'Entregables y facturación'] as $item)
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-indigo-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <p class="hidden lg:block text-sm text-indigo-300 mt-10">
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
