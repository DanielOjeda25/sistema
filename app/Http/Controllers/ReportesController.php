<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Reportes consolidados para el equipo interno (Jefe, PM, PO).
 *
 * No repiten las tablas de los listados: cruzan proyectos, tareas, hitos y
 * solicitudes para responder "como viene todo y que necesita atencion".
 */
class ReportesController extends Controller
{
    public function index(Request $request)
    {
        return view('reportes.index');
    }

    /**
     * Reporte "Estado y avance de proyectos": filtros, metricas por proyecto,
     * totales del periodo y exportacion.
     */
    public function proyectos(Request $request)
    {
        [$filas, $resumen] = $this->armarReporteProyectos($request);

        // La tabla pagina, pero los totales y los graficos se calculan sobre
        // el conjunto filtrado completo ($filas).
        $proyectos = new LengthAwarePaginator(
            $filas->forPage(LengthAwarePaginator::resolveCurrentPage(), 15)->values(),
            $filas->count(),
            15,
        );
        $proyectos->withQueryString();

        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $pms = User::role(['Jefe', 'PM'])->orderBy('name')->get(['id', 'name']);
        $estados = [
            'pendiente' => 'Pendiente',
            'en_progreso' => 'En progreso',
            'completado' => 'Completado',
            'cancelado' => 'Cancelado',
        ];

        // Datos listos para los graficos Chart.js de la pantalla.
        $donutEstados = [
            ['etiqueta' => 'Pendientes', 'valor' => $filas->where('estado', 'pendiente')->count(), 'color' => '#94a3b8'],
            ['etiqueta' => 'En progreso', 'valor' => $filas->where('estado', 'en_progreso')->count(), 'color' => '#38bdf8'],
            ['etiqueta' => 'Completados', 'valor' => $filas->where('estado', 'completado')->count(), 'color' => '#00b87d'],
            ['etiqueta' => 'Cancelados', 'valor' => $filas->where('estado', 'cancelado')->count(), 'color' => '#f87171'],
        ];
        $avancePorProyecto = $filas->sortByDesc('avance')->values()
            ->map(fn ($p) => [
                'etiqueta' => $p['nombre'],
                'valor' => $p['avance'],
                'color' => $p['retrasado'] ? '#f87171' : '#00b87d',
            ])->all();

        return view('reportes.proyectos', compact('filas', 'proyectos', 'resumen', 'clientes', 'pms', 'estados', 'donutEstados', 'avancePorProyecto'));
    }

