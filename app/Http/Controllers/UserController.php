<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Cliente;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

// Mostrar el formulario con los roles
    public function editRoles(User $user)
    {
        // Traemos todos los roles de la base de datos
        $roles = Role::all(); 
        
        return view('users.roles', compact('user', 'roles'));
    }

    // Guardar los roles seleccionados
    public function updateRoles(Request $request, User $user)
    {
        // Spatie tiene un método mágico llamado "syncRoles".
        // Lo que hace es: mira los roles que llegaron del formulario, 
        // se los asigna al usuario, y le quita los que no estén marcados.
        $user->syncRoles($request->roles);

        // Volvemos a la página anterior con un mensaje de éxito
        return redirect()->back()->with('success', 'Roles actualizados correctamente.');
    }



    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with('cliente')
            ->when($request->filled('q'), function ($query) use ($request) {
                $texto = $request->string('q')->trim()->toString();

                $query->where(function ($subquery) use ($texto) {
                    $subquery->where('name', 'like', "%{$texto}%")
                        ->orWhere('apellido', 'like', "%{$texto}%")
                        ->orWhere('email', 'like', "%{$texto}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $clientes = Cliente::orderBy('nombre')->get();

        return view('users.create', compact('roles', 'clientes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'estado' => 'required|in:activo,inactivo',
            // La contraseña es provisional: se la damos a la persona y ella la
            // cambia desde su perfil cuando entra por primera vez.
            'password' => ['required', 'confirmed', Password::min(8)],
            'rol' => 'required|exists:roles,name',
            // La empresa solo aplica a cuentas de Cliente: es lo que define
            // qué proyectos, tareas y facturas va a ver.
            'cliente_id' => 'nullable|exists:clientes,id|required_if:rol,Cliente',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'estado' => $data['estado'],
            'password' => Hash::make($data['password']),
            // Un rol interno nunca queda vinculado a una empresa, venga lo
            // que venga del formulario.
            'cliente_id' => $data['rol'] === 'Cliente' ? $data['cliente_id'] : null,
        ]);

        $user->syncRoles([$data['rol']]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado. Pasale la contraseña provisional para que entre y la cambie desde su perfil.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
