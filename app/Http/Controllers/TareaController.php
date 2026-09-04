<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\Sprint;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index(Request $request)
{
    $tareas = Tarea::visiblePara($request->user())
        ->with(['proyecto', 'asignado'])
        ->when($request->filled('q'), function ($query) use ($request) {
            $texto = $request->string('q')->trim()->toString();

            $query->where(function ($subquery) use ($texto) {
                $subquery->where('titulo', 'like', "%{$texto}%")
                    ->orWhere('descripcion', 'like', "%{$texto}%");
            });
        })
        ->when($request->filled('estado'), fn ($query) =>
            $query->where('estado', $request->string('estado')->toString())
        )
        ->latest()
        ->paginate(15)
        ->withQueryString();

    return view('tareas.index', compact('tareas'));
}

    public function tablero(Request $request)
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();
        $proyectoId = $request->query('proyecto');
        $sprintId = $request->query('sprint');

        // Los sprints son por proyecto: el selector solo tiene sentido cuando
        // hay un proyecto elegido; con "todos los proyectos" no se filtra.
        $sprints = $proyectoId
            ? Sprint::where('proyecto_id', $proyectoId)->orderBy('fecha_inicio')->orderBy('id')->get()
            : collect();

        // Si cambia el proyecto desde el filtro, el sprint viejo puede quedar
        // seleccionado y no pertenecer al proyecto nuevo: se ignora en ese caso.
        if ($sprintId && ! $sprints->contains('id', (int) $sprintId)) {
            $sprintId = null;
        }

        $tareas = Tarea::visiblePara($request->user())
            ->with(['proyecto', 'asignado', 'sprint'])
            ->when($proyectoId, fn ($q) => $q->where('proyecto_id', $proyectoId))
            ->when($sprintId, fn ($q) => $q->where('sprint_id', $sprintId))
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        // Solo los roles que pueden editar tareas pueden arrastrar tarjetas;
        // para el resto (Programador, Cliente) el tablero es de solo lectura.
        $puedeMover = $request->user()->hasAnyRole('Jefe', 'PM', 'PO');

        // El modal de edición puede abrir tarjetas de cualquier proyecto, así
        // que necesita todos los sprints agrupados por proyecto.
        $sprintsPorProyecto = Sprint::with('proyecto')->get()
            ->sortBy(fn ($s) => [$s->proyecto?->nombre, $s->fecha_inicio?->format('Y-m-d'), $s->id])
            ->groupBy('proyecto.nombre');

        return view('tareas.tablero', compact('tareas', 'proyectos', 'usuarios', 'proyectoId', 'sprints', 'sprintId', 'sprintsPorProyecto', 'puedeMover'));
    }

    public function mover(Request $request)
    {
        $data = $request->validate([
            'columnas' => ['required', 'array'],
            'columnas.*.estado' => ['required', 'in:pendiente,en_progreso,completada,cancelada'],
            'columnas.*.ids' => ['required', 'array'],
            'columnas.*.ids.*' => ['integer', 'exists:tareas,id'],
        ]);

        // El frontend manda las columnas que cambiaron con sus tareas en el
        // orden final. La posición es el índice dentro de la columna.
        foreach ($data['columnas'] as $columna) {
            foreach ($columna['ids'] as $posicion => $id) {
                Tarea::find($id)?->update([
                    'estado' => $columna['estado'],
                    'orden' => $posicion,
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function create()
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();
        $solicitudes = SolicitudCambio::orderBy('titulo')->get();
        $sprints = Sprint::with('proyecto')->orderBy('proyecto_id')->orderBy('fecha_inicio')->get();

        return view('tareas.create', compact('proyectos', 'usuarios', 'solicitudes', 'sprints'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
            'prioridad' => 'required|in:baja,media,alta',
            'fecha_limite' => 'nullable|date',
            'proyecto_id' => 'required|exists:proyectos,id',
            'sprint_id' => 'nullable|exists:sprints,id',
            'asignado_a' => 'required|exists:users,id',
            'solicitud_cambio_id' => 'nullable|exists:solicitudes_cambio,id',
            'orden' => 'nullable|integer',
        ]);

        // Las tarjetas nuevas del tablero se agregan al final de su columna,
        // como en Trello; el orden explícito gana si viene en la petición.
        $data['orden'] ??= ((int) Tarea::where('estado', $data['estado'])->max('orden') + 1);

        $tarea = Tarea::create($data);

        if ($request->wantsJson()) {
            return response()->json($tarea->load(['proyecto', 'asignado', 'sprint']), 201);
        }

        return redirect()->route('tareas.index')->with('success', 'Tarea creada correctamente.');
    }

    public function show(Request $request, Tarea $tarea)
    {
        abort_unless($request->user()->puedeVer($tarea), 403);

        $tarea->load(['proyecto', 'asignado', 'solicitudCambio']);

        return view('tareas.show', compact('tarea'));
    }

    public function edit(Tarea $tarea)
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();
        $solicitudes = SolicitudCambio::orderBy('titulo')->get();
        $sprints = Sprint::with('proyecto')->orderBy('proyecto_id')->orderBy('fecha_inicio')->get();

        return view('tareas.edit', compact('tarea', 'proyectos', 'usuarios', 'solicitudes', 'sprints'));
    }

    public function update(Request $request, Tarea $tarea)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
            'prioridad' => 'required|in:baja,media,alta',
            'fecha_limite' => 'nullable|date',
            'proyecto_id' => 'required|exists:proyectos,id',
            'sprint_id' => 'nullable|exists:sprints,id',
            'asignado_a' => 'required|exists:users,id',
            'solicitud_cambio_id' => 'nullable|exists:solicitudes_cambio,id',
        ]);

        $tarea->update($data);

        if ($request->wantsJson()) {
            return response()->json($tarea->load(['proyecto', 'asignado', 'sprint']));
        }

        return redirect()->route('tareas.index')->with('success', 'Tarea actualizada correctamente.');
    }

    public function destroy(Request $request, Tarea $tarea)
    {
        $tarea->delete();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('tareas.index')->with('success', 'Tarea eliminada correctamente.');
    }
}
