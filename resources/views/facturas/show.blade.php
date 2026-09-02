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

                <div class="pt-4 flex gap-4 border-t">
                    <a href="{{ route('facturas.edit', $factura) }}" class="text-yellow-600 hover:underline">Editar</a>
                    <a href="{{ route('facturas.index') }}" class="text-gray-600 hover:underline">Volver al listado</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
