<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'CRUZNEGRA') }} · Sistema de Gestión</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex flex-col">

        <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

                <a href="/" class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="font-extrabold text-xl text-gray-900 tracking-tight">CRUZNEGRA</span>
                </a>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-2 sm:gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-semibold bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                                Ir al panel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 px-3 py-2 transition">
                                Ingresar
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm font-semibold bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                                    Registrarse
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        <main class="flex-grow">

            {{-- Portada --}}
            <section class="bg-indigo-700 text-white">
                <div class="max-w-5xl mx-auto px-6 py-20 sm:py-28 text-center">

                    <span class="inline-block text-xs font-semibold tracking-widest uppercase text-indigo-200 mb-5">
                        Sistema de Gestión Interna
                    </span>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                        Todos tus proyectos,<br class="hidden sm:block"> en un solo lugar
                    </h1>

                    <p class="mt-6 text-lg sm:text-xl text-indigo-100 max-w-2xl mx-auto leading-relaxed">
                        CRUZNEGRA centraliza clientes, proyectos, tareas, hitos, entregables
                        y facturación para que el equipo trabaje sobre la misma información.
                    </p>

                    @guest
                        <div class="mt-10 flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white text-indigo-700 font-semibold rounded-lg hover:bg-indigo-50 transition">
                                Ingresar al sistema
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-indigo-400 text-white font-semibold rounded-lg hover:bg-indigo-600 transition">
                                    Crear una cuenta
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="mt-10">
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white text-indigo-700 font-semibold rounded-lg hover:bg-indigo-50 transition">
                                Ir al panel de control
                            </a>
                        </div>
                    @endguest
                </div>
            </section>

            {{-- Módulos --}}
            <section class="max-w-6xl mx-auto px-6 py-16 sm:py-20">

                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight text-center">
                    Qué podés gestionar
                </h2>
                <p class="mt-3 text-gray-500 text-center max-w-2xl mx-auto">
                    Siete módulos conectados entre sí, con control de acceso por rol.
                </p>

                @php
                    $modulos = [
                        ['Clientes', 'Datos de contacto y empresa de cada cliente, con su historial de proyectos.', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                        ['Proyectos', 'Alcance, fechas y estado de cada proyecto, con su cliente y responsable asignado.', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                        ['Tareas e hitos', 'Trabajo repartido por persona, con prioridad, fecha límite y avance visible.', 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                        ['Solicitudes de cambio', 'Pedidos de modificación registrados, aprobados y convertidos en tareas.', 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                        ['Entregables', 'Documentos y productos del proyecto, con su estado de revisión y aprobación.', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['Facturación', 'Facturas por proyecto, con monto, vencimiento y estado de cobro.', 'M9 7h6m-6 4h6m-6 4h4M5 3h14a1 1 0 011 1v16l-3-2-2 2-2-2-2 2-2-2-3 2V4a1 1 0 011-1z'],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
                    @foreach ($modulos as [$titulo, $texto, $icono])
                        <div class="bg-white p-6 rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-md transition">
                            <div class="w-11 h-11 rounded-lg bg-indigo-50 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icono }}"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 text-lg">{{ $titulo }}</h3>
                            <p class="text-sm text-gray-500 mt-2 leading-relaxed">{{ $texto }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Roles y trazabilidad --}}
            <section class="bg-white border-t border-gray-200">
                <div class="max-w-6xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-2 gap-10">

                    <div class="flex gap-4">
                        <div class="w-11 h-11 shrink-0 rounded-lg bg-indigo-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">Acceso por rol</h3>
                            <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                                Cada persona ve y edita solo lo que le corresponde según su rol:
                                Jefe, Project Manager, Product Owner, Programador o Cliente.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-11 h-11 shrink-0 rounded-lg bg-indigo-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">Trazabilidad</h3>
                            <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                                Cada cambio queda registrado: quién lo hizo, cuándo y qué modificó.
                                Auditoría completa de los movimientos del sistema.
                            </p>
                        </div>
                    </div>

                </div>
            </section>

        </main>

        <footer class="bg-white border-t border-gray-200 py-6">
            <div class="max-w-7xl mx-auto px-4 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} CRUZNEGRA · Sistema de Gestión Interna de Proyectos.
            </div>
        </footer>

    </body>
</html>
