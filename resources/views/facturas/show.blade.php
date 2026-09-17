<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de la Factura
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-700 space-y-3">

                <p><strong>Número:</strong> {{ $factura->numero }}</p>
                <p><strong>Proyecto:</strong> {{ $factura->proyecto?->nombre ?? 'N/A' }}</p>
                <p><strong>Emitida por:</strong> {{ $factura->emisor?->name ?? 'N/A' }}</p>
                <p><strong>Monto:</strong> ${{ number_format($factura->monto, 2) }}</p>
                <p><strong>Fecha de emisión:</strong> {{ $factura->fecha_emision?->format('d/m/Y') ?? 'N/A' }}</p>
                <p><strong>Fecha de vencimiento:</strong> {{ $factura->fecha_vencimiento?->format('d/m/Y') ?? 'Sin vencimiento' }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($factura->estado) }}</p>
                <p><strong>Detalle:</strong> {{ $factura->detalle ?? 'Sin detalle' }}</p>

                <div class="pt-4 flex items-center gap-4 border-t">
                    @php($valoresFactura = $factura->only(['numero', 'monto', 'estado', 'detalle', 'proyecto_id', 'emitida_por']) + ['fecha_emision' => $factura->fecha_emision?->format('Y-m-d'), 'fecha_vencimiento' => $factura->fecha_vencimiento?->format('Y-m-d')])
                    <button type="button" data-abrir-modal="modal-factura-editar"
                            data-url="{{ route('facturas.update', $factura) }}"
                            data-valores='@json($valoresFactura)'
                            class="text-yellow-600 hover:text-yellow-800 inline-flex" title="Editar" aria-label="Editar">
                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                    </button>
                    <a href="{{ route('facturas.pdf', $factura) }}" class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800" title="Descargar PDF">
                        <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                        Descargar PDF
                    </a>
                    <a href="{{ route('facturas.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
    @hasanyrole('Jefe|PM')
    @include('facturas._modal_editar')
    @endhasanyrole
</x-app-layout>
