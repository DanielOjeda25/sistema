<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\User;
use Illuminate\Http\Request;

class SolicitudCambioController extends Controller
{
    public function index(Request $request)
{
    $solicitudes = SolicitudCambio::visiblePara($request->user())
        ->with(['proyecto', 'solicitante'])
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

    return view('solicitudes_cambio.index', compact('solicitudes'));
}

    public function create()
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('solicitudes_cambio.create', compact('proyectos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'estado' => 'required|in:pendiente,aprobada,rechazada',
            'prioridad' => 'required|in:baja,media,alta',
            'proyecto_id' => 'required|exists:proyectos,id',
            'solicitado_por' => 'required|exists:users,id',
        ]);

        SolicitudCambio::create($data);

        return redirect()->route('solicitudes-cambio.index')->with('success', 'Solicitud de cambio creada correctamente.');
    }

    public function show(Request $request, SolicitudCambio $solicitudes_cambio)
    {
        abort_unless($request->user()->puedeVer($solicitudes_cambio), 403);

        $solicitudes_cambio->load(['proyecto', 'solicitante', 'tareas']);

        return view('solicitudes_cambio.show', ['solicitud' => $solicitudes_cambio]);
    }

    public function edit(SolicitudCambio $solicitudes_cambio)
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('solicitudes_cambio.edit', [
            'solicitud' => $solicitudes_cambio,
            'proyectos' => $proyectos,
            'usuarios' => $usuarios,
        ]);
    }

    public function update(Request $request, SolicitudCambio $solicitudes_cambio)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'estado' => 'required|in:pendiente,aprobada,rechazada',
            'prioridad' => 'required|in:baja,media,alta',
            'proyecto_id' => 'required|exists:proyectos,id',
            'solicitado_por' => 'required|exists:users,id',
        ]);

        $solicitudes_cambio->update($data);

        return redirect()->route('solicitudes-cambio.index')->with('success', 'Solicitud de cambio actualizada correctamente.');
    }

    public function destroy(SolicitudCambio $solicitudes_cambio)
    {
        $solicitudes_cambio->delete();

        return redirect()->route('solicitudes-cambio.index')->with('success', 'Solicitud de cambio eliminada correctamente.');
    }
}
