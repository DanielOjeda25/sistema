{{--
    Pop-up de detalle de una solicitud de cambio (solo lectura).
    Componente autocontenido: escucha el evento "ver-solicitud" que disparan
    los botones del listado via data-dispatch (ver crud-modal.js).
    El boton "Editar" del pie reutiliza el disparador de edicion de la fila
    (#editar-solicitud-{id}) para que el modal CRUD se abra ya cargado.
--}}
<div data-teleportar x-data="{ detalle: null }" @ver-solicitud.window="detalle = $event.detail" x-show="detalle" x-cloak
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
                    {{-- Cabecera: insignias, titulo y proyecto/solicitante --}}
                    <div class="flex items-start justify-between gap-4 p-6 border-b border-gray-100">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                      :class="detalle.estado_color" x-text="detalle.estado"></span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                      :class="detalle.prioridad_color" x-text="'Prioridad ' + detalle.prioridad"></span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800" x-text="detalle.titulo"></h3>
                            <p class="text-xs text-gray-400 mt-1">
                                <span x-text="detalle.proyecto"></span> · pedida por <span x-text="detalle.solicitante"></span>
                            </p>
                        </div>
                        <button @click="detalle = null" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="Cerrar">
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="p-6">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Descripción</p>
                        <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed"
                           x-text="detalle.descripcion || 'Sin descripción.'"></p>
                    </div>

                    {{-- Pie: cerrar o pasar directo al modal de edicion de esa fila --}}
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100">
                        <button type="button" @click="detalle = null"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            Cerrar
                        </button>
                        <button type="button" x-show="detalle.puede_editar"
                                @click="const id = detalle.id; detalle = null; document.getElementById('editar-solicitud-' + id)?.click()"
                                class="px-4 py-2 bg-[#00b87d] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63] inline-flex items-center gap-1.5">
                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                            Editar
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
