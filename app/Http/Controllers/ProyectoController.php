<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Models\User;
use App\Services\AI\ProjectContextBuilder;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index(Request $request)
    {
        $proyectos = Proyecto::visiblePara($request->user())
            ->with(['cliente', 'pm'])
            ->latest()
            ->paginate(10);

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

    public function show(Request $request, Proyecto $proyecto, ProjectContextBuilder $contextBuilder)
    {
        abort_unless($request->user()->puedeVer($proyecto), 403);

        $proyecto->load(['cliente', 'pm', 'tareas', 'hitos', 'facturas']);

        $actualizaciones = $proyecto->actualizaciones()
            ->with('autor')
            ->when($request->user()->esCliente(), fn ($query) => $query->where('visible_cliente', true))
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

        return view('proyectos.show', compact('proyecto', 'actualizaciones', 'informes', 'progreso'));
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
