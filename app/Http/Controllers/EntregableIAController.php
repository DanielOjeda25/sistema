<?php

namespace App\Http\Controllers;

use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Http\Request;

class EntregableIAController extends Controller
{
    public function index()
    {
        $entregables = EntregableIA::with(['proyecto', 'generador'])->latest()->paginate(10);

        return view('entregables.index', compact('entregables'));
    }

    public function create()
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('entregables.create', compact('proyectos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'tipo' => 'required|string|max:255',
            'estado' => 'required|in:borrador,revisado,aprobado',
            'proyecto_id' => 'required|exists:proyectos,id',
            'generado_por' => 'required|exists:users,id',
        ]);

        EntregableIA::create($data);

        return redirect()->route('entregables.index')->with('success', 'Entregable creado correctamente.');
    }

    public function show(EntregableIA $entregable)
    {
        $entregable->load(['proyecto', 'generador']);

        return view('entregables.show', compact('entregable'));
    }

    public function edit(EntregableIA $entregable)
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('entregables.edit', compact('entregable', 'proyectos', 'usuarios'));
    }

    public function update(Request $request, EntregableIA $entregable)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'tipo' => 'required|string|max:255',
            'estado' => 'required|in:borrador,revisado,aprobado',
            'proyecto_id' => 'required|exists:proyectos,id',
            'generado_por' => 'required|exists:users,id',
        ]);

        $entregable->update($data);

        return redirect()->route('entregables.index')->with('success', 'Entregable actualizado correctamente.');
    }

    public function destroy(EntregableIA $entregable)
    {
        $entregable->delete();

        return redirect()->route('entregables.index')->with('success', 'Entregable eliminado correctamente.');
    }
}
