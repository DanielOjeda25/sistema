{{--
    Pop-up de detalle de una factura (solo lectura).
    Se engancha al alcance Alpine del padre: el layout define `verFactura`
    y los botones del listado le asignan el objeto con los datos de la fila.
--}}
<div x-show="verFactura" x-cloak class="fixed inset-0 overflow-y-auto" style="z-index: 9999" role="dialog" aria-modal="true" @keydown.escape.window="verFactura = null">
    <div class="fixed inset-0 bg-gray-900/60" @click="verFactura = null"></div>

    <div class="min-h-full flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl" x-show="verFactura">
            <template x-if="verFactura">
                <div>
                    <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-100">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800" x-text="verFactura.numero"></h3>
                            <p class="text-xs text-gray-400 mt-1" x-text="verFactura.proyecto"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-gray-900" x-text="verFactura.monto"></p>
                            <span class="mt-1 inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                :class="verFactura.estado === 'pagada' ? 'bg-green-100 text-green-700' : (verFactura.estado === 'vencida' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')"
                                x-text="verFactura.estado"></span>
                        </div>
                        <button @click="verFactura = null" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl leading-none ml-4" aria-label="Cerrar">×</button>
                    </div>

                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <p class="text-[11px] font-semibold uppercase text-gray-400">Fecha de emisión</p>
                            <p class="mt-0.5 text-gray-800" x-text="verFactura.emision"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase text-gray-400">Fecha de vencimiento</p>
                            <p class="mt-0.5 text-gray-800" x-text="verFactura.vencimiento"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase text-gray-400">Proyecto</p>
                            <p class="mt-0.5 text-gray-800" x-text="verFactura.proyecto"></p>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase text-gray-400">Emitida por</p>
                            <p class="mt-0.5 text-gray-800" x-text="verFactura.emitida_por"></p>
                        </div>
                        <div class="sm:col-span-2" x-show="verFactura.detalle">
                            <p class="text-[11px] font-semibold uppercase text-gray-400">Detalle</p>
                            <p class="mt-0.5 text-gray-800" x-text="verFactura.detalle"></p>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                        <a :href="verFactura.pdf" class="inline-flex items-center gap-1.5 px-3 py-2 bg-[#00b87d] rounded-lg text-xs font-semibold text-white uppercase tracking-widest hover:bg-[#008c63]">
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Descargar PDF
                        </a>
                        <button type="button" @click="verFactura = null" class="text-sm text-gray-600 hover:text-gray-900">Cerrar</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
