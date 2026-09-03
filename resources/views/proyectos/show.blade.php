<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Proyecto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-100 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Nombre:</strong> {{ $proyecto->nombre }}</p>
                <p><strong>Descripción:</strong> {{ $proyecto->descripcion ?? 'Sin descripción' }}</p>
                <p><strong>Cliente:</strong> {{ $proyecto->cliente?->nombre ?? 'N/A' }} {{ $proyecto->cliente?->apellido }}</p>
                <p><strong>Project Manager:</strong> {{ $proyecto->pm?->name ?? 'N/A' }}</p>
                <p><strong>Estado:</strong> {{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}</p>
                <p><strong>Fecha de inicio:</strong> {{ $proyecto->fecha_inicio?->format('d/m/Y') ?? 'N/A' }}</p>
                <p><strong>Fecha de fin estimada:</strong> {{ $proyecto->fecha_fin_estimada?->format('d/m/Y') ?? 'N/A' }}</p>

                <div class="pt-4 border-t">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-semibold">Avance calculado</span>
                        <span>{{ $progreso['porcentaje'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-indigo-600 h-3 rounded-full"
                             style="width: {{ $progreso['porcentaje'] }}%"></div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">{{ $progreso['criterio'] }}</p>
                </div>

                <div class="pt-4 border-t">
                    <p class="font-semibold mb-2">Tareas de este proyecto:</p>
                    <ul class="list-disc ms-5">
                        @forelse ($proyecto->tareas as $t)
                            <li>{{ $t->titulo }}</li>
                        @empty
                            <li class="list-none text-gray-500">Sin tareas todavía.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="pt-4 flex gap-4 border-t">
                    @hasanyrole('Jefe|PM')
                    <a href="{{ route('proyectos.edit', $proyecto) }}" class="text-yellow-600 hover:underline">Editar</a>
                    @endhasanyrole
                    <a href="{{ route('proyectos.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
            </div>

            <section class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Actualizaciones del proyecto</h3>
                    <p class="text-sm text-gray-500">Avances y novedades utilizados como contexto del informe.</p>
                </div>

                @hasanyrole('Jefe|PM|PO|Programador')
                    <form method="POST" action="{{ route('proyectos.actualizaciones.store', $proyecto) }}"
                          class="grid grid-cols-1 md:grid-cols-2 gap-4 border rounded-lg p-4">
                        @csrf
                        <div>
                            <x-input-label for="titulo" value="Título" />
                            <x-text-input id="titulo" name="titulo" class="mt-1 block w-full"
                                          :value="old('titulo')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
                        </div>
                        <div>
                            <x-input-label for="fecha" value="Fecha" />
                            <x-text-input id="fecha" name="fecha" type="date" class="mt-1 block w-full"
                                          :value="old('fecha', now()->toDateString())" required />
                            <x-input-error class="mt-2" :messages="$errors->get('fecha')" />
                        </div>
                        <div>
                            <x-input-label for="tipo" value="Tipo" />
                            <select id="tipo" name="tipo"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="avance">Avance</option>
                                <option value="problema">Problema</option>
                                <option value="decision">Decisión</option>
                                <option value="proximo_paso">Próximo paso</option>
                            </select>
                        </div>
                        <label class="flex items-center gap-2 self-end pb-2">
                            <input type="checkbox" name="visible_cliente" value="1"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm">
                            <span class="text-sm">Puede incluirse en el informe del Cliente</span>
                        </label>
                        <div class="md:col-span-2">
                            <x-input-label for="descripcion" value="Descripción" />
                            <textarea id="descripcion" name="descripcion" rows="3" required
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('descripcion') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('descripcion')" />
                        </div>
                        <div class="md:col-span-2 text-right">
                            <x-primary-button>Registrar actualización</x-primary-button>
                        </div>
                    </form>
                @endhasanyrole

                <div class="space-y-3">
                    @forelse ($actualizaciones as $actualizacion)
                        <article class="border rounded-lg p-4">
                            <div class="flex flex-wrap justify-between gap-2">
                                <strong>{{ $actualizacion->titulo }}</strong>
                                <span class="text-sm text-gray-500">{{ $actualizacion->fecha->format('d/m/Y') }}</span>
                            </div>
                            <p class="mt-2 whitespace-pre-wrap">{{ $actualizacion->descripcion }}</p>
                            <p class="mt-2 text-xs text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $actualizacion->tipo)) }} · {{ $actualizacion->autor?->name }}
                                @unless ($actualizacion->visible_cliente)
                                    · Solo interno
                                @endunless
                            </p>
                        </article>
                    @empty
                        <p class="text-gray-500">Todavía no hay actualizaciones registradas.</p>
                    @endforelse
                </div>
            </section>

            <section class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-4">
                <div class="flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Informes de avance</h3>
                        <p class="text-sm text-gray-500">El Cliente solo ve informes aprobados y publicados.</p>
                    </div>
                    @hasanyrole('Jefe|PM|PO|Programador')
                        <form method="POST" action="{{ route('proyectos.informes-ia.store', $proyecto) }}">
                            @csrf
                            <x-primary-button>Generar borrador</x-primary-button>
                        </form>
                    @endhasanyrole
                </div>

                <div class="space-y-4">
                    @forelse ($informes as $informe)
                        <article class="border rounded-lg p-4 space-y-3">
                            <div class="flex flex-wrap justify-between gap-2">
                                <strong>{{ $informe->titulo }}</strong>
                                <span class="text-sm {{ $informe->visible_cliente ? 'text-green-700' : 'text-yellow-700' }}">
                                    {{ $informe->visible_cliente ? 'Publicado' : ucfirst($informe->estado) }}
                                </span>
                            </div>
                            <div class="whitespace-pre-wrap bg-gray-50 rounded p-4">{{ $informe->contenido }}</div>
                            @if ($informe->mensaje_error)
                                <p class="text-sm text-red-700 bg-red-50 rounded p-3">
                                    Error de generación: {{ $informe->mensaje_error }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-500">
                                Modelo: {{ $informe->modelo_ia ?? 'No informado' }} ·
                                Generado: {{ $informe->generado_en?->format('d/m/Y H:i') ?? 'N/A' }}
                            </p>

                            @hasanyrole('Jefe|PM|PO')
                                <div class="flex flex-wrap gap-4">
                                <a href="{{ route('entregables.edit', $informe) }}"
                                   class="text-indigo-600 hover:underline">Editar borrador</a>
                                @if ($informe->visible_cliente)
                                    <form method="POST" action="{{ route('informes-ia.unpublish', $informe) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-red-600 hover:underline">Retirar del Cliente</button>
                                    </form>
                                @elseif (! $informe->mensaje_error)
                                    <form method="POST" action="{{ route('informes-ia.publish', $informe) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-green-700 hover:underline">Aprobar y publicar</button>
                                    </form>
                                @endif
                                </div>
                            @endhasanyrole
                        </article>
                    @empty
                        <p class="text-gray-500">Todavía no hay informes de avance.</p>
                    @endforelse
                </div>
            </section>

        </div>
        </div>
    </div>
</x-app-layout>
