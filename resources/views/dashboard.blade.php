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