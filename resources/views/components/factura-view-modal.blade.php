{{--
    Pop-up de detalle de una factura (solo lectura).
    Se engancha al alcance Alpine del padre: el layout define `detalle`
    y los botones del listado le asignan el objeto con los datos de la fila.
--}}
<div x-data="{ detalle: null }" @ver-factura.window="detalle = $event.detail" x-show="detalle" x-cloak class="fixed inset-0 overflow-y-auto" style="z-index: 9999" role="dialog" aria-modal="true" @keydown.escape.window="detalle = null">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="detalle = null"></div>

    <div class="min-h-full flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" x-show="detalle"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <template x-if="detalle">
                <div>
                    {{-- Cabecera: numero, monto destacado y estado --}}
                    <div class="relative bg-gradient-to-br from-[#202225] to-[#2d3134] px-6 py-5 text-white">
                        <button @click="detalle = null"
                                class="absolute top-4 right-4 flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition hover:bg-white/10 hover:text-white"
                                aria-label="Cerrar">
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                        <div class="flex items-center gap-4">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white/10">
                                <x-heroicon-o-banknotes class="h-7 w-7 text-[#00e5a0]" />
                            </span>
                            <div class="min-w-0">
                                <h3 class="text-lg font-bold leading-tight" x-text="detalle.numero"></h3>
                                <p class="text-xs text-slate-300 mt-0.5 truncate" x-text="detalle.proyecto"></p>
                            </div>
                            <div class="ml-auto text-right">
                                <p class="text-xl font-bold text-[#00e5a0]" x-text="detalle.monto"></p>
                                <span class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold"
                                    :class="detalle.estado === 'pagada' ? 'bg-emerald-400/20 text-emerald-300' : (detalle.estado === 'vencida' ? 'bg-red-400/20 text-red-300' : 'bg-amber-400/20 text-amber-200')"
                                    x-text="detalle.estado"></span>
                            </div>
                        </div>
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
