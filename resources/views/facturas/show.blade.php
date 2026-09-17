<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle de la Factura
            </h2>
            <a href="{{ route('facturas.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                <x-heroicon-o-arrow-left class="w-4 h-4" /> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Factura {{ $factura->numero }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $factura->proyecto?->nombre ?? 'Sin proyecto' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($factura->monto, 2) }}</p>
                        <x-estado-badge :estado="$factura->estado" />
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 px-6 py-5 text-sm">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Fecha de emisión</dt>
                        <dd class="mt-1 text-gray-900">{{ $factura->fecha_emision?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Fecha de vencimiento</dt>
                        <dd class="mt-1 text-gray-900">{{ $factura->fecha_vencimiento?->format('d/m/Y') ?? 'Sin vencimiento' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Proyecto</dt>
                        <dd class="mt-1 text-gray-900">{{ $factura->proyecto?->nombre ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase">Emitida por</dt>
                        <dd class="mt-1 text-gray-900">{{ $factura->emisor?->name ?? '—' }}</dd>
                    </div>
                    @if ($factura->detalle)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase">Detalle</dt>
                        <dd class="mt-1 text-gray-900">{{ $factura->detalle }}</dd>
                    </div>
                    @endif
                </dl>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center gap-3">
                    @hasanyrole('Jefe|PM')
                        @php($valoresFactura = $factura->only(['numero', 'monto', 'estado', 'detalle', 'proyecto_id', 'emitida_por']) + ['fecha_emision' => $factura->fecha_emision?->format('Y-m-d'), 'fecha_vencimiento' => $factura->fecha_vencimiento?->format('Y-m-d')])
                        <button type="button" data-abrir-modal="modal-factura-editar"
                                data-url="{{ route('facturas.update', $factura) }}"
                                data-valores='@json($valoresFactura)'
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            <x-heroicon-o-pencil-square class="w-4 h-4" /> Editar
                        </button>
                    @endhasanyrole
                    <a href="{{ route('facturas.pdf', $factura) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-[#00b87d] rounded-lg text-xs font-semibold text-white uppercase tracking-widest hover:bg-[#008c63]">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" /> Descargar PDF
                    </a>
                    <a href="{{ route('facturas.index') }}" class="ml-auto text-sm text-gray-600 hover:text-gray-900">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    @hasanyrole('Jefe|PM')
        @include('facturas._modal_editar')
    @endhasanyrole
</x-app-layout>
