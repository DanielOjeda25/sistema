<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Reglas que cruzan módulos: quién puede figurar en cada campo, que los
 * datos relacionados pertenezcan al mismo proyecto y que las bajas no
 * arrastren información sin avisar.
 */
class ValidacionesCruzadasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function usuario(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    #[Test]
    public function el_pm_de_un_proyecto_debe_ser_jefe_o_pm(): void
    {
        $datos = [
            'nombre' => 'Proyecto con PM invalido', 'fecha_inicio' => now()->toDateString(),
            'estado' => 'pendiente', 'cliente_id' => 1,
        ];

        $this->actingAs($this->usuario('jefe@example.com'))
            ->post(route('proyectos.store'), $datos + ['pm_id' => $this->usuario('cliente@example.com')->id])
            ->assertSessionHasErrors('pm_id');

        $this->actingAs($this->usuario('jefe@example.com'))
            ->post(route('proyectos.store'), $datos + ['pm_id' => $this->usuario('pm@example.com')->id])
            ->assertSessionDoesntHaveErrors();
    }

    #[Test]
    public function una_factura_no_puede_ser_emitida_por_un_cliente_ni_ser_de_monto_cero(): void
    {
        $base = [
            'numero' => 'F-TEST-1', 'monto' => 100, 'fecha_emision' => now()->toDateString(),
            'estado' => 'pendiente', 'proyecto_id' => 1,
        ];
        $jefe = $this->usuario('jefe@example.com');

        $this->actingAs($jefe)->post(route('facturas.store'), $base + ['emitida_por' => $this->usuario('cliente@example.com')->id])
            ->assertSessionHasErrors('emitida_por');

        $this->actingAs($jefe)->post(route('facturas.store'), array_merge($base, ['monto' => 0, 'emitida_por' => $jefe->id]))
            ->assertSessionHasErrors('monto');
    }

    #[Test]
    public function una_tarea_solo_acepta_sprints_de_su_proyecto(): void
    {
        $jefe = $this->usuario('jefe@example.com');
        $sprintAjeno = Sprint::create(['nombre' => 'Sprint ajeno', 'proyecto_id' => 2]);
        $base = [
            'titulo' => 'Tarea de prueba', 'estado' => 'pendiente', 'prioridad' => 'media',
            'proyecto_id' => 1, 'asignado_a' => $this->usuario('dev@example.com')->id,
        ];

        $this->actingAs($jefe)->post(route('tareas.store'), $base + ['sprint_id' => $sprintAjeno->id])
            ->assertSessionHasErrors('sprint_id');

        $sprintPropio = Sprint::create(['nombre' => 'Sprint propio', 'proyecto_id' => 1]);
        $this->actingAs($jefe)->post(route('tareas.store'), $base + ['sprint_id' => $sprintPropio->id])
            ->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('tareas', ['titulo' => 'Tarea de prueba', 'sprint_id' => $sprintPropio->id]);
    }

    #[Test]
    public function un_cliente_con_proyectos_no_se_puede_eliminar(): void
    {
        $cliente = Cliente::has('proyectos')->firstOrFail();

        $this->actingAs($this->usuario('jefe@example.com'))
            ->delete(route('clientes.destroy', $cliente))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('clientes', ['id' => $cliente->id]);
    }

    #[Test]
    public function borrar_la_cuenta_de_un_cliente_no_borra_su_empresa_ni_sus_proyectos(): void
    {
        $cuenta = $this->usuario('cliente@example.com');
        $empresaId = $cuenta->cliente_id;
        $proyectos = Proyecto::where('cliente_id', $empresaId)->count();
        $this->assertGreaterThan(0, $proyectos);

        $this->actingAs($this->usuario('jefe@example.com'))->delete(route('users.destroy', $cuenta));

        $this->assertDatabaseHas('clientes', ['id' => $empresaId]);
        $this->assertSame($proyectos, Proyecto::where('cliente_id', $empresaId)->count());
    }

    #[Test]
    public function un_usuario_inactivo_no_puede_iniciar_sesion(): void
    {
        $dev = $this->usuario('dev@example.com');
        $dev->update(['estado' => 'inactivo']);

        $this->post('/login', ['email' => $dev->email, 'password' => '1234'])
            ->assertSessionHasErrors();
        $this->assertGuest();
    }

    #[Test]
    public function las_facturas_pendientes_vencidas_pasan_a_vencida(): void
    {
        $factura = Factura::create([
            'numero' => 'F-VENC-1', 'monto' => 50, 'fecha_emision' => now()->subMonths(2)->toDateString(),
            'fecha_vencimiento' => now()->subDay()->toDateString(), 'estado' => 'pendiente',
            'proyecto_id' => 1, 'emitida_por' => $this->usuario('jefe@example.com')->id,
        ]);
        $vigente = Factura::create([
            'numero' => 'F-VIG-1', 'monto' => 50, 'fecha_emision' => now()->toDateString(),
            'fecha_vencimiento' => now()->addDays(5)->toDateString(), 'estado' => 'pendiente',
            'proyecto_id' => 1, 'emitida_por' => $this->usuario('jefe@example.com')->id,
        ]);

        Factura::marcarVencidas();

        $this->assertSame('vencida', $factura->fresh()->estado);
        $this->assertSame('pendiente', $vigente->fresh()->estado);
    }

    #[Test]
    public function las_fechas_absurdas_y_los_textos_enormes_se_rechazan_con_error_de_validacion(): void
    {
        $jefe = $this->usuario('jefe@example.com');

        $this->actingAs($jefe)->post(route('hitos.store'), [
            'nombre' => 'Hito raro', 'fecha_objetivo' => '9999-12-31', 'completado' => 0, 'proyecto_id' => 1,
        ])->assertSessionHasErrors('fecha_objetivo');

        $this->actingAs($jefe)->post(route('hitos.store'), [
            'nombre' => 'Hito largo', 'fecha_objetivo' => now()->toDateString(), 'completado' => 0,
            'proyecto_id' => 1, 'descripcion' => str_repeat('a', 5001),
        ])->assertSessionHasErrors('descripcion');
    }

    #[Test]
    public function si_inactivan_a_alguien_con_la_sesion_abierta_se_le_cierra_la_sesion(): void
    {
        $dev = $this->usuario('dev@example.com');
        $this->actingAs($dev)->get('/dashboard')->assertOk();

        $dev->update(['estado' => 'inactivo']);

        $this->actingAs($dev->fresh())->get('/dashboard')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
