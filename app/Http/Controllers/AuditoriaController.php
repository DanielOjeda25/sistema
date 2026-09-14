<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $auditoria = Audit::with('user')
            ->latest()
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->lower();
                $query->where(function ($sub) use ($q) {
                    $sub->where('event', 'like', "%{$q}%")
                        ->orWhere('auditable_type', 'like', "%{$q}%");
                });
            })
            ->paginate(20)
            ->withQueryString();

        return view('auditoria.index', compact('auditoria'));
    }
}
