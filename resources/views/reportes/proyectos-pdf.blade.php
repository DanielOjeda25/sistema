@extends('reportes._layout-pdf')

@section('contenido')
    {{-- Resumen de metricas: solo lo esencial, el detalle va en la tabla --}}
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <tr>
            <td style="background: #eef7f3; border: 1px solid #d7eee6; padding: 10px; text-align: center;"><div style="font-size: 14px; font-weight: bold; color: #008c63;">{{ $resumen['total'] }}</div><div style="font-size: 8px; color: #6b7280; text-transform: uppercase;">Proyectos</div></td>
            <td style="background: #eef7f3; border: 1px solid #d7eee6; padding: 10px; text-align: center;"><div style="font-size: 14px; font-weight: bold; color: #008c63;">{{ $resumen['avance_global'] }}%</div><div style="font-size: 8px; color: #6b7280; text-transform: uppercase;">Avance global</div></td>
            <td style="background: #fef2f2; border: 1px solid #fecaca; padding: 10px; text-align: center;"><div style="font-size: 14px; font-weight: bold; color: #dc2626;">{{ $resumen['retrasados'] }}</div><div style="font-size: 8px; color: #dc2626; text-transform: uppercase;">Retrasados</div></td>
            <td style="background: #fef2f2; border: 1px solid #fecaca; padding: 10px; text-align: center;"><div style="font-size: 14px; font-weight: bold; color: #dc2626;">{{ $resumen['tareas_vencidas'] }}</div><div style="font-size: 8px; color: #dc2626; text-transform: uppercase;">Tareas vencidas</div></td>
            <td style="background: #fef2f2; border: 1px solid #fecaca; padding: 10px; text-align: center;"><div style="font-size: 14px; font-weight: bold; color: #dc2626;">{{ $resumen['hitos_vencidos'] }}</div><div style="font-size: 8px; color: #dc2626; text-transform: uppercase;">Hitos vencidos</div></td>
        </tr>
    </table>

    {{-- Detalle numerado por proyecto --}}
    <table class="detalle">
        <thead>
            <tr>
                <th style="width: 24px;">Nº</th>
                <th>Proyecto</th>
                <th>Cliente</th>
                <th>PM</th>
                <th>Estado</th>
                <th>Avance</th>
                <th>Tareas</th>
                <th>Vencidas</th>
                <th>Hitos v./p.</th>
                <th>Cambios</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($proyectos as $i => $fila)
                <tr>
                    <td>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $fila['nombre'] }} @if ($fila['retrasado']) <span style="color: #dc2626; font-weight: bold;">(retrasado)</span> @endif</td>
                    <td>{{ $fila['cliente'] }}</td>
                    <td>{{ $fila['pm'] }}</td>
                    <td>{{ $fila['estado_texto'] }}</td>
                    <td>{{ $fila['avance'] }}%</td>
                    <td>{{ $fila['tareas_completadas'] }}/{{ $fila['tareas_total'] }}</td>
                    <td @if ($fila['tareas_vencidas'] > 0) style="color: #dc2626; font-weight: bold;" @endif>{{ $fila['tareas_vencidas'] }}</td>
                    <td @if ($fila['hitos_vencidos'] > 0) style="color: #dc2626; font-weight: bold;" @endif>{{ $fila['hitos_vencidos'] }}/{{ $fila['hitos_proximos'] }}</td>
                    <td>{{ $fila['solicitudes_pendientes'] }}</td>
                </tr>
            @empty
                <tr><td colspan="10">Ningún proyecto cumple con los filtros aplicados.</td></tr>
            @endforelse
        </tbody>
    </table>
    <p class="mostrando">Mostrando registros 1-{{ $resumen['total'] }} de {{ $resumen['total'] }}.</p>

    <h2 class="seccion">Resumen final del período</h2>
    <div class="criterio">
        <p>
            <strong>Por estado: {{ $resumen['completados'] }} completados, {{ $resumen['en_progreso'] }} en progreso y {{ $resumen['retrasados'] }} retrasados.
            Total filtrado: {{ $resumen['total'] }} proyectos.</strong>
        </p>
    </div>
@endsection
