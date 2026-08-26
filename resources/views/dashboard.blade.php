@php
    use App\Models\Cliente;
    use App\Models\EntregableIA;
    use App\Models\Factura;
    use App\Models\Hito;
    use App\Models\Proyecto;
    use App\Models\SolicitudCambio;
    use App\Models\Tarea;

    $usuario = Auth::user();

    // Vistas ya construidas. Los modulos que todavia no las tienen se muestran
    // sin enlace, para no mandar a nadie a una pantalla que no existe.
    $listos = ['clientes', 'proyectos', 'tareas', 'solicitudes', 'entregables'];

    $tarjetas = [
        ['clave' => 'clientes',   'ruta' => 'clientes.index',           'titulo' => 'Clientes',    'valor' => Cliente::count(),        'detalle' => 'registrados'],
        ['clave' => 'proyectos',  'ruta' => 'proyectos.index',          'titulo' => 'Proyectos',   'valor' => Proyecto::count(),       'detalle' => Proyecto::where('estado', 'en_progreso')->count() . ' en progreso'],
        ['clave' => 'tareas',     'ruta' => 'tareas.index',             'titulo' => 'Tareas',      'valor' => Tarea::count(),          'detalle' => Tarea::where('estado', 'pendiente')->count() . ' pendientes'],
        ['clave' => 'hitos',      'ruta' => 'hitos.index',              'titulo' => 'Hitos',       'valor' => Hito::count(),           'detalle' => Hito::where('completado', false)->count() . ' sin completar'],
        ['clave' => 'solicitudes','ruta' => 'solicitudes-cambio.index', 'titulo' => 'Solicitudes', 'valor' => SolicitudCambio::count(),'detalle' => SolicitudCambio::where('estado', 'pendiente')->count() . ' pendientes'],
        ['clave' => 'entregables','ruta' => 'entregables.index',        'titulo' => 'Entregables', 'valor' => EntregableIA::count(),   'detalle' => EntregableIA::where('estado', 'aprobado')->count() . ' aprobados'],
        ['clave' => 'facturas',   'ruta' => 'facturas.index',           'titulo' => 'Facturas',    'valor' => Factura::count(),        'detalle' => Factura::where('estado', 'pendiente')->count() . ' por cobrar'],
    ];

    $montoPendiente = Factura::where('estado', 'pendiente')->sum('monto');

    $misTareas = Tarea::with('proyecto')
        ->where('asignado_a', $usuario->id)
        ->whereIn('estado', ['pendiente', 'en_progreso'])
        ->orderByRaw('fecha_limite is null, fecha_limite asc')
        ->take(5)
        ->get();

    $proyectosActivos = Proyecto::with('cliente')
        ->where('estado', 'en_progreso')
        ->latest()
        ->take(5)
        ->get();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Panel de control
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Bienvenida --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-lg text-gray-900">
                        Hola, <span class="font-bold">{{ $usuario->name }}</span>
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        @forelse ($usuario->getRoleNames() as $rol)
                            <span class="inline-block bg-indigo-50 text-indigo-700 text-xs font-semibold px-2 py-1 rounded">{{ $rol }}</span>
                        @empty
                            <span class="text-gray-400">Sin rol asignado</span>
                        @endforelse
                    </p>
                </div>
                <p class="text-sm text-gray-400">{{ now()->translatedFormat('d \d\e F, Y') }}</p>
            </div>

            {{-- Contadores por modulo --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Resumen</h3>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($tarjetas as $t)
                        @php $activo = in_array($t['clave'], $listos); @endphp

                        @if ($activo)
                            <a href="{{ route($t['ruta']) }}"
                               class="block bg-white shadow-sm rounded-lg p-5 border border-transparent hover:border-indigo-300 hover:shadow-md transition">
                                <div class="text-3xl font-bold text-gray-900">{{ $t['valor'] }}</div>
                                <div class="text-sm font-semibold text-gray-700 mt-1">{{ $t['titulo'] }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $t['detalle'] }}</div>
                            </a>
                        @else
                            <div class="bg-white shadow-sm rounded-lg p-5 opacity-60" title="Pantalla en construcción">
                                <div class="text-3xl font-bold text-gray-400">{{ $t['valor'] }}</div>
                                <div class="text-sm font-semibold text-gray-500 mt-1">{{ $t['titulo'] }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">pantalla en construcción</div>
                            </div>
                        @endif
                    @endforeach

                    <div class="bg-white shadow-sm rounded-lg p-5">
                        <div class="text-3xl font-bold text-gray-900">${{ number_format($montoPendiente, 0, ',', '.') }}</div>
                        <div class="text-sm font-semibold text-gray-700 mt-1">Por cobrar</div>
                        <div class="text-xs text-gray-400 mt-0.5">facturas pendientes</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Mis tareas --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900">Mis tareas abiertas</h3>
                        <a href="{{ route('tareas.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">Ver todas</a>
                    </div>

                    <ul class="divide-y divide-gray-100">
                        @forelse ($misTareas as $tarea)
                            <li class="py-3 flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <a href="{{ route('tareas.show', $tarea) }}" class="font-medium text-gray-900 hover:text-indigo-600 truncate block">
                                        {{ $tarea->titulo }}
                                    </a>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $tarea->proyecto?->nombre ?? 'Sin proyecto' }}
                                        @if ($tarea->fecha_limite)
                                            · vence {{ $tarea->fecha_limite->format('d/m/Y') }}
                                        @endif
                                    </p>
                                </div>
                                <span class="shrink-0 text-xs font-semibold px-2 py-1 rounded-full
                                    @if ($tarea->prioridad === 'alta') bg-red-100 text-red-700
                                    @elseif ($tarea->prioridad === 'media') bg-amber-100 text-amber-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($tarea->prioridad) }}
                                </span>
                            </li>
                        @empty
                            <li class="py-6 text-center text-sm text-gray-400">No tenés tareas pendientes.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Proyectos en curso --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Proyectos en curso</h3>

                    <ul class="divide-y divide-gray-100">
                        @forelse ($proyectosActivos as $proyecto)
                            <li class="py-3">
                                <p class="font-medium text-gray-900">{{ $proyecto->nombre }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $proyecto->cliente?->nombre ?? 'Sin cliente' }}
                                    @if ($proyecto->fecha_fin_estimada)
                                        · entrega {{ $proyecto->fecha_fin_estimada->format('d/m/Y') }}
                                    @endif
                                </p>
                            </li>
                        @empty
                            <li class="py-6 text-center text-sm text-gray-400">No hay proyectos en progreso.</li>
                        @endforelse
                    </ul>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
