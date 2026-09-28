<?php

namespace Tests\Unit;

use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntregablesTipoTest extends TestCase
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

    private function datosValidos(array $sobrescribir = []): array
    {
        return array_merge([
            'titulo' => 'Entregable de prueba',
            'contenido' => 'Contenido del entregable.',
            'tipo' => 'resumen',
            'estado' => 'borrador',
            'proyecto_id' => Proyecto::firstOrFail()->id,
            'generado_por' => $this->jefe()->id,
            'desde_modal' => 1,
        ], $sobrescribir);
    }

    public function test_acepta_solo_los_tipos_definidos(): void
    {
        $this->actingAs($this->jefe())
            ->post('/entregables', $this->datosValidos())
            ->assertRedirect();

        $this->assertDatabaseHas('entregables_ia', ['titulo' => 'Entregable de prueba', 'tipo' => 'resumen']);
    }

    public function test_rechaza_un_tipo_inventado(): void
    {
        $this->actingAs($this->jefe())
            ->post('/entregables', $this->datosValidos(['tipo' => 'pelicula']))
            ->assertSessionHasErrors('tipo');

        $this->assertDatabaseMissing('entregables_ia', ['titulo' => 'Entregable de prueba']);
    }

    public function test_al_editar_el_tipo_tambien_se_valida(): void
    {
        $jefe = $this->jefe();
        $entregable = EntregableIA::create([
            'titulo' => 'A editar',
            'contenido' => 'Contenido',
            'tipo' => 'documento',
            'estado' => 'borrador',
            'origen' => 'manual',
            'visible_cliente' => false,
            'proyecto_id' => Proyecto::firstOrFail()->id,
            'generado_por' => $jefe->id,
        ]);

        $this->actingAs($jefe)
            ->put("/entregables/{$entregable->id}", $this->datosValidos(['tipo' => 'tipo_falso']))
            ->assertSessionHasErrors('tipo');

        $this->actingAs($jefe)
            ->put("/entregables/{$entregable->id}", $this->datosValidos(['tipo' => 'informe']))
            ->assertRedirect();

        $this->assertSame('informe', $entregable->fresh()->tipo);
    }
}
