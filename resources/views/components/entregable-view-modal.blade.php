{{--
    Pop-up de detalle de un entregable IA.
    Se engancha al alcance Alpine del padre: la pagina define `detalle`
    (objeto del entregable o null) y los botones le asignan el objeto.
--}}
<div x-data="{ detalle: null }" @detalle-entregable.window="detalle = $event.detail" x-show="detalle" x-cloak class="fixed inset-0 odetalleflow-y-auto" style="z-index: 9999" role="dialog" aria-modal="true" @keydown.escape.window="detalle = null">
    <div class="fixed inset-0 bg-gray-900/60" @click="detalle = null"></div>

    <div class="min-h-full flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl" x-show="detalle">
            <template x-if="detalle">
                <div>
                    <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-100">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700" x-text="detalle.tipo"></span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                    :class="detalle.estado === 'aprobado' ? 'bg-green-100 text-green-700' : (detalle.estado === 'revisado' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600')"
                                    x-text="detalle.estado"></span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800" x-text="detalle.titulo"></h3>
                            <p class="text-xs text-gray-400 mt-1">
                                <span x-text="detalle.proyecto"></span> · generado por <span x-text="detalle.generador"></span>
                                <span x-show="detalle.fecha"> · <span x-text="detalle.fecha"></span></span>
                            </p>
                        </div>
                        <button @click="detalle = null" class="text-gray-400 hodetalle:text-gray-600 text-xl leading-none" aria-label="Cerrar">×</button>
                    </div>

                    <div class="p-6">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Contenido</p>
                        <div class="bg-[#f8fafc] border border-gray-100 rounded-xl p-4 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed max-h-72 odetalleflow-y-auto" x-text="detalle.contenido"></div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
