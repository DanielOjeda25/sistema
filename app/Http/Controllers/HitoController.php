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
            ->when($request->filled('proyecto_id'), function ($query) use ($request) {
                $query->where('proyecto_id', $request->integer('proyecto_id'));
            })
            ->when($request->filled('fecha_objetivo'), function ($query) use ($request) {
                $valor = $request->string('fecha_objetivo')->toString();

                if ($valor === 'vencidos') {
                    $query->where('completado', false)
                        ->whereDate('fecha_objetivo', '<', today());
                } elseif ($valor === 'proximos_7_dias') {
                    $query->where('completado', false)
                        ->whereDate('fecha_objetivo', '>=', today())
                        ->whereDate('fecha_objetivo', '<=', today()->addDays(7));
                } elseif ($valor === 'rango') {
                    $desde = $request->input('fecha_desde');
                    $hasta = $request->input('fecha_hasta');

                    if ($desde) {
                        $query->whereDate('fecha_objetivo', '>=', $desde);
                    }

                    if ($hasta) {
                        $query->whereDate('fecha_objetivo', '<=', $hasta);
                    }
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Para el filtro: un Cliente solo puede elegir proyectos de su empresa
        $proyectos = Proyecto::visiblePara($request->user())->orderBy('nombre')->get();

        return view('hitos.index', compact('hitos', 'proyectos'));
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

        return ($request->input('desde_modal') ? redirect()->back() : redirect()->route('hitos.index'))->with('success', 'Hito creado correctamente.');
    }

    public function show(Request $request, Hito $hito)
    {
        abort_unless($request->user()->puedeVer($hito), 403);

        $hito->load('proyecto');

        $proyectos = Proyecto::orderBy('nombre')->get();

        return view('hitos.show', compact('hito', 'proyectos'));
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

        return ($request->input('desde_modal') ? redirect()->back() : redirect()->route('hitos.index'))->with('success', 'Hito actualizado correctamente.');
    }

    public function destroy(Hito $hito)
    {
        $hito->delete();

        return redirect()->route('hitos.index')->with('success', 'Hito eliminado correctamente.');
    }
}
