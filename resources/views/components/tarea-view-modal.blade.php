{{--
    Pop-up de detalle de una tarea (solo lectura).
    Componente autocontenido: escucha el evento "ver-tarea" que disparan los
    botones del listado via data-dispatch (ver crud-modal.js).
--}}
<div data-teleportar x-data="{ detalle: null }" @ver-tarea.window="detalle = $event.detail" x-show="detalle" x-cloak
     class="fixed inset-0 overflow-y-auto" style="z-index: 9999" role="dialog" aria-modal="true"
     @keydown.escape.window="detalle = null">
    <div class="fixed inset-0 bg-gray-900/60" @click="detalle = null"></div>

    <div class="min-h-full flex items-center justify-center p-4">
        <div class="relative m-auto h-fit bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden" x-show="detalle"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <template x-if="detalle">
                <div>
                    {{-- Cabecera: insignias, titulo y proyecto/sprint --}}
                    <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-100">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                      :class="detalle.estado_color" x-text="detalle.estado"></span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                      :class="detalle.prioridad_color" x-text="'Prioridad ' + detalle.prioridad"></span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800" x-text="detalle.titulo"></h3>
                            <p class="text-xs text-gray-400 mt-1">
                                <span x-text="detalle.proyecto"></span><span x-show="detalle.sprint"> · Sprint: <span x-text="detalle.sprint"></span></span>
                            </p>
                        </div>
                        <button @click="detalle = null" class="text-gray-400 hover:text-gray-600 text-xl leading-none" aria-label="Cerrar">×</button>
                    </div>

                    {{-- Datos --}}
                    <div class="px-6 py-2 divide-y divide-gray-100">
                        <div class="py-3.5">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Descripción</p>
                            <p class="mt-0.5 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed"
                               x-text="detalle.descripcion || 'Sin descripción.'"></p>
                        </div>
                        <div class="flex items-center gap-3 py-3.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-user class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Asignado a</p>
                                <p class="text-sm font-medium text-gray-800" x-text="detalle.responsable"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-calendar-days class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Fecha límite</p>
                                <p class="text-sm font-medium text-gray-800" x-text="detalle.fecha ?? 'Sin fecha'"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3.5" x-show="detalle.solicitud">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                                <x-heroicon-o-arrow-path class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Solicitud de cambio</p>
                                <p class="text-sm font-medium text-gray-800" x-text="detalle.solicitud"></p>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <button type="button" @click="detalle = null"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            Cerrar
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
