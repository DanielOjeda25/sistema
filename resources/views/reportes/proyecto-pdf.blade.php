@extends('reportes._layout-pdf')

@section('contenido')
    {{-- Datos del proyecto --}}
    <table class="detalle" style="margin-bottom: 10px;">
        <tr>
            <td style="width: 90px; color: #6b7280;">Cliente</td><td>{{ $proyecto->cliente?->nombre ?? '—' }}</td>
            <td style="width: 90px; color: #6b7280;">PM a cargo</td><td>{{ $proyecto->pm?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td style="color: #6b7280;">Estado</td><td>{{ ucfirst(str_replace('_', ' ', $proyecto->estado)) }}</td>
            <td style="color: #6b7280;">Período</td><td>{{ $proyecto->fecha_inicio?->format('d/m/Y') ?? '—' }} — {{ $proyecto->fecha_fin_estimada?->format('d/m/Y') ?? '—' }}</td>
        </tr>
        @if ($proyecto->descripcion)
            <tr><td style="color: #6b7280;">Descripción</td><td colspan="3">{{ $proyecto->descripcion }}</td></tr>
        @endif
    </table>

    {{-- Avance --}}
    <table class="metricas">
        <tr>
            <td><div class="valor">{{ $resumen['avance'] }}%</div><div class="rotulo">Avance</div></td>
            <td><div class="valor">{{ $resumen['tareas_completadas'] }}/{{ $resumen['tareas_total'] }}</div><div class="rotulo">Tareas listas</div></td>
            <td><div class="valor">{{ $resumen['tareas_en_progreso'] }}</div><div class="rotulo">En progreso</div></td>
            <td><div class="valor">{{ $resumen['tareas_pendientes'] }}</div><div class="rotulo">Pendientes</div></td>
            <td><div class="valor">{{ $resumen['hitos_completados'] }}/{{ $resumen['hitos_total'] }}</div><div class="rotulo">Hitos</div></td>
        </tr>
    </table>

    <h2 class="seccion">Hitos</h2>
    <table class="detalle">
        <thead>
            <tr>
                <th style="width: 24px;">Nº</th>
                <th>Hito</th>
                <th>Fecha objetivo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hitos as $i => $hito)
                <tr>
                    <td>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $hito->nombre }}</td>
                    <td>{{ $hito->fecha_objetivo->format('d/m/Y') }}</td>
                    <td @if (! $hito->completado && $hito->fecha_objetivo->isPast()) style="color: #dc2626; font-weight: bold;" @elseif ($hito->completado) style="color: #008c63;" @endif>
                        {{ $hito->completado ? 'Completado' : ($hito->fecha_objetivo->isPast() ? 'Vencido' : 'Pendiente') }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Sin hitos cargados.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($tareas !== null)
        <h2 class="seccion">Tareas</h2>
        <table class="detalle">
            <thead>
                <tr>
                    <th style="width: 24px;">Nº</th>
                    <th>Título</th>
                    <th>Responsable</th>
                    <th>Estado</th>
                    <th>Límite</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tareas as $i => $tarea)
                    <tr>
                        <td>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $tarea->titulo }}</td>
                        <td>{{ $tarea->asignado?->name ?? '—' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $tarea->estado)) }}</td>
                        <td>{{ $tarea->fecha_limite?->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Sin tareas cargadas.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if ($solicitudes !== null)
        <h2 class="seccion">Solicitudes de cambio</h2>
        <table class="detalle">
            <thead>
                <tr>
                    <th style="width: 24px;">Nº</th>
                    <th>Título</th>
                    <th>Solicitante</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($solicitudes as $i => $solicitud)
                    <tr>
                        <td>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $solicitud->titulo }}</td>
                        <td>{{ $solicitud->solicitante?->name ?? '—' }}</td>
                        <td>{{ ucfirst($solicitud->prioridad) }}</td>
                        <td>{{ ucfirst($solicitud->estado) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Sin solicitudes de cambio.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <h2 class="seccion">{{ $esCliente ? 'Novedades del equipo' : 'Últimas actualizaciones' }}</h2>
    <div class="criterio">
        @forelse ($actualizaciones as $i => $actualizacion)
            <p>
                <strong>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}. {{ $actualizacion->titulo }}</strong> — {{ $actualizacion->descripcion }}
                <span style="color: #9ca3af;">({{ $actualizacion->autor?->name ?? 'Equipo' }} · {{ $actualizacion->fecha?->format('d/m/Y') }})</span>
            </p>
        @empty
            <p>Sin novedades registradas.</p>
        @endforelse
    </div>

    <h2 class="seccion">{{ $esCliente ? 'Material aprobado' : 'Entregables' }}</h2>
    <div class="criterio">
        @forelse ($entregables as $i => $entregable)
            <p>
                <strong>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}. {{ $entregable->titulo }}</strong>
                <span style="color: #9ca3af;">({{ ucfirst($entregable->tipo) }} · {{ $entregable->generador?->name ?? 'Equipo' }} · {{ $entregable->generado_en?->format('d/m/Y') }})</span>
            </p>
        @empty
            <p>Sin entregables{{ $esCliente ? ' aprobados' : '' }} por ahora.</p>
        @endforelse
    </div>
@endsection
