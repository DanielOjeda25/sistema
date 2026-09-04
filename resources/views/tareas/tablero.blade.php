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

    // El JSON viaja en un atributo delimitado por comillas simples; los flags
    // HEX_* escapan &, <, >, ' y " para que el navegador no lo rompa al
    // decodificar el HTML y JSON.parse reciba texto válido.
    $flags = JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP;
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
                <a href="{{ route('tareas.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                    Vista lista
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @unless ($puedeMover)
                <p class="mb-4 text-sm text-gray-500">
                    Estás viendo el tablero en modo lectura: solo se pueden mover, crear o editar
                    tarjetas con rol Jefe, PM o PO.
                </p>
            @endunless

            <div class="flex gap-4 items-start overflow-x-auto pb-4" data-tablero>

                @foreach ($columnas as $estado => [$etiqueta, $color])
                    <div class="columna bg-gray-100/80 rounded-xl p-3 w-72 shrink-0" data-estado="{{ $estado }}">
                        <div class="flex items-center justify-between px-1 pb-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $color }}">{{ $etiqueta }}</span>
                            <span class="text-xs text-gray-400" data-contador>{{ $tareas->where('estado', $estado)->count() }}</span>
                        </div>

                        <div class="tarjetas space-y-3 min-h-[80px]">
                            @forelse ($tareas->where('estado', $estado) as $tarea)
                                @php
                                    $vencida = $tarea->fecha_limite
                                        && $tarea->fecha_limite->lt($hoy)
                                        && ! in_array($tarea->estado, ['completada', 'cancelada']);

                                    // Lo que consume el modal de edición al hacer clic en la tarjeta.
                                    $payload = $tarea->only(['id', 'titulo', 'descripcion', 'estado', 'prioridad', 'fecha_limite', 'proyecto_id', 'asignado_a']);
                                    $payload['proyecto'] = ['nombre' => $tarea->proyecto?->nombre];
                                    $payload['asignado'] = ['name' => $tarea->asignado?->name];
                                @endphp
                                <article class="tarjeta bg-white rounded-lg shadow-sm p-3 {{ $puedeMover ? 'cursor-grab active:cursor-grabbing' : '' }} hover:shadow-md transition-shadow"
                                         data-id="{{ $tarea->id }}"
                                         data-tarea='@json($payload, $flags)'>
                                    <a href="{{ route('tareas.show', $tarea) }}" class="font-medium text-gray-800 hover:text-indigo-600">
                                        {{ $tarea->titulo }}
                                    </a>
                                    <div class="mt-2 flex flex-wrap items-center gap-1.5 text-xs">
                                        <span class="px-2 py-0.5 rounded-full font-medium {{ $prioridades[$tarea->prioridad] }}">
                                            {{ ucfirst($tarea->prioridad) }}
                                        </span>
                                        @if ($tarea->fecha_limite)
                                            <span class="px-2 py-0.5 rounded-full font-medium {{ $vencida ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $tarea->fecha_limite->format('d/m') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500 flex justify-between gap-2">
                                        <span class="truncate">{{ $tarea->asignado?->name ?? 'Sin asignar' }}</span>
                                        @unless ($proyectoId)
                                            <span class="truncate max-w-[120px]" title="{{ $tarea->proyecto?->nombre }}">{{ $tarea->proyecto?->nombre }}</span>
                                        @endunless
                                    </div>
                                </article>
                            @empty
                                <p class="sin-tareas text-xs text-gray-400 text-center py-4">Sin tareas</p>
                            @endforelse
                        </div>

                        @if ($puedeMover)
                            <div class="agregar-tarjeta mt-3">
                                <form data-agregar class="hidden space-y-2">
                                    <input type="hidden" name="estado" value="{{ $estado }}">
                                    <textarea name="titulo" rows="2" placeholder="Título de la tarea…"
                                              class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"></textarea>
                                    <div class="grid grid-cols-2 gap-2">
                                        <select name="prioridad" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">
                                            <option value="baja">Baja</option>
                                            <option value="media" selected>Media</option>
                                            <option value="alta">Alta</option>
                                        </select>
                                        <select name="asignado_a" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">
                                            @foreach ($usuarios as $u)
                                                <option value="{{ $u->id }}" @selected($u->is(auth()->user()))>{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <select name="proyecto_id" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs">
                                        @foreach ($proyectos as $p)
                                            <option value="{{ $p->id }}" @selected($proyectoId == $p->id)>{{ $p->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <div class="flex items-center justify-between gap-2">
                                        <button type="submit"
                                                class="px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                            Añadir
                                        </button>
                                        <button type="button" data-alternar-agregar
                                                class="text-xs text-gray-500 hover:underline">Cancelar</button>
                                    </div>
                                    <p class="error hidden text-xs text-red-600"></p>
                                </form>
                                <button type="button" data-alternar-agregar
                                        class="w-full text-left px-2 py-1.5 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded-md transition-colors">
                                    + Añadir tarjeta
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    {{-- Configuración que consume resources/js/tablero.js --}}
    <script>
        window.TABLERO = {
            puedeEditar: {{ $puedeMover ? 'true' : 'false' }},
            proyectoFiltrado: {{ $proyectoId ? 'true' : 'false' }},
            prioridades: @json($prioridades),
            urls: {
                store: '{{ route('tareas.store') }}',
                update: '{{ route('tareas.update', ':id:') }}',
                show: '{{ route('tareas.show', ':id:') }}',
                mover: '{{ route('tareas.mover') }}',
            },
        };
    </script>
    @vite('resources/js/tablero.js')

    @if ($puedeMover)
        {{-- Modal de edición rápida (clic en una tarjeta) --}}
        <div id="modal-tarea" class="hidden fixed inset-0 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/50" data-cerrar-modal></div>

            <div class="min-h-full flex items-center justify-center p-4">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
                    <form id="form-editar-tarea" method="POST" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-lg text-gray-800">Editar tarea</h3>
                            <button type="button" data-cerrar-modal class="text-gray-400 hover:text-gray-600">✕</button>
                        </div>

                        <div>
                            <x-input-label for="editar-titulo" value="Título" />
                            <x-text-input id="editar-titulo" name="titulo" type="text" class="mt-1 block w-full" required />
                        </div>

                        <div>
                            <x-input-label for="editar-descripcion" value="Descripción (opcional)" />
                            <textarea id="editar-descripcion" name="descripcion" rows="3"
                                      class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-input-label for="editar-estado" value="Estado" />
                                <select id="editar-estado" name="estado" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach ($columnas as $estado => [$etiqueta, $color])
                                        <option value="{{ $estado }}">{{ $etiqueta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="editar-prioridad" value="Prioridad" />
                                <select id="editar-prioridad" name="prioridad" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="baja">Baja</option>
                                    <option value="media">Media</option>
                                    <option value="alta">Alta</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-input-label for="editar-fecha" value="Fecha límite (opcional)" />
                                <x-text-input id="editar-fecha" name="fecha_limite" type="date" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="editar-proyecto" value="Proyecto" />
                                <select id="editar-proyecto" name="proyecto_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @foreach ($proyectos as $p)
                                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="editar-asignado" value="Asignar a" />
                            <select id="editar-asignado" name="asignado_a" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($usuarios as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <p class="error hidden text-sm text-red-600"></p>

                        <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                            <div class="flex items-center gap-4">
                                <button type="button" class="eliminar-tarea text-sm text-red-600 hover:underline">Eliminar</button>
                                <a href="#" class="ver-detalle text-sm text-gray-600 hover:underline">Ver detalle</a>
                            </div>
                            <x-primary-button>Guardar</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
