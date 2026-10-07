<?php

namespace Tests\Feature;

use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Ciclo de vida del módulo Entregables IA (los cargados a mano): crear ->
 * editar -> eliminar. Un test por tarea; cada uno arma lo que necesita
 * porque la base se reinicia entre tests (RefreshDatabase).
 */
class EntregablesCrudTest extends TestCase
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
    public function un_jefe_puede_crear_un_entregable(): void
    {
        $jefe = $this->jefe();
        $proyecto = Proyecto::firstOrFail();

        $this->actingAs($jefe)->post(route('entregables.store'), [
            'titulo' => 'Entregable nuevo', 'contenido' => 'contenido',
            'tipo' => 'informe', 'estado' => 'borrador',
            'proyecto_id' => $proyecto->id, 'generado_por' => $jefe->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('entregables_ia', ['titulo' => 'Entregable nuevo', 'estado' => 'borrador']);
    }

    #[Test]
    public function un_jefe_puede_editar_un_entregable(): void
    {
        $jefe = $this->jefe();
        $entregable = EntregableIA::create([
            'titulo' => 'Entregable a editar', 'contenido' => 'contenido',
            'tipo' => 'informe', 'estado' => 'borrador',
            'proyecto_id' => Proyecto::firstOrFail()->id, 'generado_por' => $jefe->id,
        ]);

        $this->actingAs($jefe)->put(route('entregables.update', $entregable), [
            'titulo' => 'Entregable editado', 'contenido' => 'contenido v2',
            'tipo' => 'informe', 'estado' => 'aprobado',
            'proyecto_id' => $entregable->proyecto_id, 'generado_por' => $jefe->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('entregables_ia', ['id' => $entregable->id, 'estado' => 'aprobado']);
    }

    #[Test]
    public function un_jefe_puede_eliminar_un_entregable(): void
    {
        $jefe = $this->jefe();
        $entregable = EntregableIA::create([
            'titulo' => 'Entregable a eliminar', 'contenido' => 'contenido',
            'tipo' => 'informe', 'estado' => 'borrador',
            'proyecto_id' => Proyecto::firstOrFail()->id, 'generado_por' => $jefe->id,
        ]);

        $this->actingAs($jefe)->delete(route('entregables.destroy', $entregable))->assertRedirect();

        $this->assertDatabaseMissing('entregables_ia', ['id' => $entregable->id]);
    }
}
