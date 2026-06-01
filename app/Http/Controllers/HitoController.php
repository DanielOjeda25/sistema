<?php

namespace App\Http\Controllers;

use App\Models\Hito;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class HitoController extends Controller
{
    public function index()
    {
        $hitos = Hito::with('proyecto')->latest()->paginate(10);

        return view('hitos.index', compact('hitos'));
    }

    public function create()
    {
        $proyectos = Proyecto::orderBy('nombre')->get();

        return view('hitos.create', compact('proyectos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_objetivo' => 'required|date',
            'completado' => 'required|boolean',
            'proyecto_id' => 'required|exists:proyectos,id',
        ]);

        Hito::create($data);

        return redirect()->route('hitos.index')->with('success', 'Hito creado correctamente.');
    }

    public function show(Hito $hito)
    {
        $hito->load('proyecto');

        return view('hitos.show', compact('hito'));
    }

    public function edit(Hito $hito)
    {
        $proyectos = Proyecto::orderBy('nombre')->get();

        return view('hitos.edit', compact('hito', 'proyectos'));
    }

    public function update(Request $request, Hito $hito)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_objetivo' => 'required|date',
            'completado' => 'required|boolean',
            'proyecto_id' => 'required|exists:proyectos,id',
        ]);

        $hito->update($data);

        return redirect()->route('hitos.index')->with('success', 'Hito actualizado correctamente.');
    }

    public function destroy(Hito $hito)
    {
        $hito->delete();

        return redirect()->route('hitos.index')->with('success', 'Hito eliminado correctamente.');
    }
}
