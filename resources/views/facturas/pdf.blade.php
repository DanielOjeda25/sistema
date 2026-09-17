<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1f2937; }
        .cabecera { width: 100%; }
        .cabecera td { border: none; vertical-align: middle; }
        .logo { height: 64px; }
        .titulo-factura { text-align: right; }
        .titulo-factura .numero { font-size: 20px; font-weight: bold; color: #4338ca; }
        .titulo-factura .fecha { color: #6b7280; margin-top: 2px; }
        table.datos { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.datos th, table.datos td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.datos th { background: #f3f4f6; width: 180px; }
        .total td { font-weight: bold; font-size: 14px; }
        .cliente-box { margin-top: 20px; padding: 10px 14px; background: #f3f4f6; border-radius: 6px; }
        .cliente-box h3 { margin: 0 0 6px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; }
        .cliente-box p { margin: 1px 0; }
    </style>
</head>
<body>
    <table class="cabecera">
        <tr>
            <td>
                <img class="logo" src="{{ public_path('images/cruznegra-logo.png') }}" alt="CRUZNEGRA">
            </td>
            <td class="titulo-factura">
                <div class="numero">Factura {{ $factura->numero }}</div>
                <div class="fecha">Emitida el {{ $factura->fecha_emision->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="cliente-box">
        <h3>Datos del cliente</h3>
        <p><strong>{{ $factura->proyecto->cliente->nombre ?? '' }} {{ $factura->proyecto->cliente->apellido ?? '' }}</strong></p>
        <p>{{ $factura->proyecto->cliente->empresa ?? '' }}</p>
        <p>{{ $factura->proyecto->cliente->email ?? '' }} @if($factura->proyecto->cliente->telefono) · {{ $factura->proyecto->cliente->telefono }} @endif</p>
    </div>

    <table class="datos">
        <tr><th>Proyecto</th><td>{{ $factura->proyecto->nombre }}</td></tr>
        <tr><th>Emitida por</th><td>{{ $factura->emisor->name ?? '—' }}</td></tr>
        <tr><th>Estado</th><td>{{ ucfirst($factura->estado) }}</td></tr>
        <tr><th>Detalle</th><td>{{ $factura->detalle ?? '—' }}</td></tr>
        <tr><th>Vencimiento</th><td>{{ $factura->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td></tr>
        <tr class="total"><th>Total</th><td>$ {{ number_format((float) $factura->monto, 2, ',', '.') }}</td></tr>
    </table>
</body>
</html>
