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

            @if (! $esCliente)
    <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Reportes generales</h3>
            <p class="text-sm text-gray-500">Resumen global para los roles internos.</p>
        </div>

        <div>
            <h4 class="font-semibold text-gray-700 mb-3">Proyectos por estado</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $proyectosPorEstado['pendiente'] }}</div>
                    <div class="text-sm text-gray-500">Pendientes</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-700">{{ $proyectosPorEstado['en_progreso'] }}</div>
                    <div class="text-sm text-gray-500">En progreso</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-700">{{ $proyectosPorEstado['completado'] }}</div>
                    <div class="text-sm text-gray-500">Completados</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-700">{{ $proyectosPorEstado['cancelado'] }}</div>
                    <div class="text-sm text-gray-500">Cancelados</div>
                </div>
            </div>
        </div>

        <div>
            <h4 class="font-semibold text-gray-700 mb-3">Tareas por estado</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $tareasPorEstado['pendiente'] }}</div>
                    <div class="text-sm text-gray-500">Pendientes</div>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-700">{{ $tareasPorEstado['en_progreso'] }}</div>
                    <div class="text-sm text-gray-500">En progreso</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-700">{{ $tareasPorEstado['completada'] }}</div>
                    <div class="text-sm text-gray-500">Completadas</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <div class="text-2xl font-bold text-red-700">{{ $tareasPorEstado['cancelada'] }}</div>
                    <div class="text-sm text-gray-500">Canceladas</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-indigo-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-indigo-700">
                    $ {{ number_format($totalFacturado, 2, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500">Total facturado</div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-yellow-700">
                    $ {{ number_format($totalPendienteCobro, 2, ',', '.') }}
                </div>
                <div class="text-sm text-gray-500">Pendiente de cobro</div>
            </div>
            <div class="bg-red-50 rounded-lg p-4">
                <div class="text-2xl font-bold text-red-700">{{ $tareasVencidas }}</div>
                <div class="text-sm text-gray-500">Tareas vencidas</div>
            </div>
        </div>
    </section>
@endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @unless ($esCliente)
                <a href="{{ route('clientes.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-gray-900">{{ $totalClientes }}</div>
                    <div class="text-sm text-gray-500 mt-1">Clientes</div>
                </a>
                @endunless

                <a href="{{ route('proyectos.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-gray-900">{{ $totalProyectos }}</div>
                    <div class="text-sm text-gray-500 mt-1">Proyectos</div>
                </a>

                <a href="{{ route('tareas.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-orange-600">{{ $tareasPendientes }}</div>
                    <div class="text-sm text-gray-500 mt-1">Tareas pendientes</div>
                </a>

                <a href="{{ route('hitos.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-gray-900">{{ $totalHitos }}</div>
                    <div class="text-sm text-gray-500 mt-1">Hitos</div>
                </a>

                <a href="{{ route('entregables.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-gray-900">{{ $totalEntregables }}</div>
                    <div class="text-sm text-gray-500 mt-1">Entregables</div>
                </a>

                <a href="{{ route('facturas.index') }}" class="block bg-white shadow-sm rounded-lg p-6 hover:shadow-md transition">
                    <div class="text-3xl font-bold text-red-600">{{ $facturasPendientes }}</div>
                    <div class="text-sm text-gray-500 mt-1">Facturas pendientes</div>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>