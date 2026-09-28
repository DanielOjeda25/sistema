<?php

namespace Tests\Unit;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FacturasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function datosValidos(array $sobrescribir = []): array
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $proyecto = Proyecto::firstOrFail();

        return array_merge([
            'numero' => 'T-'.Str::random(6),
            'monto' => 1500.5,
            'fecha_emision' => today()->toDateString(),
            'fecha_vencimiento' => today()->addWeek()->toDateString(),
            'estado' => 'pendiente',
            'detalle' => null,
            'proyecto_id' => $proyecto->id,
            'emitida_por' => $jefe->id,
            'desde_modal' => 1,
        ], $sobrescribir);
    }

    public function test_jefe_crea_una_factura_valida(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $datos = $this->datosValidos();

        $this->actingAs($jefe)
            ->post('/facturas', $datos)
            ->assertRedirect();

        $this->assertDatabaseHas('facturas', ['numero' => $datos['numero'], 'monto' => 1500.5]);
    }

    public function test_el_numero_de_factura_debe_ser_unico(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $datos = $this->datosValidos();

        $this->actingAs($jefe)->post('/facturas', $datos)->assertRedirect();

        $this->actingAs($jefe)
            ->post('/facturas', $this->datosValidos(['numero' => $datos['numero']]))
            ->assertSessionHasErrors('numero');
    }

    public function test_el_vencimiento_no_puede_ser_anterior_a_la_emision(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $this->actingAs($jefe)
            ->post('/facturas', $this->datosValidos([
                'fecha_vencimiento' => today()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors('fecha_vencimiento');
    }

    public function test_el_monto_no_acepta_negativos_ni_valores_fuera_de_tope(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $this->actingAs($jefe)
            ->post('/facturas', $this->datosValidos(['monto' => -1]))
            ->assertSessionHasErrors('monto');

        $this->actingAs($jefe)
            ->post('/facturas', $this->datosValidos(['monto' => 10000001]))
            ->assertSessionHasErrors('monto');
    }

    public function test_el_cliente_no_puede_crear_facturas(): void
    {
        $cliente = User::where('email', 'cliente@example.com')->firstOrFail();

        $this->actingAs($cliente)
            ->post('/facturas', $this->datosValidos())
            ->assertForbidden();
    }
}
