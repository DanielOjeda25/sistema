<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Proyecto;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        // Una factura pendiente cuyo vencimiento ya paso figura como vencida.
        Factura::marcarVencidas();
        [$desde, $hasta] = $this->rangoFechas($request);

        $facturas = Factura::visiblePara($request->user())
            ->with(['proyecto', 'emisor'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $texto = $request->string('q')->trim()->toString();

                $query->where(function ($subquery) use ($texto) {
                    $subquery->where('numero', 'like', "%{$texto}%")
                        ->orWhere('detalle', 'like', "%{$texto}%");
                });
            })
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->string('estado')->toString())
            )
            // Rango por fecha de emision.
            ->when($desde, fn ($query) => $query->whereDate('fecha_emision', '>=', $desde))
            ->when($hasta, fn ($query) => $query->whereDate('fecha_emision', '<=', $hasta))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Listas para los modales de crear/editar del listado.
        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('facturas.index', compact('facturas', 'proyectos', 'usuarios', 'desde', 'hasta'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:255|unique:facturas,numero',
            'monto' => 'required|numeric|min:0.01|max:10000000',
            'fecha_emision' => 'required|date_format:Y-m-d|after:1999-12-31|before:2101-01-01',
            'fecha_vencimiento' => 'nullable|date_format:Y-m-d|after_or_equal:fecha_emision|after:1999-12-31|before:2101-01-01',
            'estado' => 'required|in:pendiente,pagada,vencida',
            'detalle' => 'nullable|string|max:5000',
            'proyecto_id' => 'required|exists:proyectos,id',
            'emitida_por' => ['required', Rule::in(User::internos()->pluck('id'))],
        ]);

        Factura::create($data);

        return ($request->input('desde_modal') ? redirect()->back() : redirect()->route('facturas.index'))->with('success', 'Factura creada correctamente.');
    }

    public function show(Request $request, Factura $factura)
    {
        abort_unless($request->user()->puedeVer($factura), 403);

        $factura->load(['proyecto', 'emisor']);

        $proyectos = Proyecto::orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();

        return view('facturas.show', compact('factura', 'proyectos', 'usuarios'));
    }

    public function update(Request $request, Factura $factura)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:255|unique:facturas,numero,'.$factura->id,
            'monto' => 'required|numeric|min:0.01|max:10000000',
            'fecha_emision' => 'required|date_format:Y-m-d|after:1999-12-31|before:2101-01-01',
            'fecha_vencimiento' => 'nullable|date_format:Y-m-d|after_or_equal:fecha_emision|after:1999-12-31|before:2101-01-01',
            'estado' => 'required|in:pendiente,pagada,vencida',
            'detalle' => 'nullable|string|max:5000',
            'proyecto_id' => 'required|exists:proyectos,id',
            'emitida_por' => ['required', Rule::in(User::internos()->pluck('id'))],
        ]);

        $factura->update($data);

        return ($request->input('desde_modal') ? redirect()->back() : redirect()->route('facturas.index'))->with('success', 'Factura actualizada correctamente.');
    }

    public function destroy(Factura $factura)
    {
        abort_unless(auth()->user()->hasAnyRole('Jefe', 'PM'), 403);

        $factura->delete();

        return redirect()->route('facturas.index')->with('success', 'Factura eliminada correctamente.');
    }

    public function descargarPdf(Request $request, Factura $factura)
    {
        abort_unless($request->user()->puedeVer($factura), 403);

        $factura->load(['proyecto.cliente', 'emisor']);

        $pdf = Pdf::loadView('facturas.pdf', compact('factura'));

        return $pdf->download("factura-{$factura->numero}.pdf");
    }
}
