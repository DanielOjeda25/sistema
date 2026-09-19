<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Models\User;
use App\Services\AI\ProjectContextBuilder;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index(Request $request)
    {
        // Portal del Cliente: tarjetas simples con el avance de cada proyecto.
        if ($request->user()->esCliente()) {
            $proyectos = Proyecto::visiblePara($request->user())
                ->with(['cliente', 'pm'])
                ->withCount([
                    'tareas',
                    'tareas as tareas_completadas' => fn ($q) => $q->where('estado', 'completada'),
                    'hitos as hitos_completados' => fn ($q) => $q->where('completado', true),
                ])
                ->orderBy('fecha_inicio')
                ->get();

            return view('cliente.proyectos', compact('proyectos'));
        }

        $proyectos = Proyecto::visiblePara($request->user())
            ->with(['cliente', 'pm'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $texto = $request->string('q')->trim()->toString();

                $query->where(function ($subquery) use ($texto) {
                    $subquery->where('nombre', 'like', "%{$texto}%")
                        ->orWhere('descripcion', 'like', "%{$texto}%");
                });
            })
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')->toString())
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Listas para los modales de crear/editar del listado.
        $clientes = Cliente::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('proyectos.index', compact('proyectos', 'clientes', 'usuarios'));

    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin_estimada' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:pendiente,en_progreso,completado,cancelado',
            'cliente_id' => 'required|exists:clientes,id',
            'pm_id' => 'required|exists:users,id',
        ]);

        Proyecto::create($data);

        return ($request->input('desde_modal') ? redirect()->back() : redirect()->route('proyectos.index'))->with('success', 'Proyecto creado correctamente.');
    }

    public function show(Request $request, Proyecto $proyecto, ProjectContextBuilder $contextBuilder)
    {
        abort_unless($request->user()->puedeVer($proyecto), 403);

        // Portal del Cliente: una linea de tiempo simple con hitos, sprints,
        // novedades y entregables aprobados del proyecto.
        if ($request->user()->esCliente()) {
            $proyecto->load(['cliente', 'pm', 'hitos', 'sprints.tareas']);

            $hitos = $proyecto->hitos
                ->map(fn ($h) => [
                    'fecha' => $h->fecha_objetivo,
                    'fecha_texto' => $h->fecha_objetivo?->format('d/m/Y'),
                    'tipo' => 'hito',
                    'titulo' => $h->nombre,
                    'detalle' => $h->descripcion,
                    'descripcion' => $h->descripcion,
                    'hecho' => (bool) $h->completado,
                    'vencido' => ! $h->completado && $h->fecha_objetivo->isPast(),
                ]);

            $sprints = $proyecto->sprints
                ->map(function ($sp) {
                    $total = $sp->tareas->count();
                    $hechas = $sp->tareas->where('estado', 'completada')->count();
                    $fin = $sp->fecha_fin?->format('d/m/Y');
                    $avance = $total > 0 ? (int) round($hechas * 100 / $total) : 0;

                    return [
                        'fecha' => $sp->fecha_inicio,
                        'fecha_texto' => $sp->fecha_inicio?->format('d/m/Y'),
                        'fecha_fin_texto' => $fin,
                        'tipo' => 'sprint',
                        'titulo' => $sp->nombre,
                        'detalle' => trim("{$hechas} de {$total} tareas completadas".($fin ? " · hasta el {$fin}" : '')),
                        'descripcion' => $sp->descripcion,
                        'resumen_ia' => $sp->resumen_ia,
                        'hecho' => $total > 0 && $hechas === $total,
                        'vencido' => false,
                        'avance' => $avance,
                    ];
                });

            $linea = $hitos->concat($sprints)
                ->sortBy(fn ($item) => $item['fecha'])
                ->values();

            $novedades = $proyecto->actualizaciones()
                ->with('autor')
                ->where('visible_cliente', true)
                ->latest('fecha')
                ->latest('id')
                ->take(5)
                ->get();

            $entregables = EntregableIA::where('proyecto_id', $proyecto->id)
                ->where('estado', 'aprobado')
                ->latest('generado_en')
                ->take(5)
                ->get();

            $cambios = $proyecto->solicitudesCambio()
                ->with('solicitante')
                ->latest()
                ->take(5)
                ->get();

            $totalTareas = $proyecto->tareas()->count();
            $tareasHechas = $proyecto->tareas()->where('estado', 'completada')->count();
            $avanceProyecto = $totalTareas > 0 ? (int) round($tareasHechas * 100 / $totalTareas) : 0;

            return view('cliente.proyecto', compact(
                'proyecto', 'linea', 'novedades', 'entregables', 'cambios',
                'totalTareas', 'tareasHechas', 'avanceProyecto'
            ));
        }

        $proyecto->load(['cliente', 'pm', 'tareas', 'hitos', 'facturas']);

        $actualizaciones = $proyecto->actualizaciones()
            ->with('autor')
            ->when($request->user()->esCliente(), fn ($query) => $query->where('visible_cliente', true))
            ->latest('fecha')
            ->latest('id')
            ->get();

        $informes = EntregableIA::visiblePara($request->user())
            ->where('proyecto_id', $proyecto->id)
            ->where('origen', 'ia')
            ->with(['generador', 'aprobador'])
            ->latest('generado_en')
            ->get();

        $progreso = $contextBuilder->build($proyecto)['progreso'];

        $clientes = Cliente::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();
        // La vista incluye los modales de actualizaciones e informes IA,
        // cuyos campos necesitan la lista de proyectos para sus selects.
        $proyectos = Proyecto::orderBy('nombre')->get();

        return view('proyectos.show', compact('proyecto', 'actualizaciones', 'informes', 'progreso', 'clientes', 'usuarios', 'proyectos'));
    }

    public function update(Request $request, Proyecto $proyecto)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin_estimada' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:pendiente,en_progreso,completado,cancelado',
            'cliente_id' => 'required|exists:clientes,id',
            'pm_id' => 'required|exists:users,id',
        ]);

        $proyecto->update($data);

        return ($request->input('desde_modal') ? redirect()->back() : redirect()->route('proyectos.index'))->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Proyecto $proyecto)
    {
        $proyecto->delete();

        return redirect()->route('proyectos.index')->with('success', 'Proyecto eliminado correctamente.');
    }
}
