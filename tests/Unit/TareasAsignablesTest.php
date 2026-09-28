<?php

namespace Tests\Unit;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El servidor tambien tiene que cuidar quien recibe tareas: la UI solo lista
 * PM/PO/Programador, pero sin validar en el controlador un Jefe podria recibir
 * una tarea enviando la peticion a mano.
 */
class TareasAsignablesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function datos(string $asignadoA): array
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        return [
            'titulo' => 'Tarea de prueba',
            'estado' => 'pendiente',
            'prioridad' => 'media',
            'proyecto_id' => Proyecto::firstOrFail()->id,
            'asignado_a' => $asignadoA,
            'creado_por' => $jefe->id,
        ];
    }

    public function test_rechaza_asignar_una_tarea_al_jefe(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $this->actingAs($jefe)
            ->postJson(route('tareas.store'), $this->datos($jefe->id))
            ->assertJsonValidationErrors('asignado_a');
    }

    public function test_rechaza_asignar_una_tarea_a_un_cliente(): void
    {
        $cliente = User::where('email', 'cliente@example.com')->firstOrFail();

        $this->actingAs($this->jefe())
            ->postJson(route('tareas.store'), $this->datos($cliente->id))
            ->assertJsonValidationErrors('asignado_a');
    }

    public function test_acepta_asignar_a_un_programador(): void
    {
        $dev = User::where('email', 'dev@example.com')->firstOrFail();

        $this->actingAs($this->jefe())
            ->postJson(route('tareas.store'), $this->datos($dev->id))
            ->assertCreated()
            ->assertJsonPath('asignado.id', $dev->id);
    }

    private function jefe(): User
    {
        return User::where('email', 'jefe@example.com')->firstOrFail();
    }
}
