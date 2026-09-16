<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1f2937; }
        h1 { color: #4338ca; margin: 0; font-size: 22px; }
        .sub { color: #6b7280; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f3f4f6; width: 180px; }
        .total td { font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <h1>CRUZNEGRA</h1>
    <p class="sub">Factura {{ $factura->numero }} — emitida el {{ $factura->fecha_emision->format('d/m/Y') }}</p>

    <table>
        <tr><th>Cliente</th><td>{{ $factura->proyecto->cliente->nombre ?? '—' }}</td></tr>
        <tr><th>Proyecto</th><td>{{ $factura->proyecto->nombre }}</td></tr>
        <tr><th>Emitida por</th><td>{{ $factura->emisor->name ?? '—' }}</td></tr>
        <tr><th>Estado</th><td>{{ ucfirst($factura->estado) }}</td></tr>
        <tr><th>Detalle</th><td>{{ $factura->detalle ?? '—' }}</td></tr>
        <tr><th>Vencimiento</th><td>{{ $factura->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td></tr>
        <tr class="total"><th>Total</th><td>$ {{ number_format((float) $factura->monto, 2, ',', '.') }}</td></tr>
    </table>
</body>
</html>
