{{--
    Pop-up de detalle de una factura (solo lectura).
    Se engancha al alcance Alpine del padre: el layout define `detalle`
    y los botones del listado le asignan el objeto con los datos de la fila.
--}}
<div data-teleportar x-data="{ detalle: null }" @ver-factura.window="detalle = $event.detail" x-show="detalle" x-cloak class="fixed inset-0 overflow-y-auto" style="z-index: 9999" role="dialog" aria-modal="true" @keydown.escape.window="detalle = null">
    <div class="fixed inset-0 bg-gray-900/60" @click="detalle = null"></div>

    <div class="min-h-full flex items-center justify-center p-4">
        <div class="relative m-auto h-fit bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden" x-show="detalle"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <template x-if="detalle">
                <div>
                    {{-- Cabecera: numero, monto destacado y estado (el cierre esta en el boton "Cerrar" del pie) --}}
                    <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-100">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                    :class="detalle.estado === 'pagada' ? 'bg-green-100 text-green-700' : (detalle.estado === 'vencida' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')"
                                    x-text="detalle.estado"></span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800" x-text="detalle.numero"></h3>
                            <p class="text-xs text-gray-400 mt-1 truncate" x-text="detalle.proyecto"></p>
                        </div>
                        <p class="text-xl font-bold text-[#008c63] shrink-0" x-text="detalle.monto"></p>
                    </div>

                    {{-- Datos: filas con icono --}}
                    <div class="px-6 py-2 divide-y divide-gray-100">
                        <div class="flex items-center gap-3 py-3.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-calendar-days class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Emisión</p>
                                <p class="text-sm font-medium text-gray-800" x-text="detalle.emision"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-clock class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Vencimiento</p>
                                <p class="text-sm font-medium text-gray-800" x-text="detalle.vencimiento"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-user class="h-5 w-5" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Emitida por</p>
                                <p class="text-sm font-medium text-gray-800 truncate" x-text="detalle.emitida_por"></p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 py-3.5" x-show="detalle.detalle">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-document-text class="h-5 w-5" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Detalle</p>
                                <p class="text-sm font-medium text-gray-800" x-text="detalle.detalle"></p>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <a :href="detalle.pdf" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#00b87d] rounded-lg text-xs font-semibold text-white uppercase tracking-widest hover:bg-[#008c63]">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Descargar PDF
                        </a>
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
