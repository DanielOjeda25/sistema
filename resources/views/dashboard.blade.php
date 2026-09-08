<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel de Control
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-700">
                    Bienvenido, <strong>{{ Auth::user()->name }}</strong>.
                </p>
            </div>

            @unless ($esCliente)
                <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Reportes generales</h3>
                        <p class="text-sm text-gray-500">Resumen global para los roles internos.</p>
                    </div>

                    {{-- Cada tarjeta de proyectos/tareas linkea al listado filtrado
                         por estado, así no se repite el número en otra tarjeta. --}}
                    @php
                        $estilos = [
                            'pendiente'   => ['Pendientes',   'bg-gray-50 text-gray-900'],
                            'en_progreso' => ['En progreso',  'bg-blue-50 text-blue-700'],
                            'completado'  => ['Completados',  'bg-green-50 text-green-700'],
                            'cancelado'   => ['Cancelados',   'bg-red-50 text-red-700'],
                        ];
                        $estilosTarea = [
                            'pendiente'   => ['Pendientes',   'bg-gray-50 text-gray-900'],
                            'en_progreso' => ['En progreso',  'bg-blue-50 text-blue-700'],
                            'completada'  => ['Completadas',  'bg-green-50 text-green-700'],
                            'cancelada'   => ['Canceladas',   'bg-red-50 text-red-700'],
                        ];
                    @endphp

                    <div>
                        <h4 class="font-semibold text-gray-700 mb-3">Proyectos por estado</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach ($estilos as $estado => [$etiqueta, $estilo])
                                <a href="{{ route('proyectos.index', ['estado' => $estado]) }}"
                                   class="{{ $estilo }} rounded-lg p-4 block hover:shadow-md transition">
                                    <div class="text-2xl font-bold">{{ $proyectosPorEstado[$estado] }}</div>
                                    <div class="text-sm text-gray-500">{{ $etiqueta }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-700 mb-3">Tareas por estado</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach ($estilosTarea as $estado => [$etiqueta, $estilo])
                                <a href="{{ route('tareas.index', ['estado' => $estado]) }}"
                                   class="{{ $estilo }} rounded-lg p-4 block hover:shadow-md transition">
                                    <div class="text-2xl font-bold">{{ $tareasPorEstado[$estado] }}</div>
                                    <div class="text-sm text-gray-500">{{ $etiqueta }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <a href="{{ route('facturas.index') }}" class="bg-indigo-50 rounded-lg p-4 block hover:shadow-md transition">
                            <div class="text-2xl font-bold text-indigo-700">
                                $ {{ number_format($totalFacturado, 2, ',', '.') }}
                            </div>
                            <div class="text-sm text-gray-500">Total facturado</div>
                        </a>
                        <a href="{{ route('facturas.index') }}" class="bg-yellow-50 rounded-lg p-4 block hover:shadow-md transition">
                            <div class="text-2xl font-bold text-yellow-700">
                                $ {{ number_format($totalPendienteCobro, 2, ',', '.') }}
                            </div>
                            <div class="text-sm text-gray-500">Pendiente de cobro</div>
                        </a>
                        <a href="{{ route('tareas.index', ['estado' => 'pendiente']) }}" class="bg-red-50 rounded-lg p-4 block hover:shadow-md transition">
                            <div class="text-2xl font-bold text-red-700">{{ $tareasVencidas }}</div>
                            <div class="text-sm text-gray-500">Tareas vencidas</div>
                        </a>
                    </div>
                </section>
            @endunless

            {{-- Accesos rápidos a los módulos. Los totales que ya están en los
                 reportes de arriba (tareas por estado) no se repiten acá. --}}
            @php
                $accesos = [
                    ['route' => 'proyectos.index', 'cifra' => $totalProyectos, 'etiqueta' => 'Proyectos'],
                    ['route' => 'hitos.index', 'cifra' => $totalHitos, 'etiqueta' => 'Hitos'],
                    ['route' => 'entregables.index', 'cifra' => $totalEntregables, 'etiqueta' => 'Entregables'],
                    ['route' => 'facturas.index', 'cifra' => $facturasPendientes, 'etiqueta' => 'Facturas pendientes', 'color' => 'text-red-600'],
                ];
                if (! $esCliente) {
                    array_unshift($accesos, ['route' => 'clientes.index', 'cifra' => $totalClientes, 'etiqueta' => 'Clientes']);
                }
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($accesos as $acceso)
                    <a href="{{ route($acceso['route']) }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                        <div class="text-3xl font-bold {{ $acceso['color'] ?? 'text-gray-900' }}">{{ $acceso['cifra'] }}</div>
                        <div class="text-sm text-gray-500 mt-1">{{ $acceso['etiqueta'] }}</div>
                    </a>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
