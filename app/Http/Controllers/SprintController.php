<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Sprint;
use Illuminate\Http\Request;

class SprintController extends Controller
{
    public function index(Request $request)
    {
        $sprints = Sprint::visiblePara($request->user())
            ->with('proyecto')
            ->withCount('tareas')
            ->when($request->filled('q'), function ($query) use ($request) {
                $texto = $request->string('q')->trim()->toString();

                $query->where(function ($subquery) use ($texto) {
                    $subquery->where('nombre', 'like', "%{$texto}%")
                        ->orWhereHas('proyecto', fn ($q) => $q->where('nombre', 'like', "%{$texto}%"));
                });
            })
            ->when($request->filled('proyecto'), fn ($query) =>
                $query->where('proyecto_id', $request->string('proyecto')->toString())
            )
            ->orderBy('proyecto_id')
            ->orderBy('fecha_inicio')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $proyectos = Proyecto::visiblePara($request->user())->orderBy('nombre')->get();

        return view('sprints.index', compact('sprints', 'proyectos'));
    }

    public function create()
    {
        $proyectos = Proyecto::orderBy('nombre')->get();

        return view('sprints.create', compact('proyectos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'proyecto_id' => 'required|exists:proyectos,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        Sprint::create($data);

        return ($request->input('desde_modal') ? redirect()->back() : redirect()->route('sprints.index'))->with('success', 'Sprint creado correctamente.');
    }

    public function edit(Sprint $sprint)
    {
        $proyectos = Proyecto::orderBy('nombre')->get();

        return view('sprints.edit', compact('sprint', 'proyectos'));
    }

    public function update(Request $request, Sprint $sprint)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'proyecto_id' => 'required|exists:proyectos,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $sprint->update($data);

        return ($request->input('desde_modal') ? redirect()->back() : redirect()->route('sprints.index'))->with('success', 'Sprint actualizado correctamente.');
    }

    public function destroy(Sprint $sprint)
    {
        // Las tareas del sprint quedan sin sprint (nullOnDelete), no se borran.
        $sprint->delete();

        return redirect()->route('sprints.index')->with('success', 'Sprint eliminado. Sus tareas quedaron sin sprint.');
    }
}
