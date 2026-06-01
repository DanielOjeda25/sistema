<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['proyecto', 'emisor'])->latest()->paginate(10);

        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('facturas.create', compact('proyectos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:255|unique:facturas,numero',
            'monto' => 'required|numeric|min:0',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'estado' => 'required|in:pendiente,pagada,vencida',
            'detalle' => 'nullable|string',
            'proyecto_id' => 'required|exists:proyectos,id',
            'emitida_por' => 'required|exists:users,id',
        ]);

        Factura::create($data);

        return redirect()->route('facturas.index')->with('success', 'Factura creada correctamente.');
    }

    public function show(Factura $factura)
    {
        $factura->load(['proyecto', 'emisor']);

        return view('facturas.show', compact('factura'));
    }

    public function edit(Factura $factura)
    {
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('facturas.edit', compact('factura', 'proyectos', 'usuarios'));
    }

    public function update(Request $request, Factura $factura)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:255|unique:facturas,numero,' . $factura->id,
            'monto' => 'required|numeric|min:0',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'estado' => 'required|in:pendiente,pagada,vencida',
            'detalle' => 'nullable|string',
            'proyecto_id' => 'required|exists:proyectos,id',
            'emitida_por' => 'required|exists:users,id',
        ]);

        $factura->update($data);

        return redirect()->route('facturas.index')->with('success', 'Factura actualizada correctamente.');
    }

    public function destroy(Factura $factura)
    {
        $factura->delete();

        return redirect()->route('facturas.index')->with('success', 'Factura eliminada correctamente.');
    }
}
