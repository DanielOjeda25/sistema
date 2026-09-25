<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SolicitudCambioNotificacionesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function al_crear_una_solicitud_avisa_al_jefe_y_al_pm_del_proyecto(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $pm = User::where('email', 'pm@example.com')->firstOrFail();
        $proyecto = Proyecto::where('pm_id', $pm->id)->firstOrFail();
        $solicitante = User::where('email', 'cliente@example.com')->firstOrFail();

        $this->actingAs($jefe)->post(route('solicitudes-cambio.store'), [
            'titulo' => 'Solicitud notificable',
            'descripcion' => 'Se necesita avisar al equipo.',
            'estado' => 'pendiente',
            'prioridad' => 'media',
            'proyecto_id' => $proyecto->id,
            'solicitado_por' => $solicitante->id,
        ])->assertRedirect();

        $this->assertDatabaseCount('notifications', 2);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $jefe->id,
            'type' => 'App\\Notifications\\SolicitudCambioCreada',
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $pm->id,
            'type' => 'App\\Notifications\\SolicitudCambioCreada',
        ]);
        $this->assertSame(1, $pm->fresh()->unreadNotifications->count());
    }

    #[Test]
    public function al_cambiar_el_estado_avisa_solo_si_el_estado_realmente_cambia(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $pm = User::where('email', 'pm@example.com')->firstOrFail();
        $solicitud = SolicitudCambio::whereHas('proyecto', fn ($query) => $query->where('pm_id', $pm->id))
            ->firstOrFail();

        $this->actingAs($jefe)->put(route('solicitudes-cambio.update', $solicitud), [
            'titulo' => $solicitud->titulo,
            'descripcion' => $solicitud->descripcion,
            'estado' => $solicitud->estado === 'pendiente' ? 'aprobada' : 'pendiente',
            'prioridad' => $solicitud->prioridad,
            'proyecto_id' => $solicitud->proyecto_id,
            'solicitado_por' => $solicitud->solicitado_por,
        ])->assertRedirect();

        $this->assertDatabaseCount('notifications', 2);
        $this->assertSame(
            'solicitud_cambio_estado_cambiado',
            $pm->fresh()->unreadNotifications->first()->data['tipo']
        );
    }
}
