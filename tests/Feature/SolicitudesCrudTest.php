<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Ciclo de vida del módulo Solicitudes de cambio: crear -> editar ->
 * eliminar. Un test por tarea; cada uno arma lo que necesita porque la
 * base se reinicia entre tests (RefreshDatabase).
 */
class SolicitudesCrudTest extends TestCase
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
    public function un_jefe_puede_crear_una_solicitud_de_cambio(): void
    {
        $jefe = $this->jefe();
        $proyecto = Proyecto::firstOrFail();

        $this->actingAs($jefe)->post(route('solicitudes-cambio.store'), [
            'titulo' => 'Solicitud nueva', 'descripcion' => 'prueba',
            'estado' => 'pendiente', 'prioridad' => 'media',
            'proyecto_id' => $proyecto->id, 'solicitado_por' => $jefe->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('solicitudes_cambio', ['titulo' => 'Solicitud nueva', 'estado' => 'pendiente']);
    }

    #[Test]
    public function un_jefe_puede_editar_una_solicitud_de_cambio(): void
    {
        $jefe = $this->jefe();
        $solicitud = SolicitudCambio::create([
            'titulo' => 'Solicitud a editar', 'descripcion' => 'prueba',
            'estado' => 'pendiente', 'prioridad' => 'media',
            'proyecto_id' => Proyecto::firstOrFail()->id, 'solicitado_por' => $jefe->id,
        ]);

        $this->actingAs($jefe)->put(route('solicitudes-cambio.update', $solicitud), [
            'titulo' => 'Solicitud a editar', 'descripcion' => 'prueba',
            'estado' => 'aprobada', 'prioridad' => 'alta',
            'proyecto_id' => $solicitud->proyecto_id, 'solicitado_por' => $jefe->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('solicitudes_cambio', ['id' => $solicitud->id, 'estado' => 'aprobada']);
    }

    #[Test]
    public function un_jefe_puede_eliminar_una_solicitud_de_cambio(): void
    {
        $jefe = $this->jefe();
        $solicitud = SolicitudCambio::create([
            'titulo' => 'Solicitud a eliminar', 'descripcion' => 'prueba',
            'estado' => 'pendiente', 'prioridad' => 'media',
            'proyecto_id' => Proyecto::firstOrFail()->id, 'solicitado_por' => $jefe->id,
        ]);

        $this->actingAs($jefe)->delete(route('solicitudes-cambio.destroy', $solicitud))->assertRedirect();

        $this->assertDatabaseMissing('solicitudes_cambio', ['id' => $solicitud->id]);
    }
}
