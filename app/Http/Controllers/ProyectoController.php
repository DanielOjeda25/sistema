<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::with(['cliente', 'pm'])->latest()->paginate(10);

        return view('proyectos.index', compact('proyectos'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('proyectos.create', compact('clientes', 'usuarios'));
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

        return redirect()->route('proyectos.index')->with('success', 'Proyecto creado correctamente.');
    }

    public function show(Proyecto $proyecto)
    {
        $proyecto->load(['cliente', 'pm', 'tareas', 'hitos', 'facturas']);

        return view('proyectos.show', compact('proyecto'));
    }

    public function edit(Proyecto $proyecto)
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('proyectos.edit', compact('proyecto', 'clientes', 'usuarios'));
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

        return redirect()->route('proyectos.index')->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Proyecto $proyecto)
    {
        $proyecto->delete();

        return redirect()->route('proyectos.index')->with('success', 'Proyecto eliminado correctamente.');
    }
}
