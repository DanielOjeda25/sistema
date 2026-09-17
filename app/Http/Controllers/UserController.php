<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

/**
 * Gestion de Usuarios del sistema (solo accesible para el rol Jefe).
 *
 * Todo el flujo ocurre sobre el listado /usuarios con modales: alta con
 * contrasena provisional (la persona la cambia despues desde su perfil),
 * edicion, baja y cambio de rol. Un usuario tiene UN solo rol; si el rol es
 * Cliente, la cuenta queda atada a una empresa (tabla clientes) y ese vinculo
 * es lo que define que proyectos, tareas y facturas podra ver.
 */
class UserController extends Controller
{
    // Guardar el rol elegido (uno solo) desde el modal de la lista
    public function updateRoles(Request $request, User $user)
    {
        $data = $request->validate([
            'rol' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$data['rol']]);

        return redirect()->back()->with('success', 'Rol actualizado correctamente.');
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

        $roles = Role::orderBy('name')->get();
        $clientes = Cliente::orderBy('nombre')->get();

        return view('users.index', compact('users', 'roles', 'clientes'));
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'estado' => 'required|in:activo,inactivo',
            'rol' => 'required|exists:roles,name',
            'cliente_id' => 'nullable|exists:clientes,id|required_if:rol,Cliente',
            // En edición la contraseña es opcional: vacía = no cambia.
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user->fill([
            'name' => $data['name'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'estado' => $data['estado'],
            // Igual que al crear: solo las cuentas Cliente quedan atadas a una empresa.
            'cliente_id' => $data['rol'] === 'Cliente' ? $data['cliente_id'] : null,
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        $user->syncRoles([$data['rol']]);

        return ($request->input('desde_modal') ? redirect()->back() : redirect()->route('users.index'))
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Nadie borra su propia cuenta desde la lista.
        abort_if($user->id === auth()->id(), 403, 'No podés eliminar tu propia cuenta.');

        // Evitamos quedarnos sin ningún Jefe: es el único rol que gestiona usuarios.
        if ($user->hasRole('Jefe') && User::role('Jefe')->count() <= 1) {
            return redirect()->back()->with('error', 'No podés eliminar al único Jefe del sistema.');
        }

        // Si era una cuenta Cliente, su ficha de empresa no debe quedar huérfana.
        $user->cliente?->delete();
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
