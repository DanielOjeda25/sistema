<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_jefe_ve_el_indice_y_el_reporte_de_proyectos(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $this->actingAs($jefe)->get('/reportes')
            ->assertOk()
            ->assertSee('Estado y avance de proyectos');

        $this->actingAs($jefe)->get('/reportes/proyectos')
            ->assertOk()
            ->assertSee('Proyectos por estado')
            ->assertSee('Detalle por proyecto');
    }

    public function test_pm_y_po_acceden_al_reporte(): void
    {
        foreach (['pm@example.com', 'po@example.com'] as $email) {
            $this->actingAs(User::where('email', $email)->firstOrFail())
                ->get('/reportes/proyectos')
                ->assertOk();
        }
    }

    public function test_programador_no_ve_reportes_en_el_menu(): void
    {
        $dev = User::where('email', 'dev@example.com')->firstOrFail();

        $html = $this->actingAs($dev)->get('/dashboard')->getContent();

        $this->assertStringNotContainsString('Reportes', $html);
        $this->actingAs($dev)->get('/reportes')->assertForbidden();
    }

    public function test_cliente_queda_bloqueado(): void
    {
        $cliente = User::where('email', 'cliente@example.com')->firstOrFail();

        $this->actingAs($cliente)->get('/reportes')->assertForbidden();
        $this->actingAs($cliente)->get('/reportes/proyectos')->assertForbidden();
        $this->actingAs($cliente)->get('/reportes/proyectos/exportar?formato=csv')->assertForbidden();
    }

    public function test_filtra_por_estado(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $html = $this->actingAs($jefe)->get('/reportes/proyectos?estado=en_progreso')->getContent();

        $this->assertStringContainsString('Gestor de expedientes Gimenez', $html);
        $this->assertStringContainsString('Sistema de obra L', $html);
        $this->assertStringNotContainsString('Portal del socio', $html);
    }

    public function test_facturacion_solo_jefe_y_pm(): void
    {
        $this->actingAs(User::where('email', 'jefe@example.com')->firstOrFail())
            ->get('/reportes/facturacion')
            ->assertOk()
            ->assertSee('Facturación y cobranzas');

        $this->actingAs(User::where('email', 'pm@example.com')->firstOrFail())
            ->get('/reportes/facturacion')->assertOk();

        // PO y Programador no entran: es un reporte de plata.
        foreach (['po@example.com', 'dev@example.com'] as $email) {
            $this->actingAs(User::where('email', $email)->firstOrFail())
                ->get('/reportes/facturacion')->assertForbidden();
        }
    }

    public function test_cliente_descarga_el_reporte_de_su_proyecto(): void
    {
        $cliente = User::where('email', 'cliente@example.com')->firstOrFail();

        $this->actingAs($cliente)
            ->get('/reportes/proyectos/1/pdf')
            ->assertOk();

        // Proyecto de otra empresa: prohibido.
        $this->actingAs($cliente)
            ->get('/reportes/proyectos/2/pdf')
            ->assertForbidden();
    }

    public function test_exporta_csv(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $respuesta = $this->actingAs($jefe)->get('/reportes/proyectos/exportar?formato=csv');

        $respuesta->assertOk();
        $this->assertStringContainsString('text/csv', $respuesta->headers->get('Content-Type'));
        $this->assertStringContainsString('TOTAL', $respuesta->streamedContent());
    }
}
