<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $auditoria = Audit::with('user')
            ->latest()
            // Busqueda libre: evento o modelo auditado.
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->lower();
                $query->where(function ($sub) use ($q) {
                    $sub->where('event', 'like', "%{$q}%")
                        ->orWhere('auditable_type', 'like', "%{$q}%");
                });
            })
            // Filtro por usuario responsable del cambio.
            ->when($request->filled('usuario'), function ($query) use ($request) {
                $query->where('user_id', $request->string('usuario')->toString());
            })
            // Filtro por accion: created, updated o deleted.
            ->when($request->filled('accion'), function ($query) use ($request) {
                $query->where('event', $request->string('accion')->toString());
            })
            // Historial de un registro concreto (desde el boton Historial de los listados).
            ->when($request->filled('modelo'), function ($query) use ($request) {
                $query->where('auditable_type', $request->string('modelo')->toString())
                    ->when($request->filled('registro'), fn ($q) =>
                        $q->where('auditable_id', $request->string('registro')->toString()));
            })
            ->paginate(20)
            ->withQueryString();

        $usuarios = User::orderBy('name')->get();

        return view('auditoria.index', compact('auditoria', 'usuarios'));
    }
}
