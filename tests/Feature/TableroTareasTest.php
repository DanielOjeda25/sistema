<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableroTareasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_el_tablero_se_renderiza_con_modal_y_formularios_inline(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $this->actingAs($jefe)
            ->get(route('tareas.tablero'))
            ->assertOk()
            ->assertSee('data-tablero')
            ->assertSee('data-agregar')
            ->assertSee('modal-tarea')
            ->assertSee('form-editar-tarea');
    }

    public function test_cliente_ve_el_tablero_en_modo_lectura(): void
    {
        $cliente = User::role('Cliente')->firstOrFail();

        $this->actingAs($cliente)
            ->get(route('tareas.tablero'))
            ->assertOk()
            ->assertDontSee('data-agregar')
            ->assertDontSee('modal-tarea');
    }

    public function test_crear_tarjeta_ajax_la_agrega_al_final_de_su_columna(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $proyecto = Proyecto::firstOrFail();
        $ordenPrevio = (int) Tarea::where('estado', 'en_progreso')->max('orden');

        $respuesta = $this->actingAs($jefe)
            ->postJson(route('tareas.store'), [
                'titulo' => 'Tarjeta creada desde el tablero',
                'estado' => 'en_progreso',
                'prioridad' => 'media',
                'proyecto_id' => $proyecto->id,
                'asignado_a' => $jefe->id,
            ])
            ->assertCreated()
            ->assertJsonPath('titulo', 'Tarjeta creada desde el tablero')
            ->assertJsonPath('proyecto.nombre', $proyecto->nombre);

        $tarea = Tarea::find($respuesta->json('id'));
        $this->assertSame($ordenPrevio + 1, $tarea->orden);
    }

    public function test_editar_tarjeta_ajax_devuelve_la_tarea_actualizada(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $tarea = Tarea::firstOrFail();

        $this->actingAs($jefe)
            ->patchJson(route('tareas.update', $tarea), [
                'titulo' => $tarea->titulo,
                'descripcion' => 'Descripción editada en el modal',
                'estado' => 'en_progreso',
                'prioridad' => $tarea->prioridad,
                'fecha_limite' => null,
                'proyecto_id' => $tarea->proyecto_id,
                'asignado_a' => $tarea->asignado_a,
            ])
            ->assertOk()
            ->assertJsonPath('descripcion', 'Descripción editada en el modal')
            ->assertJsonPath('estado', 'en_progreso');

        $this->assertDatabaseHas('tareas', [
            'id' => $tarea->id,
            'estado' => 'en_progreso',
        ]);
    }

    public function test_eliminar_tarjeta_ajax_responde_ok(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $tarea = Tarea::firstOrFail();

        $this->actingAs($jefe)
            ->deleteJson(route('tareas.destroy', $tarea))
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertModelMissing($tarea);
    }

    public function test_mover_tarjetas_persiste_estado_y_orden(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $primera = Tarea::where('estado', 'pendiente')->orderBy('id')->firstOrFail();
        $segunda = Tarea::where('estado', 'pendiente')->orderBy('id')->skip(1)->firstOrFail();

        $this->actingAs($jefe)
            ->patchJson(route('tareas.mover'), [
                'columnas' => [
                    ['estado' => 'pendiente', 'ids' => [$segunda->id, $primera->id]],
                ],
            ])
            ->assertOk();

        $this->assertSame(0, $segunda->fresh()->orden);
        $this->assertSame(1, $primera->fresh()->orden);
    }
}
