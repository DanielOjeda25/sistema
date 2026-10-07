<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Administración de usuarios y roles: alta -> edición/rol -> baja, más el
 * cambio de contraseña propia desde el perfil. Un test por tarea; cada uno
 * arma lo que necesita porque la base se reinicia entre tests.
 */
class UsuariosCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function jefe(): User
    {
        return User::where('email', 'jefe@example.com')->firstOrFail();
    }

    #[Test]
    public function un_jefe_puede_crear_un_usuario_cliente_con_su_empresa(): void
    {
        $this->actingAs($this->jefe())->post(route('users.store'), [
            'name' => 'Cliente Nuevo', 'apellido' => 'Prueba', 'email' => 'nuevo@cliente.com',
            'estado' => 'activo', 'password' => 'contrasena123',
            'password_confirmation' => 'contrasena123',
            'rol' => 'Cliente', 'cliente_id' => 1,
        ])->assertRedirect();

        $usuario = User::where('email', 'nuevo@cliente.com')->firstOrFail();

        // El vínculo con la empresa y el rol quedan asignados.
        $this->assertSame(1, (int) $usuario->cliente_id);
        $this->assertTrue($usuario->hasRole('Cliente'));
    }

    #[Test]
    public function un_jefe_puede_editar_un_usuario_y_cambiarle_el_rol(): void
    {
        $usuario = User::create([
            'name' => 'Editable', 'apellido' => 'Usuario', 'email' => 'editable@x.com',
            'estado' => 'activo', 'password' => Hash::make('contrasena123'),
        ]);
        $usuario->assignRole('Programador');

        // Edición: cambia datos, estado y rol en un solo PUT.
        $this->actingAs($this->jefe())->put(route('users.update', $usuario), [
            'name' => 'Editable v2', 'apellido' => 'Usuario', 'email' => 'editable@x.com',
            'estado' => 'inactivo', 'rol' => 'PO',
        ])->assertRedirect();

        $usuario->refresh();
        $this->assertSame('Editable v2', $usuario->name);
        $this->assertSame('inactivo', $usuario->estado);
        $this->assertTrue($usuario->hasRole('PO'));
        $this->assertFalse($usuario->hasRole('Programador'));

        // Sin re-ingresar contraseña, la clave sigue siendo la misma.
        $this->assertTrue(Hash::check('contrasena123', $usuario->password));
    }

    #[Test]
    public function nadie_puede_eliminar_al_unico_jefe_ni_a_si_mismo(): void
    {
        $jefe = $this->jefe();

        $this->actingAs($jefe)->delete(route('users.destroy', $jefe))->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $jefe->id]);
    }

    #[Test]
    public function un_jefe_puede_eliminar_un_usuario(): void
    {
        $usuario = User::create([
            'name' => 'Eliminable', 'apellido' => 'Usuario', 'email' => 'eliminar@x.com',
            'estado' => 'activo', 'password' => Hash::make('contrasena123'),
        ]);
        $usuario->assignRole('Programador');

        $this->actingAs($this->jefe())->delete(route('users.destroy', $usuario))->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
    }

    #[Test]
    public function cualquier_usuario_cambia_su_propia_password_desde_su_perfil(): void
    {
        $user = User::create([
            'name' => 'Cambia', 'apellido' => 'Clave', 'email' => 'cambia@x.com',
            'estado' => 'activo', 'password' => Hash::make('1234'),
        ]);

        // Cambio con la contraseña actual correcta.
        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => '1234',
            'password' => 'nueva-clave-123',
            'password_confirmation' => 'nueva-clave-123',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('nueva-clave-123', $user->fresh()->password));

        // Con la vieja como "actual" el cambio se rechaza y la clave no se toca.
        // El formulario de perfil valida en su propio error bag ("updatePassword").
        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => '1234',
            'password' => 'otra-clave-123',
            'password_confirmation' => 'otra-clave-123',
        ])->assertInvalid('current_password', 'updatePassword');

        $this->assertTrue(Hash::check('nueva-clave-123', $user->fresh()->password));
    }
}
