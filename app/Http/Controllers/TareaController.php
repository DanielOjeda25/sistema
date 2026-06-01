<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index()
    {
        $tareas = Tarea::with(['proyecto', 'asignado'])->latest()->paginate(10);

        return view('tareas.index', compact('tareas'));
    }

    public function create()
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();
        $solicitudes = SolicitudCambio::orderBy('titulo')->get();

        return view('tareas.create', compact('proyectos', 'usuarios', 'solicitudes'));
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
            'asignado_a' => 'required|exists:users,id',
            'solicitud_cambio_id' => 'nullable|exists:solicitudes_cambio,id',
        ]);

        Tarea::create($data);

        return redirect()->route('tareas.index')->with('success', 'Tarea creada correctamente.');
    }

    public function show(Tarea $tarea)
    {
        $tarea->load(['proyecto', 'asignado', 'solicitudCambio']);

        return view('tareas.show', compact('tarea'));
    }

    public function edit(Tarea $tarea)
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();
        $solicitudes = SolicitudCambio::orderBy('titulo')->get();

        return view('tareas.edit', compact('tarea', 'proyectos', 'usuarios', 'solicitudes'));
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
            'asignado_a' => 'required|exists:users,id',
            'solicitud_cambio_id' => 'nullable|exists:solicitudes_cambio,id',
        ]);

        $tarea->update($data);

        return redirect()->route('tareas.index')->with('success', 'Tarea actualizada correctamente.');
    }

    public function destroy(Tarea $tarea)
    {
        $tarea->delete();

        return redirect()->route('tareas.index')->with('success', 'Tarea eliminada correctamente.');
    }
}
