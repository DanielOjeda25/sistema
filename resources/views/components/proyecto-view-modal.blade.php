{{--
    Pop-up de resumen de un proyecto (solo lectura). Se engancha al alcance
    Alpine del padre: el layout define `verProyecto` y el boton del listado
    le asigna el objeto de la fila. El detalle completo (linea de tiempo,
    tareas, informes) sigue en proyectos.show, enlazado desde el pie.
--}}
<div x-show="verProyecto" x-cloak class="fixed inset-0 overflow-y-auto" style="z-index: 9999" role="dialog" aria-modal="true" @keydown.escape.window="verProyecto = null">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="verProyecto = null"></div>

    <div class="min-h-full flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" x-show="verProyecto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <template x-if="verProyecto">
                <div>
                    {{-- Cabecera: nombre, cliente y estado --}}
                    <div class="relative bg-gradient-to-br from-[#202225] to-[#2d3134] px-6 py-5 text-white">
                        <button @click="verProyecto = null"
                                class="absolute top-4 right-4 flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition hover:bg-white/10 hover:text-white"
                                aria-label="Cerrar">
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                        <div class="flex items-center gap-4">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white/10">
                                <x-heroicon-o-squares-2x2 class="h-7 w-7 text-[#00e5a0]" />
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-lg font-bold leading-tight" x-text="verProyecto.nombre"></h3>
                                <p class="text-xs text-slate-300 mt-0.5 truncate" x-text="verProyecto.cliente"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Avance --}}
                    <div class="px-6 pt-5 pb-1">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span class="font-semibold text-gray-600 uppercase tracking-wide text-[11px]">Avance del proyecto</span>
                            <span class="font-bold text-[#008c63]" x-text="verProyecto.avance + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-[#00b87d] h-2.5 rounded-full transition-all" :style="'width: ' + verProyecto.avance + '%'"></div>
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
                                <p class="text-sm font-medium text-gray-800 truncate" x-text="verProyecto.pm"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-calendar-days class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Inicio — Fin estimado</p>
                                <p class="text-sm font-medium text-gray-800" x-text="verProyecto.fechas"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-clipboard-document-check class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Tareas</p>
                                <p class="text-sm font-medium text-gray-800" x-text="verProyecto.tareas"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-flag class="h-5 w-5" />
                            </span>
                            <div class="flex items-center justify-between w-full">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Estado</p>
                                    <span class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-gray-100 text-gray-700" x-text="verProyecto.estado"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <a :href="verProyecto.detalle" class="text-sm font-semibold text-[#008c63] hover:underline">Ver detalle completo &rarr;</a>
                        <button type="button" @click="verProyecto = null"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            Cerrar
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
