@php
    $columnas = [
        'pendiente'   => ['Pendiente',   'bg-gray-100 text-gray-700'],
        'en_progreso' => ['En progreso', 'bg-blue-100 text-blue-700'],
        'completada'  => ['Completada',  'bg-green-100 text-green-700'],
        'cancelada'   => ['Cancelada',   'bg-red-100 text-red-700'],
    ];
    $prioridades = [
        'alta'  => 'bg-red-100 text-red-700',
        'media' => 'bg-yellow-100 text-yellow-700',
        'baja'  => 'bg-gray-100 text-gray-600',
    ];
    $hoy = now()->startOfDay();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tablero de Tareas
            </h2>
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('tareas.tablero') }}" class="flex items-center gap-2">
                    <select name="proyecto" onchange="this.form.submit()"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        <option value="">— Todos los proyectos —</option>
                        @foreach ($proyectos as $p)
                            <option value="{{ $p->id }}" @selected($proyectoId == $p->id)>{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </form>
                @hasanyrole('Jefe|PM|PO')
                    <a href="{{ route('tareas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        + Nueva Tarea
                    </a>
                @endhasanyrole
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @unless ($puedeMover)
                <p class="mb-4 text-sm text-gray-500">
                    Estás viendo el tablero en modo lectura: solo se pueden mover tarjetas
                    con rol Jefe, PM o PO.
                </p>
            @endunless

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-start">

                @foreach ($columnas as $estado => [$etiqueta, $color])
                    <div class="columna bg-gray-50 rounded-lg p-3" data-estado="{{ $estado }}">
                        <div class="flex items-center justify-between px-1 pb-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $color }}">{{ $etiqueta }}</span>
                            <span class="text-xs text-gray-400">{{ $tareas->where('estado', $estado)->count() }}</span>
                        </div>
                        <div class="tarjetas space-y-3 min-h-[80px]">
                            @forelse ($tareas->where('estado', $estado) as $tarea)
                                <div class="tarjeta bg-white rounded-lg shadow-sm p-3 {{ $puedeMover ? 'cursor-grab active:cursor-grabbing' : '' }}"
                                     draggable="{{ $puedeMover ? 'true' : 'false' }}"
                                     data-id="{{ $tarea->id }}">
                                    <a href="{{ route('tareas.show', $tarea) }}" class="font-medium text-gray-800 hover:text-indigo-600 hover:underline">
                                        {{ $tarea->titulo }}
                                    </a>
                                    <div class="mt-2 flex flex-wrap items-center gap-1.5 text-xs">
                                        <span class="px-2 py-0.5 rounded-full font-medium {{ $prioridades[$tarea->prioridad] }}">
                                            {{ ucfirst($tarea->prioridad) }}
                                        </span>
                                        @if ($tarea->fecha_limite)
                                            @php $vencida = $tarea->fecha_limite->lt($hoy) && ! in_array($tarea->estado, ['completada', 'cancelada']); @endphp
                                            <span class="px-2 py-0.5 rounded-full font-medium {{ $vencida ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $tarea->fecha_limite->format('d/m') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500 flex justify-between">
                                        <span>{{ $tarea->asignado?->name ?? 'Sin asignar' }}</span>
                                        @unless ($proyectoId)
                                            <span class="truncate max-w-[120px]" title="{{ $tarea->proyecto?->nombre }}">{{ $tarea->proyecto?->nombre }}</span>
                                        @endunless
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 text-center py-4">Sin tareas</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    @if ($puedeMover)
        <script>
            (function () {
                let arrastrada = null;
                let estadoOrigen = null;
                const token = '{{ csrf_token() }}';

                function columnasAEnviar() {
                    const destino = arrastrada.closest('.columna').dataset.estado;
                    const nuevas = [estadoOrigen, destino];
                    return [...new Set(nuevas)].map(estado => ({
                        estado: estado,
                        ids: [...document.querySelectorAll(`.columna[data-estado="${estado}"] .tarjeta`)]
                            .map(el => parseInt(el.dataset.id)),
                    }));
                }

                function guardar() {
                    fetch('{{ route('tareas.mover') }}', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ columnas: columnasAEnviar() }),
                    }).catch(() => {
                        alert('No se pudo guardar el movimiento. La página se va a recargar para mostrar el estado real.');
                        location.reload();
                    });
                }

                document.querySelectorAll('.tarjeta').forEach(tarjeta => {
                    tarjeta.addEventListener('dragstart', () => {
                        arrastrada = tarjeta;
                        estadoOrigen = tarjeta.closest('.columna').dataset.estado;
                        tarjeta.classList.add('opacity-50');
                    });
                    tarjeta.addEventListener('dragend', () => {
                        tarjeta.classList.remove('opacity-50');
                        guardar();
                        arrastrada = null;
                        estadoOrigen = null;
                    });
                    // Insertar antes o después de la tarjeta sobre la que se pasa el mouse.
                    tarjeta.addEventListener('dragover', e => {
                        e.preventDefault();
                        if (!arrastrada || arrastrada === tarjeta) return;
                        const rect = tarjeta.getBoundingClientRect();
                        const despues = e.clientY > rect.top + rect.height / 2;
                        tarjeta.parentNode.insertBefore(arrastrada, despues ? tarjeta.nextSibling : tarjeta);
                    });
                });

                // Soltar en el fondo de la columna manda la tarjeta al final.
                document.querySelectorAll('.columna .tarjetas').forEach(contenedor => {
                    contenedor.addEventListener('dragover', e => e.preventDefault());
                    contenedor.addEventListener('drop', e => {
                        e.preventDefault();
                        if (arrastrada) contenedor.appendChild(arrastrada);
                    });
                });
            })();
        </script>
    @endif
</x-app-layout>
