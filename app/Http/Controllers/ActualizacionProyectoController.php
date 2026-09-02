<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActualizacionProyectoController extends Controller
{
    public function store(Request $request, Proyecto $proyecto): RedirectResponse
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'tipo' => ['required', 'in:avance,problema,decision,proximo_paso'],
            'fecha' => ['required', 'date'],
            'visible_cliente' => ['nullable', 'boolean'],
        ]);

        $proyecto->actualizaciones()->create([
            ...$data,
            'creado_por' => $request->user()->id,
            'visible_cliente' => $request->boolean('visible_cliente'),
        ]);

        return redirect()->route('proyectos.show', $proyecto)
            ->with('success', 'Actualización registrada correctamente.');
    }
}
