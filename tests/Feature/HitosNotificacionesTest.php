<?php

namespace Tests\Feature;

use App\Models\Hito;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HitosNotificacionesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        // Aislar el test: los hitos sembrados ya estan vencidos y dispararian
        // avisos propios; se completan para que solo cuente el hito del test.
        Hito::where('completado', false)->update(['completado' => true]);
    }

    private function hitoProximo(array $extra = []): Hito
    {
        $pm = User::where('email', 'pm@example.com')->firstOrFail();

        return Hito::create(array_merge([
            'nombre' => 'Hito de prueba '.uniqid(),
            'descripcion' => 'prueba',
            'fecha_objetivo' => today()->addDays(3),
            'completado' => false,
            'proyecto_id' => Proyecto::where('pm_id', $pm->id)->firstOrFail()->id,
        ], $extra));
    }

        #[Test]
    public function el_comando_notifica_al_pm_del_proyecto(): void
    {
        $pm = User::where('email', 'pm@example.com')->firstOrFail();
        $this->hitoProximo();

        Artisan::call('avisos:hitos-por-vencer');

        $this->assertDatabaseCount('notifications', 1);
        $this->assertEquals(1, $pm->fresh()->unreadNotifications->count());
        $this->assertSame('hito_por_vencer', $pm->unreadNotifications->first()->data['tipo']);
    }

        #[Test]
    public function no_duplica_avisos_del_mismo_hito(): void
    {
        $this->hitoProximo();

        Artisan::call('avisos:hitos-por-vencer');
        Artisan::call('avisos:hitos-por-vencer');

        $this->assertDatabaseCount('notifications', 1);
    }

        #[Test]
    public function no_notifica_hitos_completados(): void
    {
        $this->hitoProximo(['completado' => true]);

        Artisan::call('avisos:hitos-por-vencer');

        $this->assertDatabaseCount('notifications', 0);
    }

        #[Test]
    public function la_campanita_aparece_en_el_layout_y_se_puede_marcar_leida(): void
    {
        $pm = User::where('email', 'pm@example.com')->firstOrFail();
        $this->hitoProximo();

        Artisan::call('avisos:hitos-por-vencer');
        $notificacion = $pm->unreadNotifications->first();

        // La campanita con el contador aparece en las páginas con layout.
        $this->actingAs($pm)
            ->get(route('proyectos.index'))
            ->assertOk()
            ->assertSee('aria-label="Notificaciones"', false)
            ->assertSee($notificacion->data['titulo'], false);

        // Marcar como leída la quita del contador.
        $this->actingAs($pm)
            ->post(route('notificaciones.leer', $notificacion->id))
            ->assertRedirect();

        $this->assertSame(0, $pm->fresh()->unreadNotifications->count());

        // "Marcar todas" también responde.
        $this->actingAs($pm)
            ->post(route('notificaciones.leer-todas'))
            ->assertRedirect();
    }

        #[Test]
    public function otro_usuario_no_ve_ni_puede_marcar_las_notificaciones_ajenas(): void
    {
        $pm = User::where('email', 'pm@example.com')->firstOrFail();
        $this->hitoProximo();
        Artisan::call('avisos:hitos-por-vencer');
        $notificacion = $pm->unreadNotifications->first();

        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $this->actingAs($jefe)
            ->get(route('proyectos.index'))
            ->assertOk()
            ->assertDontSee($notificacion->data['titulo'], false);

        $this->actingAs($jefe)
            ->post(route('notificaciones.leer', $notificacion->id))
            ->assertNotFound();

        $this->assertEquals(1, $pm->fresh()->unreadNotifications->count());
    }
}
