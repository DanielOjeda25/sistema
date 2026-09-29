{{--
    Pop-up de resumen de un proyecto (solo lectura). Se engancha al alcance
    Alpine del padre: el layout define `detalle` y el boton del listado
    le asigna el objeto de la fila. El detalle completo (linea de tiempo,
    tareas, informes) sigue en proyectos.show, enlazado desde el pie.
--}}
<div data-teleportar x-data="{ detalle: null }" @ver-proyecto.window="detalle = $event.detail" x-show="detalle" x-cloak class="fixed inset-0 overflow-y-auto" style="z-index: 9999" role="dialog" aria-modal="true" @keydown.escape.window="detalle = null">
    <div class="fixed inset-0 bg-gray-900/60" @click="detalle = null"></div>

    <div class="min-h-full flex items-center justify-center p-4">
        <div class="relative m-auto h-fit bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden" x-show="detalle"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <template x-if="detalle">
                <div>
                    {{-- Cabecera: nombre, insignia de estado y cliente (el cierre esta en el boton "Cerrar" del pie) --}}
                    <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-100">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-700" x-text="detalle.estado"></span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800" x-text="detalle.nombre"></h3>
                            <p class="text-xs text-gray-400 mt-1 truncate" x-text="detalle.cliente"></p>
                        </div>
                    </div>

                    {{-- Avance --}}
                    <div class="px-6 pt-5 pb-1">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span class="font-semibold text-gray-600 uppercase tracking-wide text-[11px]">Avance del proyecto</span>
                            <span class="font-bold text-[#008c63]" x-text="detalle.avance + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-[#00b87d] h-2.5 rounded-full transition-all" :style="'width: ' + detalle.avance + '%'"></div>
                        </div>
                    </div>

                    {{-- Datos --}}
                    <div class="px-6 py-2 divide-y divide-gray-100">
                        <div class="flex items-center gap-3 py-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-user class="h-5 w-5" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Project Manager</p>
                                <p class="text-sm font-medium text-gray-800 truncate" x-text="detalle.pm"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-calendar-days class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Inicio — Fin estimado</p>
                                <p class="text-sm font-medium text-gray-800" x-text="detalle.fechas"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-clipboard-document-check class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Tareas</p>
                                <p class="text-sm font-medium text-gray-800" x-text="detalle.tareas"></p>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <a :href="detalle.detalle" class="text-sm font-semibold text-[#008c63] hover:underline">Ver detalle completo <x-heroicon-o-arrow-right class="inline h-4 w-4" /></a>
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
