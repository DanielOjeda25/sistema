<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $clientes = Cliente::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $texto = $request->string('q')->trim()->toString();

                $query->where(function ($subquery) use ($texto) {
                    $subquery->where('nombre', 'like', "%{$texto}%")
                        ->orWhere('apellido', 'like', "%{$texto}%")
                        ->orWhere('email', 'like', "%{$texto}%")
                        ->orWhere('empresa', 'like', "%{$texto}%");
                });
            })
            ->when($request->filled('estado'), fn ($query) =>
                $query->where('estado', $request->string('estado')->toString())
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'telefono' => 'nullable|string|max:50',
            'empresa' => 'nullable|string|max:255',
            'estado' => 'required|in:activo,inactivo',
        ]);

        Cliente::create($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id,
            'telefono' => 'nullable|string|max:50',
            'empresa' => 'nullable|string|max:255',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $cliente->update($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }
}
