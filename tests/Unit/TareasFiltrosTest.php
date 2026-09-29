<?php

namespace Tests\Unit;

use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TareasFiltrosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function crearTarea(string $titulo, array $extra = []): Tarea
    {
        return Tarea::create(array_merge([
            'titulo' => $titulo,
            'estado' => 'pendiente',
            'prioridad' => 'media',
            'orden' => 0,
            'proyecto_id' => Proyecto::orderBy('id')->firstOrFail()->id,
            'asignado_a' => User::where('email', 'dev@example.com')->firstOrFail()->id,
        ], $extra));
    }

    public function test_filtra_por_proyecto(): void
    {
        $proyectoA = Proyecto::orderBy('id')->firstOrFail();
        $proyectoB = Proyecto::orderBy('id', 'desc')->firstOrFail();

        $this->crearTarea('Tarea del proyecto A', ['proyecto_id' => $proyectoA->id]);
        $this->crearTarea('Tarea del proyecto B', ['proyecto_id' => $proyectoB->id]);

        $html = $this->actingAs($this->jefe())
            ->get("/tareas?proyecto_id={$proyectoB->id}")
            ->getContent();

        $this->assertStringContainsString('Tarea del proyecto B', $html);
        $this->assertStringNotContainsString('Tarea del proyecto A', $html);
    }

    public function test_filtra_por_sprint(): void
    {
        $sprint = Sprint::create([
            'proyecto_id' => Proyecto::orderBy('id')->firstOrFail()->id,
            'nombre' => 'Sprint de prueba',
        ]);

        $this->crearTarea('Tarea en sprint', ['sprint_id' => $sprint->id]);
        $this->crearTarea('Tarea sin sprint');

        $html = $this->actingAs($this->jefe())
            ->get("/tareas?sprint_id={$sprint->id}")
            ->getContent();

        $this->assertStringContainsString('Tarea en sprint', $html);
        $this->assertStringNotContainsString('Tarea sin sprint', $html);
    }

    private function jefe(): User
    {
        return User::where('email', 'jefe@example.com')->firstOrFail();
    }
}
