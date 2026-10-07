<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Models\User;
use App\Services\AI\ProjectContextBuilder;
use App\Support\LineaDeTiempo;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index(Request $request)
    {
        // Misma URL, dos experiencias: el Cliente ve tarjetas con el avance
        // de sus proyectos; el equipo interno, el listado con filtros.
        if ($request->user()->esCliente()) {
            return $this->misProyectos($request->user());
        }

        return $this->listadoInterno($request);
    }

    /** Portal del Cliente: tarjetas simples con el avance de cada proyecto. */
    private function misProyectos(User $usuario)
    {
        $proyectos = Proyecto::visiblePara($usuario)
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

    /** Listado interno: buscador, filtros y paginación, con modales de alta/edición. */
    private function listadoInterno(Request $request)
    {
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
            ->when($request->filled('cliente_id'), fn ($query) => $query->where('cliente_id', $request->integer('cliente_id')))
            ->when($request->filled('pm_id'), fn ($query) => $query->where('pm_id', $request->integer('pm_id')))
            ->when($request->filled('fecha_desde'), fn ($query) => $query->whereDate('fecha_inicio', '>=', $request->input('fecha_desde')))
            ->when($request->filled('fecha_hasta'), fn ($query) => $query->whereDate('fecha_inicio', '<=', $request->input('fecha_hasta')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Listas para los modales de crear/editar del listado.
        $clientes = Cliente::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();
        // PMs disponibles para el filtro del listado.
        $pms = User::whereHas('roles', fn ($q) => $q->where('name', 'PM'))->orderBy('name')->get();

        // Rango real de fechas registradas: acota los selectores "inicio desde"
        // e "inicio hasta" del filtro (no tiene sentido buscar desde 1999 si
        // no hay proyectos de ese año).
        $limitesFecha = Proyecto::visiblePara($request->user())
            ->selectRaw('MIN(fecha_inicio) as desde, MAX(fecha_inicio) as hasta')
            ->first();
        $limitesFecha->desde = $limitesFecha->desde ? substr($limitesFecha->desde, 0, 10) : null;
        $limitesFecha->hasta = $limitesFecha->hasta ? substr($limitesFecha->hasta, 0, 10) : null;

        return view('proyectos.index', compact('proyectos', 'clientes', 'usuarios', 'pms', 'limitesFecha'));

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

        // Misma URL, dos experiencias: el Cliente ve su portal y el equipo
        // interno, la ficha completa. Cada una arma lo suyo en su método.
        if ($request->user()->esCliente()) {
            return $this->portalCliente($proyecto);
        }

        return $this->fichaInterna($request, $proyecto, $contextBuilder);
    }

    /**
     * Portal del Cliente: línea de tiempo con hitos, sprints, novedades y
     * entregables aprobados. Las reglas de la línea viven en
     * App\Support\LineaDeTiempo; acá solo se reúnen los datos de la vista.
     */
    private function portalCliente(Proyecto $proyecto)
    {
        $proyecto->load(['cliente', 'pm', 'hitos', 'sprints.tareas']);

        $linea = (new LineaDeTiempo)->construir($proyecto);

        $novedades = $proyecto->actualizaciones()
            ->with('autor')
            ->where('visible_cliente', true)
            ->latest('fecha')
            ->latest('id')
            ->take(5)
            ->get();

        // El Cliente solo recibe entregables aprobados (regla del dominio).
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

    /** Ficha completa para los roles internos: todo lo del proyecto. */
    private function fichaInterna(Request $request, Proyecto $proyecto, ProjectContextBuilder $contextBuilder)
    {
        $proyecto->load(['cliente', 'pm', 'tareas', 'hitos', 'facturas']);

        $actualizaciones = $proyecto->actualizaciones()
            ->with('autor')
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
