<?php

namespace Tests\Feature;

use App\Models\Factura;
use App\Models\Sprint;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verificación de las correcciones de seguridad y facturación
 * (commits 7e7007d y efcd952 de Jesús).
 */
class CorreccionesSeguridadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function datosFactura(array $extras = []): array
    {
        return array_merge([
            'numero' => 'TEST-'.uniqid(),
            'monto' => 1000,
            'fecha_emision' => '2026-09-08',
            'fecha_vencimiento' => '2026-09-30',
            'estado' => 'pendiente',
            'proyecto_id' => 1,
            'emitida_por' => 2,
        ], $extras);
    }

    /** @test */
    public function monto_maximo_de_10_millones_se_rechaza(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $this->actingAs($jefe)
            ->post(route('facturas.store'), $this->datosFactura(['monto' => 10000001]))
            ->assertSessionHasErrors('monto');

        $this->assertDatabaseMissing('facturas', ['monto' => 10000001]);
    }

    /** @test */
    public function monto_negativo_se_rechaza(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $this->actingAs($jefe)
            ->post(route('facturas.store'), $this->datosFactura(['monto' => -5]))
            ->assertSessionHasErrors('monto');
    }

    /** @test */
    public function vencimiento_anterior_a_emision_se_rechaza(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $this->actingAs($jefe)
            ->post(route('facturas.store'), $this->datosFactura(['fecha_vencimiento' => '2026-09-01']))
            ->assertSessionHasErrors('fecha_vencimiento');
    }

    /** @test */
    public function factura_valida_se_crea(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $this->actingAs($jefe)
            ->post(route('facturas.store'), $this->datosFactura(['monto' => 10000000]))
            ->assertRedirect(route('facturas.index'));

        $this->assertDatabaseHas('facturas', ['monto' => 10000000]);
    }

    /** @test */
    public function jefe_y_pm_pueden_eliminar_facturas(): void
    {
        // La eliminación sigue la misma regla que crear/editar: Jefe y PM
        // manejan el módulo comercial completo.
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $pm = User::where('email', 'pm@example.com')->firstOrFail();

        $this->actingAs($jefe)->delete(route('facturas.destroy', 1))->assertRedirect();
        $this->assertDatabaseMissing('facturas', ['id' => 1]);

        $this->actingAs($pm)->delete(route('facturas.destroy', 2))->assertRedirect();
        $this->assertDatabaseMissing('facturas', ['id' => 2]);
    }

    /** @test */
    public function po_y_programador_no_pueden_eliminar_facturas(): void
    {
        $po = User::where('email', 'po@example.com')->firstOrFail();
        $dev = User::where('email', 'dev@example.com')->firstOrFail();

        $this->actingAs($po)->delete(route('facturas.destroy', 1))->assertForbidden();
        $this->actingAs($dev)->delete(route('facturas.destroy', 1))->assertForbidden();
        $this->assertDatabaseHas('facturas', ['id' => 1]);
    }

    /** @test */
    public function mover_tareas_ajenas_al_cliente_se_rechaza(): void
    {
        $cliente = User::where('email', 'cliente@example.com')->firstOrFail();

        // La tarea 4 pertenece a un proyecto de otro cliente.
        $estadoOriginal = Tarea::findOrFail(4)->estado;

        $respuesta = $this->actingAs($cliente)
            ->patchJson(route('tareas.mover'), [
                'columnas' => [['estado' => 'completada', 'ids' => [4]]],
            ]);

        $respuesta->assertStatus(403);
        $this->assertDatabaseHas('tareas', ['id' => 4, 'estado' => $estadoOriginal]);
    }

    /** @test */
    public function cliente_solo_ve_sprints_de_sus_proyectos_en_el_tablero(): void
    {
        $cliente = User::where('email', 'cliente@example.com')->firstOrFail();

        $respuesta = $this->actingAs($cliente)->get(route('tareas.tablero'));
        $respuesta->assertOk();

        // Sprint 1 y 2 son del proyecto 1 (cliente del usuario), sprint 3 del proyecto 2 (otro cliente).
        $html = $respuesta->getContent();
        $this->assertStringNotContainsString('Sprint 3', $html);
    }
}
