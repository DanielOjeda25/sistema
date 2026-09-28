<?php

namespace Tests\Unit;

use App\Models\ActualizacionProyecto;
use App\Models\Proyecto;
use App\Models\User;
use App\Services\AI\ProjectContextBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectContextBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function proyectoFresco(): Proyecto
    {
        $cliente = User::where('email', 'cliente@example.com')->firstOrFail();
        $pm = User::where('email', 'pm@example.com')->firstOrFail();

        Proyecto::create([
            'nombre' => 'Proyecto de contexto',
            'descripcion' => 'Para testear el armado del contexto de IA.',
            'fecha_inicio' => today()->subDays(30),
            'estado' => 'en_progreso',
            'cliente_id' => $cliente->cliente_id,
            'pm_id' => $pm->id,
        ]);

        return Proyecto::where('nombre', 'Proyecto de contexto')->firstOrFail();
    }

    private function asignable(): int
    {
        return User::where('email', 'dev@example.com')->firstOrFail()->id;
    }

    public function test_calcula_el_progreso_ponderando_tareas_e_hitos_por_igual(): void
    {
        $proyecto = $this->proyectoFresco();
        $asignado = $this->asignable();

        $proyecto->tareas()->createMany([
            ['titulo' => 'Tarea lista', 'estado' => 'completada', 'prioridad' => 'media', 'asignado_a' => $asignado],
            ['titulo' => 'Tarea en curso', 'estado' => 'en_progreso', 'prioridad' => 'media', 'asignado_a' => $asignado],
            ['titulo' => 'Tarea pendiente', 'estado' => 'pendiente', 'prioridad' => 'baja', 'asignado_a' => $asignado],
        ]);
        $proyecto->hitos()->createMany([
            ['nombre' => 'Hito cumplido', 'fecha_objetivo' => today()->addDays(5), 'completado' => true],
            ['nombre' => 'Hito pendiente', 'fecha_objetivo' => today()->addDays(15), 'completado' => false],
        ]);

        $contexto = (new ProjectContextBuilder)->build($proyecto);

        // 2 completados de 5 elementos totales (3 tareas + 2 hitos) -> 40%
        $this->assertSame(40, $contexto['progreso']['porcentaje']);
        $this->assertSame(3, $contexto['progreso']['tareas_total']);
        $this->assertSame(2, $contexto['progreso']['hitos_total']);
    }

    public function test_cuenta_como_vencidas_solo_las_no_terminadas_con_fecha_pasada(): void
    {
        $proyecto = $this->proyectoFresco();
        $asignado = $this->asignable();

        $proyecto->tareas()->createMany([
            ['titulo' => 'Vencida pendiente', 'estado' => 'pendiente', 'prioridad' => 'alta', 'fecha_limite' => today()->subDay(), 'asignado_a' => $asignado],
            ['titulo' => 'Vencida en progreso', 'estado' => 'en_progreso', 'prioridad' => 'alta', 'fecha_limite' => today()->subDays(2), 'asignado_a' => $asignado],
            ['titulo' => 'Completada tarde', 'estado' => 'completada', 'prioridad' => 'baja', 'fecha_limite' => today()->subDay(), 'asignado_a' => $asignado],
            ['titulo' => 'Cancelada tarde', 'estado' => 'cancelada', 'prioridad' => 'baja', 'fecha_limite' => today()->subDay(), 'asignado_a' => $asignado],
            ['titulo' => 'Al dia', 'estado' => 'pendiente', 'prioridad' => 'baja', 'fecha_limite' => today()->addWeek(), 'asignado_a' => $asignado],
        ]);

        $contexto = (new ProjectContextBuilder)->build($proyecto);

        $this->assertSame(2, $contexto['progreso']['tareas_vencidas']);
    }

    public function test_solo_incluye_novedades_visibles_para_el_cliente(): void
    {
        $proyecto = $this->proyectoFresco();
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        ActualizacionProyecto::create([
            'proyecto_id' => $proyecto->id,
            'creado_por' => $jefe->id,
            'titulo' => 'Novedad publicable',
            'descripcion' => 'Ok para el cliente.',
            'tipo' => 'avance',
            'fecha' => today(),
            'visible_cliente' => true,
        ]);
        ActualizacionProyecto::create([
            'proyecto_id' => $proyecto->id,
            'creado_por' => $jefe->id,
            'titulo' => 'Novedad secreta',
            'descripcion' => 'Interna.',
            'tipo' => 'problema',
            'fecha' => today(),
            'visible_cliente' => false,
        ]);

        $contexto = (new ProjectContextBuilder)->build($proyecto);

        $titulos = collect($contexto['actualizaciones'])->pluck('titulo');
        $this->assertTrue($titulos->contains('Novedad publicable'));
        $this->assertFalse($titulos->contains('Novedad secreta'));
    }

    public function test_presenta_al_cliente_por_empresa_cuando_tiene(): void
    {
        $proyecto = $this->proyectoFresco();
        $proyecto->load('cliente');

        $contexto = (new ProjectContextBuilder)->build($proyecto);

        $esperado = $proyecto->cliente->empresa ?: trim($proyecto->cliente->nombre.' '.$proyecto->cliente->apellido);
        $this->assertSame($esperado, $contexto['proyecto']['cliente']);
    }
}
