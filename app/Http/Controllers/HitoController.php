<?php

namespace App\Http\Controllers;

use App\Models\Hito;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class HitoController extends Controller
{
    public function index(Request $request)
    {
        $hitos = Hito::visiblePara($request->user())
            ->with('proyecto')
            ->when($request->filled('q'), function ($query) use ($request) {
                $texto = $request->string('q')->trim()->toString();

                $query->where(function ($subquery) use ($texto) {
                    $subquery->where('nombre', 'like', "%{$texto}%")
                        ->orWhere('descripcion', 'like', "%{$texto}%");
                });
            })
            ->when($request->filled('estado'), function ($query) use ($request) {
                if ($request->string('estado')->toString() === 'completado') {
                    $query->where('completado', true);
                } elseif ($request->string('estado')->toString() === 'pendiente') {
                    $query->where('completado', false);
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

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

    public function show(Request $request, Hito $hito)
    {
        abort_unless($request->user()->puedeVer($hito), 403);

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
