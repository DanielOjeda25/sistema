<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle del Proyecto
            </h2>
            <a href="{{ route('proyectos.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="w-4 h-4" /> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8" x-data="{
        tab: localStorage.getItem('tab-proyecto-{{ $proyecto->id }}') || 'resumen',
        ir(destino) { this.tab = destino; localStorage.setItem('tab-proyecto-{{ $proyecto->id }}', destino); },
    }">
            @if (session('success'))
                <div class="mb-5 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 p-4 bg-red-100 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Pestañas del detalle: una seccion por pestaña --}}
            <div class="mb-5 flex flex-wrap gap-2 border-b border-gray-200 pb-3">
                @php($contTareas = $proyecto->tareas->count())
                @php($contAct = $actualizaciones->count())
                @php($contInf = $informes->count())
                <button type="button" @click="ir('resumen')"
                        class="px-3.5 py-2 rounded-lg text-xs font-semibold uppercase tracking-widest transition"
                        :class="tab === 'resumen' ? 'bg-[#00b87d] text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50'">
                    Resumen
                </button>
                <button type="button" @click="ir('tareas')"
                        class="px-3.5 py-2 rounded-lg text-xs font-semibold uppercase tracking-widest transition"
                        :class="tab === 'tareas' ? 'bg-[#00b87d] text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50'">
                    Tareas <span class="opacity-70">({{ $contTareas }})</span>
                </button>
                <button type="button" @click="ir('actualizaciones')"
                        class="px-3.5 py-2 rounded-lg text-xs font-semibold uppercase tracking-widest transition"
                        :class="tab === 'actualizaciones' ? 'bg-[#00b87d] text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50'">
                    Actualizaciones <span class="opacity-70">({{ $contAct }})</span>
                </button>
                <button type="button" @click="ir('informes')"
                        class="px-3.5 py-2 rounded-lg text-xs font-semibold uppercase tracking-widest transition"
                        :class="tab === 'informes' ? 'bg-[#00b87d] text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50'">
                    Informes IA <span class="opacity-70">({{ $contInf }})</span>
                </button>
            </div>

            {{-- Resumen: ficha del proyecto --}}
            <div x-show="tab === 'resumen'" class="space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $proyecto->nombre }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $proyecto->cliente?->nombre ?? 'Sin cliente' }} {{ $proyecto->cliente?->apellido }} &middot; PM {{ $proyecto->pm?->name ?? '-' }}</p>
                    </div>
                    <x-estado-badge :estado="$proyecto->estado" />
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 px-6 py-5 text-sm">
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase">Descripción</dt>
                        <dd class="mt-1 text-gray-900">{{ $proyecto->descripcion ?? 'Sin descripción' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Fecha de inicio</dt>
                        <dd class="mt-1 text-gray-900">{{ $proyecto->fecha_inicio?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Fecha de fin estimada</dt>
                        <dd class="mt-1 text-gray-900">{{ $proyecto->fecha_fin_estimada?->format('d/m/Y') ?? 'Sin definir' }}</dd>
                    </div>
                </dl>

                <div class="px-6 pb-5">
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="font-semibold text-gray-800">Avance calculado</span>
                            <span class="font-bold text-[#008c63]">{{ $progreso['porcentaje'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-[#00b87d] h-3 rounded-full transition-all"
                                 style="width: {{ $progreso['porcentaje'] }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">{{ $progreso['criterio'] }}</p>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center gap-3">
                    @hasanyrole('Jefe|PM')
                        @php($valoresProyecto = $proyecto->only(['nombre', 'descripcion', 'estado', 'cliente_id', 'pm_id']) + ['fecha_inicio' => $proyecto->fecha_inicio?->format('Y-m-d'), 'fecha_fin_estimada' => $proyecto->fecha_fin_estimada?->format('Y-m-d')])
                        <button type="button" data-abrir-modal="modal-proyecto-editar"
                                data-url="{{ route('proyectos.update', $proyecto) }}"
                                data-valores='@json($valoresProyecto)'
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            <x-heroicon-o-pencil-square class="w-4 h-4" /> Editar
                        </button>
                    @endhasanyrole
                    <a href="{{ route('proyectos.index') }}" class="volver-listado ml-auto text-sm text-gray-600 hover:text-gray-900">Volver al listado</a>
                </div>
            </div>

            </div>

            {{-- Tareas --}}
            <div x-show="tab === 'tareas'" x-cloak>
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                {{-- Tareas en columna lateral: compactas, con badges que
                     bajan bajo el titulo si no entran en una linea. --}}
                <div class="px-6 pb-5 pt-4">
                    <ul class="space-y-2">
                        @forelse ($proyecto->tareas as $t)
                            <li class="bg-white border border-gray-100 rounded-lg px-3 py-2.5 shadow-sm">
                                <div class="flex items-center gap-2">
                                    @if ($t->estado === 'completada')
                                        <x-heroicon-o-check-circle class="w-5 h-5 shrink-0 text-[#00b87d]" />
                                    @else
                                        <x-heroicon-o-clock class="w-5 h-5 shrink-0 text-gray-400" />
                                    @endif
                                    <span class="text-sm font-medium text-gray-800 min-w-0">{{ $t->titulo }}</span>
                                </div>
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold
                                        {{ ['pendiente' => 'bg-amber-100 text-amber-800', 'en_progreso' => 'bg-blue-100 text-blue-800', 'completada' => 'bg-emerald-100 text-emerald-800', 'cancelada' => 'bg-red-100 text-red-800'][$t->estado] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $t->estado)) }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold
                                        {{ ['alta' => 'bg-red-50 text-red-700', 'media' => 'bg-yellow-50 text-yellow-700', 'baja' => 'bg-gray-50 text-gray-500'][$t->prioridad] ?? 'bg-gray-50 text-gray-500' }}">
                                        {{ ucfirst($t->prioridad) }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="bg-white border border-gray-100 rounded-lg px-3 py-3 text-sm text-gray-500">Sin tareas todavía.</li>
                        @endforelse
                    </ul>
                </div>

                </div>
            </div>

            {{-- Actualizaciones --}}
            <div x-show="tab === 'actualizaciones'" x-cloak class="space-y-6">
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
                                   class="rounded border-gray-300 text-[#00b87d] shadow-sm">
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

            </div>

            {{-- Informes IA --}}
            <div x-show="tab === 'informes'" x-cloak class="space-y-6">
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
                                @php($valoresInforme = $informe->only(['titulo', 'contenido', 'tipo', 'estado', 'proyecto_id', 'generado_por']))
                                <button type="button" data-abrir-modal="modal-entregable-editar"
                                        data-url="{{ route('entregables.update', $informe) }}"
                                        data-valores='@json($valoresInforme)'
                                        class="text-[#008c63] hover:text-[#00b87d] inline-flex items-center gap-1">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" /> Editar borrador</button>
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
        </div>
    </div>
    @hasanyrole('Jefe|PM')
    @include('proyectos._modal_editar')
    @endhasanyrole
    @hasanyrole('Jefe|PM|PO|Programador')
    @include('entregables._modal_editar')
    @endhasanyrole
</x-app-layout>
