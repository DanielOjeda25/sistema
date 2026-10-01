<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reportes</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-6">
            <p class="text-sm text-gray-500 mb-6">Consolidados para decidir con datos: cómo avanza la cartera, dónde se acumula el trabajo y cómo va la plata.</p>

            <div class="grid gap-5 md:grid-cols-2">

                {{-- Reporte disponible --}}
                <a href="{{ route('reportes.proyectos') }}"
                   class="group flex flex-col rounded-2xl border border-[#d7eee6] bg-white p-6 shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
                    <div class="flex items-center justify-between">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f0fff9] text-[#008c63]">
                            <x-heroicon-o-chart-bar class="h-6 w-6" />
                        </span>
                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">Disponible</span>
                    </div>
                    <h3 class="mt-4 font-bold text-gray-800 group-hover:text-[#008c63] transition">Estado y avance de proyectos</h3>
                    <p class="mt-1 text-sm text-gray-500 leading-relaxed">¿Cómo están avanzando los proyectos y cuáles necesitan atención? Avance por proyecto, tareas vencidas, hitos en riesgo y cambios pendientes, con filtros por cliente, PM, estado y período.</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-[#009d70]">
                        Ver reporte <x-heroicon-o-arrow-right class="h-4 w-4 transition group-hover:translate-x-0.5" />
                    </span>
                </a>

                {{-- Facturacion: disponible para Jefe y PM --}}
                @if (auth()->user()->hasAnyRole(['Jefe', 'PM']))
                    <a href="{{ route('reportes.facturacion') }}"
                       class="group flex flex-col rounded-2xl border border-[#d7eee6] bg-white p-6 shadow-sm transition hover:shadow-md hover:-translate-y-0.5">
                        <div class="flex items-center justify-between">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f0fff9] text-[#008c63]">
                                <x-heroicon-o-banknotes class="h-6 w-6" />
                            </span>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">Disponible</span>
                        </div>
                        <h3 class="mt-4 font-bold text-gray-800 group-hover:text-[#008c63] transition">Facturación y cobranzas</h3>
                        <p class="mt-1 text-sm text-gray-500 leading-relaxed">Facturado, cobrado, pendiente y vencido, agrupado por proyecto y por mes, con filtros de cliente, proyecto y período.</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-[#009d70]">
                            Ver reporte <x-heroicon-o-arrow-right class="h-4 w-4 transition group-hover:translate-x-0.5" />
                        </span>
                    </a>
                @endif

                {{-- Seguimiento de proyecto: se abre desde cada ficha --}}
                <div class="flex flex-col rounded-2xl border border-[#d7eee6] bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#f0fff9] text-[#008c63]">
                            <x-heroicon-o-document-chart-bar class="h-6 w-6" />
                        </span>
                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">Desde cada ficha</span>
                    </div>
                    <h3 class="mt-4 font-bold text-gray-800">Seguimiento de un proyecto</h3>
                    <p class="mt-1 text-sm text-gray-500 leading-relaxed">Entrá a la ficha de cualquier proyecto y tocá <strong>Reporte del proyecto</strong> para descargar el PDF con avance, tareas, hitos, novedades y entregables. El cliente descarga el suyo con la información aprobada.</p>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