    /**
     * Exportacion del reporte de proyectos: CSV descargable o PDF via dompdf.
     * Respeta exactamente los mismos filtros que la pantalla.
     */
    public function exportarProyectos(Request $request)
    {
        [$proyectos, $resumen] = $this->armarReporteProyectos($request);
        $formato = $request->string('formato')->toString() === 'pdf' ? 'pdf' : 'csv';

        if ($formato === 'pdf') {
            $clienteNombre = $request->filled('cliente_id')
                ? Cliente::find($request->integer('cliente_id'))?->nombre ?? 'todos' : 'Todos';
            $pmNombre = $request->filled('pm_id')
                ? User::find($request->integer('pm_id'))?->name ?? 'todos' : 'Todos';

            $pdf = Pdf::loadView('reportes.proyectos-pdf', [
                'proyectos' => $proyectos,
                'resumen' => $resumen,
                'titulo' => 'Estado y avance de proyectos',
                'subtitulo' => 'Módulo Proyectos · Reporte de estado y avance',
                'codigo' => $this->codigoReporte('pro'),
                'emitidoPor' => $request->user()->name,
                'fechaHora' => now()->format('d/m/Y H:i'),
                'desde' => $request->filled('desde') ? $request->date('desde')->format('d/m/Y') : 'Inicio',
                'hasta' => $request->filled('hasta') ? $request->date('hasta')->format('d/m/Y') : 'Hoy',
                'filtrosTexto' => "Cliente: {$clienteNombre} · PM: {$pmNombre} · Estado: "
                    . ($request->filled('estado') ? ucfirst(str_replace('_', ' ', $request->string('estado'))) : 'Todos'),
                'banner' => "{$resumen['total']} proyectos encontrados",
                'orden' => 'Orden: fecha de inicio, más antiguo primero',
                'criterio' => 'Incluye todos los proyectos que cumplen los filtros aplicados; los totales corresponden al conjunto completo, no solo a los registros visibles. '
                    . 'Un proyecto se marca RETRASADO cuando su fecha de fin estimada ya venció sin estar completado ni cancelado. '
                    . 'El avance se calcula como tareas completadas dividido por tareas totales del proyecto.',
            ])->setPaper('a4', 'portrait');

            return $request->boolean('inline')
                ? $pdf->stream('reporte-proyectos-' . now()->format('Ymd-Hi') . '.pdf')
                : $pdf->download('reporte-proyectos-' . now()->format('Ymd-Hi') . '.pdf');
        }

        $nombre = 'reporte-proyectos-' . now()->format('Ymd-Hi') . '.csv';

        return new StreamedResponse(function () use ($proyectos, $resumen) {
            $salida = fopen('php://output', 'w');
            // BOM para que Excel respete los acentos.
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, ['Proyecto', 'Cliente', 'PM', 'Estado', 'Inicio', 'Fin estimada', 'Tareas', 'Completadas', 'Avance %', 'Tareas vencidas', 'Hitos vencidos', 'Hitos proximos', 'Solicitudes pendientes', 'Alerta']);

            foreach ($proyectos as $fila) {
                fputcsv($salida, [
                    $fila['nombre'],
                    $fila['cliente'],
                    $fila['pm'],
                    $fila['estado_texto'],
                    $fila['inicio'],
                    $fila['fin_estimada'],
                    $fila['tareas_total'],
                    $fila['tareas_completadas'],
                    $fila['avance'] . '%',
                    $fila['tareas_vencidas'],
                    $fila['hitos_vencidos'],
                    $fila['hitos_proximos'],
                    $fila['solicitudes_pendientes'],
                    $fila['retrasado'] ? 'RETRASADO' : '',
                ]);
            }

            fputcsv($salida, []);
            fputcsv($salida, ['TOTAL', '', '', '', '', '', $resumen['tareas_total'], $resumen['tareas_completadas'], $resumen['avance_global'] . '%', $resumen['tareas_vencidas'], $resumen['hitos_vencidos'], $resumen['hitos_proximos'], $resumen['solicitudes_pendientes'], '']);
            fclose($salida);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombre}\"",
        ]);
    }

    /**
     * Reporte "Facturacion y cobranzas": montos por estado agrupados por
     * proyecto y por mes, con filtros de cliente, proyecto y periodo.
     * Solo Jefe y PM (la ruta lo fuerza).
     */
    public function facturacion(Request $request)
    {
        [$proyectos, $resumen, $porMes] = $this->armarReporteFacturacion($request);

        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre']);
        $proyectosLista = Proyecto::orderBy('nombre')->get(['id', 'nombre']);

        // Datos listos para los graficos de la pantalla.
        $donutMontos = [
            ['etiqueta' => 'Cobrado', 'valor' => $resumen['pagado'], 'color' => '#00b87d'],
            ['etiqueta' => 'Pendiente', 'valor' => $resumen['pendiente'], 'color' => '#f59e0b'],
            ['etiqueta' => 'Vencido', 'valor' => $resumen['vencido'], 'color' => '#f87171'],
        ];
        $barrasMes = $porMes->map(fn ($m) => ['etiqueta' => $m['mes'], 'valor' => $m['total']])->all();

        return view('reportes.facturacion', compact('proyectos', 'resumen', 'porMes', 'clientes', 'proyectosLista', 'donutMontos', 'barrasMes'));
    }

    public function exportarFacturacion(Request $request)
    {
        [$proyectos, $resumen, $porMes] = $this->armarReporteFacturacion($request);
        $formato = $request->string('formato')->toString() === 'pdf' ? 'pdf' : 'csv';

        if ($formato === 'pdf') {
            $clienteNombre = $request->filled('cliente_id')
                ? Cliente::find($request->integer('cliente_id'))?->nombre ?? 'todos' : 'Todos';
            $proyectoNombre = $request->filled('proyecto_id')
                ? Proyecto::find($request->integer('proyecto_id'))?->nombre ?? 'todos' : 'Todos';

            $pdf = Pdf::loadView('reportes.facturacion-pdf', [
                'proyectos' => $proyectos,
                'resumen' => $resumen,
                'porMes' => $porMes,
                'titulo' => 'Facturación y cobranzas',
                'subtitulo' => 'Módulo Facturas · Reporte de resumen',
                'codigo' => $this->codigoReporte('fac'),
                'emitidoPor' => $request->user()->name,
                'fechaHora' => now()->format('d/m/Y H:i'),
                'desde' => $request->filled('desde') ? $request->date('desde')->format('d/m/Y') : 'Inicio',
                'hasta' => $request->filled('hasta') ? $request->date('hasta')->format('d/m/Y') : 'Hoy',
                'filtrosTexto' => "Cliente: {$clienteNombre} · Proyecto: {$proyectoNombre} · "
                    . 'Importes en moneda local',
                'banner' => "{$resumen['facturas']} facturas en el conjunto filtrado",
                'orden' => 'Orden: monto facturado, mayor primero',
                'criterio' => 'Incluye las facturas emitidas dentro del período y filtros seleccionados, agrupadas por proyecto. '
                    . 'Pendiente + vencido representan los importes aún no cobrados. '
                    . 'La participación por mes se calcula sobre el total facturado del conjunto filtrado.',
            ])->setPaper('a4', 'portrait');

            return $request->boolean('inline')
                ? $pdf->stream('reporte-facturacion-' . now()->format('Ymd-Hi') . '.pdf')
                : $pdf->download('reporte-facturacion-' . now()->format('Ymd-Hi') . '.pdf');
        }

        $nombre = 'reporte-facturacion-' . now()->format('Ymd-Hi') . '.csv';

        return new StreamedResponse(function () use ($proyectos, $resumen, $porMes) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, ['Proyecto', 'Cliente', 'Facturas', 'Facturado', 'Pagado', 'Pendiente', 'Vencido']);

            foreach ($proyectos as $fila) {
                fputcsv($salida, [
                    $fila['nombre'], $fila['cliente'], $fila['facturas'],
                    $fila['facturado'], $fila['pagado'], $fila['pendiente'], $fila['vencido'],
                ]);
            }

            fputcsv($salida, []);
            fputcsv($salida, ['TOTAL', '', $resumen['facturas'], $resumen['facturado'], $resumen['pagado'], $resumen['pendiente'], $resumen['vencido']]);
            fputcsv($salida, []);
            fputcsv($salida, ['Facturacion por mes']);
            foreach ($porMes as $mes) {
                fputcsv($salida, [$mes['mes'], $mes['total']]);
            }
            fclose($salida);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombre}\"",
        ]);
    }

    /**
     * Reporte PDF de un proyecto puntual. Interno ve todo; el Cliente solo
     * entra a proyectos de su empresa y ve lo aprobado para su vista.
     */
    public function proyectoPdf(Request $request, Proyecto $proyecto)
    {
        $usuario = $request->user();
        abort_unless($usuario->puedeVer($proyecto), 403);

        $esCliente = $usuario->esCliente();

        $proyecto->load(['cliente', 'pm', 'hitos', 'solicitudesCambio.solicitante', 'tareas.asignado']);

        $tareas = $proyecto->tareas;
        $total = $tareas->count();
        $completadas = $tareas->where('estado', 'completada')->count();

        $resumen = [
            'avance' => $total > 0 ? (int) round($completadas * 100 / $total) : 0,
            'tareas_total' => $total,
            'tareas_completadas' => $completadas,
            'tareas_en_progreso' => $tareas->where('estado', 'en_progreso')->count(),
            'tareas_pendientes' => $tareas->where('estado', 'pendiente')->count(),
            'hitos_completados' => $proyecto->hitos->where('completado', true)->count(),
            'hitos_total' => $proyecto->hitos->count(),
        ];

        // El Cliente ve solo lo aprobado para su vista.
        $actualizaciones = $proyecto->actualizaciones()->with('autor')
            ->where('visible_cliente', $esCliente)
            ->latest('fecha')->take(6)->get();
        $entregables = $proyecto->entregablesIa()->with('generador')
            ->when($esCliente, fn ($q) => $q->where('estado', 'aprobado')->where('visible_cliente', true))
            ->latest('generado_en')->take(6)->get();

        $pdf = Pdf::loadView('reportes.proyecto-pdf', [
            'proyecto' => $proyecto,
            'resumen' => $resumen,
            'tareas' => $esCliente ? null : $tareas->sortBy('estado')->values(),
            'hitos' => $proyecto->hitos->sortBy('fecha_objetivo')->values(),
            'actualizaciones' => $actualizaciones,
            'entregables' => $entregables,
            'solicitudes' => $esCliente ? null : $proyecto->solicitudesCambio,
            'esCliente' => $esCliente,
            'titulo' => $proyecto->nombre,
            'subtitulo' => ($esCliente ? 'Reporte para el cliente' : 'Módulo Proyectos · Reporte interno de seguimiento')
                . ' · ' . ($proyecto->cliente?->nombre ?? ''),
            'codigo' => $this->codigoReporte('seg'),
            'emitidoPor' => $usuario->name,
            'fechaHora' => now()->format('d/m/Y H:i'),
            'desde' => $proyecto->fecha_inicio?->format('d/m/Y') ?? '—',
            'hasta' => $proyecto->fecha_fin_estimada?->format('d/m/Y') ?? '—',
            'filtrosTexto' => $esCliente
                ? 'Contiene únicamente información aprobada para el cliente'
                : 'Alcance completo: tareas, hitos, solicitudes de cambio, novedades y entregables',
            'banner' => $esCliente
                ? 'Situación del proyecto a la fecha de emisión'
                : 'Seguimiento completo: ' . $resumen['tareas_total'] . ' tareas · ' . $resumen['hitos_total'] . ' hitos',
            'orden' => 'Hitos por fecha objetivo · tareas por estado',
            'criterio' => $esCliente
                ? 'Este documento contiene únicamente información aprobada para la vista del cliente: avance, hitos, novedades visibles y material aprobado.'
                : 'Documento de situación del proyecto a la fecha de emisión: incluye todas las tareas, hitos, solicitudes de cambio y novedades registradas.',
            'criterioTitulo' => $esCliente ? 'Alcance del documento' : 'Criterio del reporte',
        ])->setPaper('a4', 'portrait');

        return $request->boolean('inline')
            ? $pdf->stream('reporte-' . \Illuminate\Support\Str::slug($proyecto->nombre) . '-' . now()->format('Ymd') . '.pdf')
            : $pdf->download('reporte-' . \Illuminate\Support\Str::slug($proyecto->nombre) . '-' . now()->format('Ymd') . '.pdf');
    }

    /**
     * Arma el reporte: aplica filtros, calcula metricas por proyecto y los
     * totales del conjunto. La usan la pantalla y las dos exportaciones.
     */
    /**
     * Codigo de emision del reporte (REP-PRO-2026-001): identificador
     * secuencial por tipo y anio, para poder rastrear cada emision.
     */
    private function codigoReporte(string $prefijo): string
    {
        $anio = now()->format('Y');
        $numero = Cache::increment("reportes.secuencia.{$prefijo}.{$anio}");

        return sprintf('REP-%s-%s-%03d', strtoupper($prefijo), $anio, $numero);
    }

    private function armarReporteProyectos(Request $request): array
    {
        $hoy = today();

        $consulta = Proyecto::query()
            ->with(['cliente', 'pm', 'hitos', 'solicitudesCambio', 'tareas:id,proyecto_id,estado,fecha_limite'])
            // Filtros de la pantalla; el periodo filtra por fecha de inicio.
            ->when($request->filled('cliente_id'), fn ($q) => $q->where('cliente_id', $request->integer('cliente_id')))
            ->when($request->filled('pm_id'), fn ($q) => $q->where('pm_id', $request->integer('pm_id')))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->string('estado')->toString()))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('fecha_inicio', '>=', $request->date('desde')))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('fecha_inicio', '<=', $request->date('hasta')))
            ->orderBy('fecha_inicio');

        $filas = $consulta->get()->map(function (Proyecto $proyecto) use ($hoy) {
            $tareas = $proyecto->tareas;
            $total = $tareas->count();
            $completadas = $tareas->where('estado', 'completada')->count();
            $vencidas = $tareas->whereNotIn('estado', ['completada', 'cancelada'])
                ->filter(fn ($t) => $t->fecha_limite !== null && $t->fecha_limite->isPast())->count();

            $hitos = $proyecto->hitos;
            $hitosVencidos = $hitos->where('completado', false)->filter(fn ($h) => $h->fecha_objetivo->isPast())->count();
            $hitosProximos = $hitos->where('completado', false)
                ->filter(fn ($h) => $h->fecha_objetivo->between($hoy, $hoy->copy()->addDays(7)))->count();

            $pendientes = $proyecto->solicitudesCambio->where('estado', 'pendiente')->count();

            $retrasado = ! in_array($proyecto->estado, ['completado', 'cancelado'])
                && $proyecto->fecha_fin_estimada !== null
                && $proyecto->fecha_fin_estimada->isPast();

            return [
                'id' => $proyecto->id,
                'nombre' => $proyecto->nombre,
                'cliente' => $proyecto->cliente?->nombre ?? '—',
                'pm' => $proyecto->pm?->name ?? '—',
                'estado' => $proyecto->estado,
                'estado_texto' => ucfirst(str_replace('_', ' ', $proyecto->estado)),
                'inicio' => $proyecto->fecha_inicio?->format('d/m/Y') ?? '—',
                'fin_estimada' => $proyecto->fecha_fin_estimada?->format('d/m/Y') ?? '—',
                'tareas_total' => $total,
                'tareas_completadas' => $completadas,
                'avance' => $total > 0 ? (int) round($completadas * 100 / $total) : 0,
                'tareas_vencidas' => $vencidas,
                'hitos_vencidos' => $hitosVencidos,
                'hitos_proximos' => $hitosProximos,
                'solicitudes_pendientes' => $pendientes,
                'retrasado' => $retrasado,
            ];
        });

        // Totales del conjunto filtrado (no de la pagina).
        $tareasTotal = $filas->sum('tareas_total');
        $completadas = $filas->sum('tareas_completadas');

        $resumen = [
            'total' => $filas->count(),
            'completados' => $filas->where('estado', 'completado')->count(),
            'en_progreso' => $filas->where('estado', 'en_progreso')->count(),
            'retrasados' => $filas->where('retrasado', true)->count(),
            'tareas_total' => $tareasTotal,
            'tareas_completadas' => $completadas,
            'avance_global' => $tareasTotal > 0 ? (int) round($completadas * 100 / $tareasTotal) : 0,
            'tareas_vencidas' => $filas->sum('tareas_vencidas'),
            'hitos_vencidos' => $filas->sum('hitos_vencidos'),
            'hitos_proximos' => $filas->sum('hitos_proximos'),
            'solicitudes_pendientes' => $filas->sum('solicitudes_pendientes'),
        ];

        return [$filas, $resumen];
    }

    /**
     * Arma el reporte de facturacion: facturas por proyecto (filtros de
     * cliente, proyecto y periodo por fecha de emision) mas el total por mes.
     */
    private function armarReporteFacturacion(Request $request): array
    {
        $consulta = \App\Models\Factura::query()
            ->with('proyecto.cliente')
            ->when($request->filled('cliente_id'), fn ($q) => $q->whereHas('proyecto', fn ($p) => $p->where('cliente_id', $request->integer('cliente_id'))))
            ->when($request->filled('proyecto_id'), fn ($q) => $q->where('proyecto_id', $request->integer('proyecto_id')))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('fecha_emision', '>=', $request->date('desde')))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('fecha_emision', '<=', $request->date('hasta')));

        $facturas = $consulta->get();

        $filas = $facturas->groupBy(fn ($f) => $f->proyecto_id)->values()->map(function ($grupo) {
            $primera = $grupo->first()->proyecto;

            return [
                'id' => $primera?->id,
                'nombre' => $primera?->nombre ?? 'Sin proyecto',
                'cliente' => $primera?->cliente?->nombre ?? '—',
                'facturas' => $grupo->count(),
                'facturado' => (float) $grupo->sum('monto'),
                'pagado' => (float) $grupo->where('estado', 'pagada')->sum('monto'),
                'pendiente' => (float) $grupo->where('estado', 'pendiente')->sum('monto'),
                'vencido' => (float) $grupo->where('estado', 'vencida')->sum('monto'),
            ];
        })->sortByDesc('facturado')->values();

        $resumen = [
            'facturas' => $facturas->count(),
            'facturado' => (float) $facturas->sum('monto'),
            'pagado' => (float) $facturas->where('estado', 'pagada')->sum('monto'),
            'pendiente' => (float) $facturas->where('estado', 'pendiente')->sum('monto'),
            'vencido' => (float) $facturas->where('estado', 'vencida')->sum('monto'),
        ];

        // Ultimos 6 meses con movimiento, mismo formato que el dashboard.
        $formatoMes = \DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', fecha_emision)"
            : "DATE_FORMAT(fecha_emision, '%Y-%m')";
        $porMesDb = (clone $consulta)->selectRaw("{$formatoMes} as mes, SUM(monto) as total")
            ->groupBy('mes')->orderBy('mes')->get()->pluck('total', 'mes');
        $porMes = collect(range(5, 0))->map(function ($i) use ($porMesDb) {
            $clave = now()->subMonths($i)->format('Y-m');

            return [
                'mes' => now()->createFromFormat('Y-m', $clave)->translatedFormat('M y'),
                'total' => (float) ($porMesDb[$clave] ?? 0),
            ];
        });

        return [$filas, $resumen, $porMes];
    }
}
