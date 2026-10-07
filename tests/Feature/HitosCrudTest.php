<?php

namespace Tests\Feature;

use App\Models\Hito;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Ciclo de vida del módulo Hitos (crear -> editar -> eliminar) y el filtro
 * del listado. Un test por tarea; cada uno arma lo que necesita porque la
 * base se reinicia entre tests (RefreshDatabase).
 */
class HitosCrudTest extends TestCase
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

    private function hitoDePrueba(string $nombre, array $extra = []): Hito
    {
        return Hito::create(array_merge([
            'nombre' => $nombre, 'descripcion' => 'prueba',
            'fecha_objetivo' => '2026-11-15', 'completado' => false,
            'proyecto_id' => Proyecto::firstOrFail()->id,
        ], $extra));
    }

    #[Test]
    public function un_jefe_puede_crear_un_hito(): void
    {
        $proyecto = Proyecto::firstOrFail();

        $this->actingAs($this->jefe())->post(route('hitos.store'), [
            'nombre' => 'Hito nuevo', 'descripcion' => 'prueba',
            'fecha_objetivo' => '2026-11-15', 'completado' => '0', 'proyecto_id' => $proyecto->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('hitos', ['nombre' => 'Hito nuevo', 'proyecto_id' => $proyecto->id]);
    }

    #[Test]
    public function un_jefe_puede_editar_un_hito(): void
    {
        $hito = $this->hitoDePrueba('Hito a editar');

        $this->actingAs($this->jefe())->put(route('hitos.update', $hito), [
            'nombre' => 'Hito editado', 'descripcion' => 'prueba',
            'fecha_objetivo' => '2026-11-20', 'completado' => '1', 'proyecto_id' => $hito->proyecto_id,
        ])->assertRedirect();

        $this->assertDatabaseHas('hitos', ['id' => $hito->id, 'nombre' => 'Hito editado', 'completado' => true]);
    }

    #[Test]
    public function un_jefe_puede_eliminar_un_hito(): void
    {
        $hito = $this->hitoDePrueba('Hito a eliminar');

        $this->actingAs($this->jefe())->delete(route('hitos.destroy', $hito))->assertRedirect();

        $this->assertDatabaseMissing('hitos', ['id' => $hito->id]);
    }

    #[Test]
    public function el_listado_de_hitos_puede_filtrarse_por_proyecto_estado_y_fecha_objetivo(): void
    {
        $jefe = $this->jefe();
        $proyectoA = Proyecto::firstOrFail();
        $proyectoB = Proyecto::query()->whereKeyNot($proyectoA->id)->firstOrFail();

        $hitoVencido = $this->hitoDePrueba('Hito vencido filtrado', [
            'descripcion' => 'debe verse',
            'fecha_objetivo' => today()->subDay()->toDateString(),
            'proyecto_id' => $proyectoA->id,
        ]);
        $hitoCercano = $this->hitoDePrueba('Hito cercano filtrado', [
            'descripcion' => 'no debe verse',
            'fecha_objetivo' => today()->addDays(3)->toDateString(),
            'proyecto_id' => $proyectoA->id,
        ]);
        $hitoOtroProyecto = $this->hitoDePrueba('Hito de otro proyecto', [
            'descripcion' => 'no debe verse',
            'fecha_objetivo' => today()->subDay()->toDateString(),
            'proyecto_id' => $proyectoB->id,
        ]);

        $this->actingAs($jefe)
            ->get(route('hitos.index', [
                'proyecto_id' => $proyectoA->id,
                'estado' => 'pendiente',
                'fecha_objetivo' => 'vencidos',
            ]))
            ->assertOk()
            ->assertSee($hitoVencido->nombre)
            ->assertDontSee($hitoCercano->nombre)
            ->assertDontSee($hitoOtroProyecto->nombre);
    }
}
