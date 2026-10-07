<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Ciclo de vida del módulo Sprints: crear -> editar -> eliminar.
 * Un test por tarea; cada uno arma lo que necesita porque la base se
 * reinicia entre tests (RefreshDatabase).
 */
class SprintsCrudTest extends TestCase
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
    public function un_jefe_puede_crear_un_sprint(): void
    {
        $proyecto = Proyecto::firstOrFail();

        $this->actingAs($this->jefe())->post(route('sprints.store'), [
            'nombre' => 'Sprint nuevo', 'proyecto_id' => $proyecto->id,
            'fecha_inicio' => '2026-10-05', 'fecha_fin' => '2026-10-16',
        ])->assertRedirect();

        $this->assertDatabaseHas('sprints', ['nombre' => 'Sprint nuevo', 'proyecto_id' => $proyecto->id]);
    }

    #[Test]
    public function un_jefe_puede_editar_un_sprint(): void
    {
        $sprint = Sprint::create([
            'nombre' => 'Sprint a editar', 'proyecto_id' => Proyecto::firstOrFail()->id,
            'fecha_inicio' => '2026-10-05', 'fecha_fin' => '2026-10-16',
        ]);

        $this->actingAs($this->jefe())->put(route('sprints.update', $sprint), [
            'nombre' => 'Sprint editado', 'proyecto_id' => $sprint->proyecto_id,
            'fecha_inicio' => '2026-10-05', 'fecha_fin' => '2026-10-23',
        ])->assertRedirect();

        // Por el modelo y no por fila cruda: la BD guarda la fecha con hora
        // y el formato cambia entre MySQL y sqlite.
        $sprint->refresh();
        $this->assertSame('Sprint editado', $sprint->nombre);
        $this->assertSame('2026-10-23', $sprint->fecha_fin->toDateString());
    }

    #[Test]
    public function un_jefe_puede_eliminar_un_sprint(): void
    {
        $sprint = Sprint::create([
            'nombre' => 'Sprint a eliminar', 'proyecto_id' => Proyecto::firstOrFail()->id,
            'fecha_inicio' => '2026-10-05', 'fecha_fin' => '2026-10-16',
        ]);

        $this->actingAs($this->jefe())->delete(route('sprints.destroy', $sprint))->assertRedirect();

        $this->assertDatabaseMissing('sprints', ['id' => $sprint->id]);
    }
}
