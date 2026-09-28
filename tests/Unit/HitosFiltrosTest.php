<?php

namespace Tests\Unit;

use App\Models\Hito;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HitosFiltrosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $proyecto = Proyecto::orderBy('id')->firstOrFail();
        $hoy = today();

        Hito::create(['nombre' => 'Hito vencido', 'fecha_objetivo' => $hoy->copy()->subDays(3), 'completado' => false, 'proyecto_id' => $proyecto->id]);
        Hito::create(['nombre' => 'Hito proximo', 'fecha_objetivo' => $hoy->copy()->addDays(3), 'completado' => false, 'proyecto_id' => $proyecto->id]);
        Hito::create(['nombre' => 'Hito lejano', 'fecha_objetivo' => $hoy->copy()->addDays(40), 'completado' => false, 'proyecto_id' => $proyecto->id]);
        Hito::create(['nombre' => 'Hito cumplido atrasado', 'fecha_objetivo' => $hoy->copy()->subDays(5), 'completado' => true, 'proyecto_id' => $proyecto->id]);
    }

    public function test_vencidos_muestra_solo_los_no_completados_con_fecha_pasada(): void
    {
        $html = $this->actingAs($this->jefe())->get('/hitos?fecha_objetivo=vencidos')->getContent();

        $this->assertStringContainsString('Hito vencido', $html);
        $this->assertStringNotContainsString('Hito proximo', $html);
        $this->assertStringNotContainsString('Hito lejano', $html);
        $this->assertStringNotContainsString('Hito cumplido atrasado', $html);
    }

    public function test_proximos_7_dias_muestra_solo_lo_que_vence_en_la_semana(): void
    {
        $html = $this->actingAs($this->jefe())->get('/hitos?fecha_objetivo=proximos_7_dias')->getContent();

        $this->assertStringContainsString('Hito proximo', $html);
        $this->assertStringNotContainsString('Hito vencido', $html);
        $this->assertStringNotContainsString('Hito lejano', $html);
    }

    public function test_el_rango_personalizado_acota_por_fechas(): void
    {
        $desde = today()->subDays(3)->toDateString();
        $hasta = today()->addDays(1)->toDateString();

        // En ese rango entra "Hito vencido" (hace 3 dias, sin completar)...
        $html = $this->actingAs($this->jefe())
            ->get("/hitos?fecha_objetivo=rango&fecha_desde={$desde}&fecha_hasta={$hasta}")
            ->getContent();

        $this->assertStringContainsString('Hito vencido', $html);
        $this->assertStringNotContainsString('Hito proximo', $html);
        $this->assertStringNotContainsString('Hito lejano', $html);
    }

    private function jefe(): User
    {
        return User::where('email', 'jefe@example.com')->firstOrFail();
    }
}
