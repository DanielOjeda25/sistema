<?php

namespace Tests\Feature;

use App\Models\ActualizacionProyecto;
use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectAIReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_internal_user_can_record_safe_context_and_generate_a_draft(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $proyecto = Proyecto::firstOrFail();

        ActualizacionProyecto::create([
            'proyecto_id' => $proyecto->id,
            'creado_por' => $jefe->id,
            'titulo' => 'Avance publicable único',
            'descripcion' => 'Información apta para el cliente.',
            'tipo' => 'avance',
            'fecha' => today(),
            'visible_cliente' => true,
        ]);

        ActualizacionProyecto::create([
            'proyecto_id' => $proyecto->id,
            'creado_por' => $jefe->id,
            'titulo' => 'Nota interna secreta',
            'descripcion' => 'No debe salir del equipo.',
            'tipo' => 'problema',
            'fecha' => today(),
            'visible_cliente' => false,
        ]);

        $this->actingAs($jefe)
            ->post(route('proyectos.informes-ia.store', $proyecto))
            ->assertRedirect(route('proyectos.show', $proyecto));

        $informe = EntregableIA::where('proyecto_id', $proyecto->id)
            ->where('origen', 'ia')
            ->latest('id')
            ->firstOrFail();

        $this->assertSame('borrador', $informe->estado);
        $this->assertFalse($informe->visible_cliente);
        $this->assertSame('fake-local', $informe->modelo_ia);
        $titulosIncluidos = collect($informe->contexto_fuente['actualizaciones'])->pluck('titulo');
        $this->assertTrue($titulosIncluidos->contains('Avance publicable único'));
        $this->assertFalse($titulosIncluidos->contains('Nota interna secreta'));
    }

    public function test_client_cannot_generate_reports_or_record_updates(): void
    {
        $cliente = User::where('email', 'cliente@example.com')->firstOrFail();
        $proyecto = Proyecto::where('cliente_id', $cliente->cliente_id)->firstOrFail();

        $this->actingAs($cliente)
            ->post(route('proyectos.informes-ia.store', $proyecto))
            ->assertForbidden();

        $this->actingAs($cliente)
            ->post(route('proyectos.actualizaciones.store', $proyecto), [
                'titulo' => 'Intento no autorizado',
                'descripcion' => 'No debe guardarse.',
                'tipo' => 'avance',
                'fecha' => today()->toDateString(),
                'visible_cliente' => true,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('actualizaciones_proyecto', [
            'titulo' => 'Intento no autorizado',
        ]);
    }

    public function test_client_only_sees_ai_report_after_it_is_approved_and_published(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $cliente = User::where('email', 'cliente@example.com')->firstOrFail();
        $proyecto = Proyecto::where('cliente_id', $cliente->cliente_id)->firstOrFail();

        $this->actingAs($jefe)->post(route('proyectos.informes-ia.store', $proyecto));
        $informe = EntregableIA::where('proyecto_id', $proyecto->id)
            ->where('origen', 'ia')
            ->latest('id')
            ->firstOrFail();

        $this->actingAs($cliente)
            ->get(route('proyectos.show', $proyecto))
            ->assertOk()
            ->assertDontSee($informe->titulo);

        $this->actingAs($jefe)
            ->patch(route('informes-ia.publish', $informe))
            ->assertRedirect(route('proyectos.show', $proyecto));

        $this->actingAs($cliente)
            ->get(route('proyectos.show', $proyecto))
            ->assertOk()
            ->assertSee($informe->titulo)
            ->assertDontSee('Generar borrador');
    }
}
