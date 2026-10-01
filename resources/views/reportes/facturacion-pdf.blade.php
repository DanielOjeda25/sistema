@extends('reportes._layout-pdf')

@section('contenido')
    {{-- Resumen de montos --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="background: #eef7f3; border: 1px solid #d7eee6; padding: 8px; text-align: center;"><div style="font-size: 13px; font-weight: bold; color: #008c63;">{{ $resumen['facturas'] }}</div><div style="font-size: 8px; color: #6b7280; text-transform: uppercase;">Facturas</div></td>
            <td style="background: #eef7f3; border: 1px solid #d7eee6; padding: 8px; text-align: center;"><div style="font-size: 13px; font-weight: bold; color: #008c63;">$ {{ number_format($resumen['facturado'], 0, ',', '.') }}</div><div style="font-size: 8px; color: #6b7280; text-transform: uppercase;">Facturado</div></td>
            <td style="background: #eef7f3; border: 1px solid #d7eee6; padding: 8px; text-align: center;"><div style="font-size: 13px; font-weight: bold; color: #008c63;">$ {{ number_format($resumen['pagado'], 0, ',', '.') }}</div><div style="font-size: 8px; color: #6b7280; text-transform: uppercase;">Cobrado</div></td>
            <td style="background: #f3f4f6; border: 1px solid #d1d5db; padding: 8px; text-align: center;"><div style="font-size: 13px; font-weight: bold; color: #374151;">$ {{ number_format($resumen['pendiente'], 0, ',', '.') }}</div><div style="font-size: 8px; color: #6b7280; text-transform: uppercase;">Pendiente</div></td>
            <td style="background: #fef2f2; border: 1px solid #fecaca; padding: 8px; text-align: center;"><div style="font-size: 13px; font-weight: bold; color: #dc2626;">$ {{ number_format($resumen['vencido'], 0, ',', '.') }}</div><div style="font-size: 8px; color: #dc2626; text-transform: uppercase;">Vencido</div></td>
        </tr>
    </table>

    {{-- Montos por proyecto, numerados --}}
    <table class="detalle">
        <thead>
            <tr>
                <th style="width: 24px;">Nº</th>
                <th>Proyecto</th>
                <th>Cliente</th>
                <th>Facturas</th>
                <th>Facturado</th>
                <th>Cobrado</th>
                <th>Pendiente</th>
                <th>Vencido</th>
                <th>Cobrado %</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($proyectos as $i => $fila)
                <tr>
                    <td>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $fila['nombre'] }}</td>
                    <td>{{ $fila['cliente'] }}</td>
                    <td>{{ $fila['facturas'] }}</td>
                    <td>$ {{ number_format($fila['facturado'], 0, ',', '.') }}</td>
                    <td>$ {{ number_format($fila['pagado'], 0, ',', '.') }}</td>
                    <td>$ {{ number_format($fila['pendiente'], 0, ',', '.') }}</td>
                    <td @if ($fila['vencido'] > 0) style="color: #dc2626; font-weight: bold;" @endif>$ {{ number_format($fila['vencido'], 0, ',', '.') }}</td>
                    <td>{{ $fila['facturado'] > 0 ? round($fila['pagado'] * 100 / $fila['facturado']) : 0 }}%</td>
                </tr>
            @empty
                <tr><td colspan="9">No hay facturas en el período filtrado.</td></tr>
            @endforelse
        </tbody>
    </table>
    <p class="mostrando">Mostrando {{ $proyectos->count() }} proyecto(s) del conjunto filtrado.</p>

    {{-- Facturacion por mes --}}
    <h2 class="seccion">Facturación por mes</h2>
    <table class="detalle">
        <thead>
            <tr>
                <th>Mes</th>
                <th>Total facturado</th>
                <th>Participación</th>
            </tr>
        </thead>
        <tbody>
            @php($totalMeses = max(collect($porMes)->sum('total'), 1))
            @foreach ($porMes as $mes)
                <tr>
                    <td>{{ $mes['mes'] }}</td>
                    <td>$ {{ number_format($mes['total'], 0, ',', '.') }}</td>
                    <td>{{ number_format($mes['total'] * 100 / $totalMeses, 1, ',', '.') }} %</td>
                </tr>
            @endforeach
            <tr>
                <td><strong>TOTAL</strong></td>
                <td><strong>$ {{ number_format($resumen['facturado'], 0, ',', '.') }}</strong></td>
                <td><strong>100 %</strong></td>
            </tr>
        </tbody>
    </table>

    <h2 class="seccion">Resumen final del período</h2>
    <div class="criterio">
        <p>
            Cobrado: {{ $resumen['facturado'] > 0 ? round($resumen['pagado'] * 100 / $resumen['facturado']) : 0 }}% del total facturado.
            Pendiente de cobro + vencido = $ {{ number_format($resumen['pendiente'] + $resumen['vencido'], 0, ',', '.') }}.
        </p>
        <p>
            <strong>Total filtrado: {{ $resumen['facturas'] }} facturas por $ {{ number_format($resumen['facturado'], 0, ',', '.') }}.</strong>
        </p>
    </div>
@endsection
