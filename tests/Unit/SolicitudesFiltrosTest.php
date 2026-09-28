<?php

namespace Tests\Unit;

use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitudesFiltrosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function crear(string $titulo, array $extra = []): SolicitudCambio
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $pm = User::where('email', 'pm@example.com')->firstOrFail();
        $proyectoA = Proyecto::orderBy('id')->firstOrFail();
        $proyectoB = Proyecto::orderBy('id', 'desc')->firstOrFail();

        return SolicitudCambio::create(array_merge([
            'titulo' => $titulo,
            'descripcion' => 'Descripcion de '.$titulo,
            'estado' => 'pendiente',
            'prioridad' => 'media',
            'proyecto_id' => $proyectoA->id,
            'solicitado_por' => $jefe->id,
        ], $extra === [] ? [] : array_combine(
            array_keys($extra),
            array_map(fn ($v) => match ($v) {
                '%PM%' => $pm->id,
                '%B%' => $proyectoB->id,
                default => $v,
            }, $extra)
        )));
    }

    public function test_filtra_por_prioridad(): void
    {
        $this->crear('Solicitud alta', ['prioridad' => 'alta']);
        $this->crear('Solicitud baja', ['prioridad' => 'baja']);

        $html = $this->actingAs($this->jefeOAlternativo())
            ->get('/solicitudes-cambio?prioridad=alta')
            ->getContent();

        $this->assertStringContainsString('Solicitud alta', $html);
        $this->assertStringNotContainsString('Solicitud baja', $html);
    }

    public function test_filtra_por_proyecto(): void
    {
        $this->crear('Solicitud del proyecto A');
        $this->crear('Solicitud del proyecto B', ['proyecto_id' => '%B%']);

        $proyectoB = Proyecto::orderBy('id', 'desc')->firstOrFail();
        $html = $this->actingAs($this->jefeOAlternativo())
            ->get("/solicitudes-cambio?proyecto_id={$proyectoB->id}")
            ->getContent();

        $this->assertStringContainsString('Solicitud del proyecto B', $html);
        $this->assertStringNotContainsString('Solicitud del proyecto A', $html);
    }

    public function test_filtra_por_solicitante(): void
    {
        $pm = User::where('email', 'pm@example.com')->firstOrFail();
        $this->crear('Pedida por el jefe');
        $this->crear('Pedida por el PM', ['solicitado_por' => '%PM%']);

        $html = $this->actingAs($this->jefeOAlternativo())
            ->get("/solicitudes-cambio?solicitado_por={$pm->id}")
            ->getContent();

        $this->assertStringContainsString('Pedida por el PM', $html);
        $this->assertStringNotContainsString('Pedida por el jefe', $html);
    }

    public function test_combina_estado_y_prioridad(): void
    {
        $this->crear('Alta y aprobada', ['prioridad' => 'alta', 'estado' => 'aprobada']);
        $this->crear('Alta y pendiente', ['prioridad' => 'alta']);

        $html = $this->actingAs($this->jefeOAlternativo())
            ->get('/solicitudes-cambio?estado=aprobada&prioridad=alta')
            ->getContent();

        $this->assertStringContainsString('Alta y aprobada', $html);
        $this->assertStringNotContainsString('Alta y pendiente', $html);
    }

    private function jefeOAlternativo(): User
    {
        return User::where('email', 'jefe@example.com')->firstOrFail();
    }
}
