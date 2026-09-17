<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\EntregableIA;
use App\Models\Hito;
use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Ciclo de vida completo (crear -> editar -> eliminar) de los modulos
 * cuyo CRUD no estaba cubierto por otros tests, ejercitado por HTTP
 * como lo haria el Jefe.
 */
class CrudModulosTest extends TestCase
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

    /** @test */
    public function ciclo_completo_de_cliente(): void
    {
        $jefe = $this->jefe();

        $this->actingAs($jefe)->post(route('clientes.store'), [
            'nombre' => 'Lucia', 'apellido' => 'Paz', 'email' => 'lucia@emp.com',
            'telefono' => '123', 'empresa' => 'Emp Lucia', 'estado' => 'activo',
        ])->assertRedirect();

        $cliente = Cliente::where('email', 'lucia@emp.com')->firstOrFail();

        $this->actingAs($jefe)->put(route('clientes.update', $cliente), [
            'nombre' => 'Lucia', 'apellido' => 'Paz', 'email' => 'lucia@emp.com',
            'telefono' => '456', 'empresa' => 'Emp Lucia SA', 'estado' => 'inactivo',
        ])->assertRedirect();

        $this->assertDatabaseHas('clientes', ['id' => $cliente->id, 'estado' => 'inactivo']);

        $this->actingAs($jefe)->delete(route('clientes.destroy', $cliente))->assertRedirect();
        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
    }

    /** @test */
    public function ciclo_completo_de_proyecto(): void
    {
        $jefe = $this->jefe();
        $cliente = Cliente::where('email', 'federico@emp.com')->first()
            ?? Cliente::first();

        $this->actingAs($jefe)->post(route('proyectos.store'), [
            'nombre' => 'Proyecto CRUD', 'descripcion' => 'prueba',
            'fecha_inicio' => '2026-10-01', 'fecha_fin_estimada' => '2026-12-01',
            'estado' => 'pendiente', 'cliente_id' => $cliente->id, 'pm_id' => $jefe->id,
        ])->assertRedirect();

        $proyecto = Proyecto::where('nombre', 'Proyecto CRUD')->firstOrFail();

        $this->actingAs($jefe)->put(route('proyectos.update', $proyecto), [
            'nombre' => 'Proyecto CRUD v2', 'fecha_inicio' => '2026-10-01',
            'estado' => 'en_progreso', 'cliente_id' => $cliente->id, 'pm_id' => $jefe->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('proyectos', ['id' => $proyecto->id, 'nombre' => 'Proyecto CRUD v2']);

        $this->actingAs($jefe)->delete(route('proyectos.destroy', $proyecto))->assertRedirect();
        $this->assertDatabaseMissing('proyectos', ['id' => $proyecto->id]);
    }

    /** @test */
    public function ciclo_completo_de_hito(): void
    {
        $jefe = $this->jefe();
        $proyecto = Proyecto::firstOrFail();

        $this->actingAs($jefe)->post(route('hitos.store'), [
            'nombre' => 'Hito CRUD', 'descripcion' => 'prueba',
            'fecha_objetivo' => '2026-11-15', 'completado' => '0', 'proyecto_id' => $proyecto->id,
        ])->assertRedirect();

        $hito = Hito::where('nombre', 'Hito CRUD')->firstOrFail();

        $this->actingAs($jefe)->put(route('hitos.update', $hito), [
            'nombre' => 'Hito CRUD v2', 'fecha_objetivo' => '2026-11-20',
            'completado' => '1', 'proyecto_id' => $proyecto->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('hitos', ['id' => $hito->id, 'completado' => true]);

        $this->actingAs($jefe)->delete(route('hitos.destroy', $hito))->assertRedirect();
        $this->assertDatabaseMissing('hitos', ['id' => $hito->id]);
    }

    /** @test */
    public function ciclo_completo_de_sprint(): void
    {
        $jefe = $this->jefe();
        $proyecto = Proyecto::firstOrFail();

        $this->actingAs($jefe)->post(route('sprints.store'), [
            'nombre' => 'Sprint CRUD', 'proyecto_id' => $proyecto->id,
            'fecha_inicio' => '2026-10-05', 'fecha_fin' => '2026-10-16',
        ])->assertRedirect();

        $sprint = Sprint::where('nombre', 'Sprint CRUD')->firstOrFail();

        $this->actingAs($jefe)->put(route('sprints.update', $sprint), [
            'nombre' => 'Sprint CRUD v2', 'proyecto_id' => $proyecto->id,
            'fecha_inicio' => '2026-10-05', 'fecha_fin' => '2026-10-23',
        ])->assertRedirect();

        $this->assertDatabaseHas('sprints', ['id' => $sprint->id, 'nombre' => 'Sprint CRUD v2']);

        $this->actingAs($jefe)->delete(route('sprints.destroy', $sprint))->assertRedirect();
        $this->assertDatabaseMissing('sprints', ['id' => $sprint->id]);
    }

    /** @test */
    public function ciclo_completo_de_solicitud_de_cambio(): void
    {
        $jefe = $this->jefe();
        $proyecto = Proyecto::firstOrFail();

        $this->actingAs($jefe)->post(route('solicitudes-cambio.store'), [
            'titulo' => 'Solicitud CRUD', 'descripcion' => 'prueba',
            'estado' => 'pendiente', 'prioridad' => 'media',
            'proyecto_id' => $proyecto->id, 'solicitado_por' => $jefe->id,
        ])->assertRedirect();

        $solicitud = SolicitudCambio::where('titulo', 'Solicitud CRUD')->firstOrFail();

        $this->actingAs($jefe)->put(route('solicitudes-cambio.update', $solicitud), [
            'titulo' => 'Solicitud CRUD', 'descripcion' => 'prueba',
            'estado' => 'aprobada', 'prioridad' => 'alta',
            'proyecto_id' => $proyecto->id, 'solicitado_por' => $jefe->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('solicitudes_cambio', ['id' => $solicitud->id, 'estado' => 'aprobada']);

        $this->actingAs($jefe)->delete(route('solicitudes-cambio.destroy', $solicitud))->assertRedirect();
        $this->assertDatabaseMissing('solicitudes_cambio', ['id' => $solicitud->id]);
    }

    /** @test */
    public function ciclo_completo_de_entregable_ia(): void
    {
        $jefe = $this->jefe();
        $proyecto = Proyecto::firstOrFail();

        $this->actingAs($jefe)->post(route('entregables.store'), [
            'titulo' => 'Entregable CRUD', 'contenido' => 'contenido',
            'tipo' => 'informe', 'estado' => 'borrador',
            'proyecto_id' => $proyecto->id, 'generado_por' => $jefe->id,
        ])->assertRedirect();

        $entregable = EntregableIA::where('titulo', 'Entregable CRUD')->firstOrFail();

        $this->actingAs($jefe)->put(route('entregables.update', $entregable), [
            'titulo' => 'Entregable CRUD v2', 'contenido' => 'contenido v2',
            'tipo' => 'informe', 'estado' => 'aprobado',
            'proyecto_id' => $proyecto->id, 'generado_por' => $jefe->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('entregables_ia', ['id' => $entregable->id, 'estado' => 'aprobado']);

        $this->actingAs($jefe)->delete(route('entregables.destroy', $entregable))->assertRedirect();
        $this->assertDatabaseMissing('entregables_ia', ['id' => $entregable->id]);
    }

    /** @test */
    public function el_jefe_crea_un_usuario_cliente_con_su_empresa(): void
    {
        $jefe = $this->jefe();

        $this->actingAs($jefe)->post(route('users.store'), [
            'name' => 'Cliente CRUD', 'apellido' => 'Prueba', 'email' => 'crud@cliente.com',
            'estado' => 'activo', 'password' => 'contrasena123',
            'password_confirmation' => 'contrasena123',
            'rol' => 'Cliente', 'cliente_id' => 1,
        ])->assertRedirect();

        $usuario = User::where('email', 'crud@cliente.com')->firstOrFail();

        // El vinculo con la empresa y el rol quedan asignados.
        $this->assertSame(1, (int) $usuario->cliente_id);
        $this->assertTrue($usuario->hasRole('Cliente'));
    }

    /** @test */
    public function el_jefe_edita_cambia_rol_y_elimina_un_usuario(): void
    {
        $jefe = $this->jefe();

        $this->actingAs($jefe)->post(route('users.store'), [
            'name' => 'Editable', 'apellido' => 'Usuario', 'email' => 'editable@x.com',
            'estado' => 'activo', 'password' => 'contrasena123',
            'password_confirmation' => 'contrasena123',
            'rol' => 'Programador',
        ])->assertRedirect();

        $usuario = User::where('email', 'editable@x.com')->firstOrFail();

        // Edición: cambia datos, estado y rol en un solo PUT.
        $this->actingAs($jefe)->put(route('users.update', $usuario), [
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

        // Nadie borra su propia cuenta ni al único Jefe del sistema.
        $this->actingAs($jefe)->delete(route('users.destroy', $jefe))->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $jefe->id]);

        $this->actingAs($jefe)->delete(route('users.destroy', $usuario))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
    }

    /** @test */
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

    /** @test */
    public function un_usuario_sin_rol_interno_no_puede_crear_nada(): void
    {
        $cliente = User::where('email', 'cliente@example.com')->firstOrFail();

        $this->actingAs($cliente)->post(route('clientes.store'), [
            'nombre' => 'X', 'apellido' => 'Y', 'email' => 'x@x.com', 'estado' => 'activo',
        ])->assertForbidden();

        $this->actingAs($cliente)->post(route('hitos.store'), [
            'nombre' => 'X', 'fecha_objetivo' => '2026-12-01',
            'completado' => '0', 'proyecto_id' => 1,
        ])->assertForbidden();

        $this->actingAs($cliente)->post(route('sprints.store'), [
            'nombre' => 'X', 'proyecto_id' => 1,
        ])->assertForbidden();
    }
}
