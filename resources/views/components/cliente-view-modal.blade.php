{{--
    Pop-up de detalle de una ficha de cliente/empresa (solo lectura).
    Se engancha al alcance Alpine del padre: el layout define `verCliente`
    y el boton del listado le asigna el objeto con los datos de la fila.
--}}
<div x-show="verCliente" x-cloak class="fixed inset-0 overflow-y-auto" style="z-index: 9999" role="dialog" aria-modal="true" @keydown.escape.window="verCliente = null">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="verCliente = null"></div>

    <div class="min-h-full flex items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" x-show="verCliente"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <template x-if="verCliente">
                <div>
                    {{-- Cabecera con avatar de iniciales, nombre y badge --}}
                    <div class="relative bg-gradient-to-br from-[#202225] to-[#2d3134] px-6 py-5 text-white">
                        <button @click="verCliente = null"
                                class="absolute top-4 right-4 flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition hover:bg-white/10 hover:text-white"
                                aria-label="Cerrar">
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                        <div class="flex items-center gap-4">
                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#00b87d] text-lg font-bold text-white"
                                  x-text="(verCliente.nombre || '?').split(' ').map(p => p[0]).slice(0, 2).join('')"></span>
                            <div class="min-w-0">
                                <h3 class="text-xl font-bold leading-tight" x-text="verCliente.nombre"></h3>
                                <p class="text-sm text-slate-300 mt-0.5 truncate" x-text="verCliente.empresa"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Datos: filas con icono, no grilla plana --}}
                    <div class="px-6 py-2 divide-y divide-gray-100">
                        <div class="flex items-center gap-3 py-3.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-envelope class="h-5 w-5" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Correo electrónico</p>
                                <p class="text-sm font-medium text-gray-800 truncate" x-text="verCliente.email"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-phone class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Teléfono</p>
                                <p class="text-sm font-medium text-gray-800" x-text="verCliente.telefono"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-building-office-2 class="h-5 w-5" />
                            </span>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Empresa</p>
                                <p class="text-sm font-medium text-gray-800 truncate" x-text="verCliente.empresa"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 py-3.5">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[#008c63]">
                                <x-heroicon-o-signal class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Estado</p>
                                <span class="mt-1 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    :class="verCliente.estado === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                                    x-text="verCliente.estado === 'activo' ? 'Activo' : 'Inactivo'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end">
                        <button type="button" @click="verCliente = null"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            Cerrar
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
