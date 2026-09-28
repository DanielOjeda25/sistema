<?php

namespace Tests\Unit;

use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Models\SolicitudCambio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Los scope visiblePara/asignables son el corazon de los permisos:
 * si se rompen, un cliente ve datos de otra empresa o el jefe recibe tareas.
 */
class VisibilidadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function cliente(): User
    {
        return User::where('email', 'cliente@example.com')->firstOrFail();
    }

    private function proyectoPropio(): Proyecto
    {
        return Proyecto::where('cliente_id', $this->cliente()->cliente_id)->firstOrFail();
    }

    private function proyectoAjeno(): Proyecto
    {
        return Proyecto::where('cliente_id', '!=', $this->cliente()->cliente_id)->firstOrFail();
    }

    public function test_el_cliente_solo_ve_proyectos_de_su_empresa(): void
    {
        $visibles = Proyecto::visiblePara($this->cliente())->pluck('id');

        $this->assertContains($this->proyectoPropio()->id, $visibles);
        $this->assertNotContains($this->proyectoAjeno()->id, $visibles);
    }

    public function test_el_equipo_interno_ve_todos_los_proyectos(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $visibles = Proyecto::visiblePara($jefe)->pluck('id');

        $this->assertContains($this->proyectoPropio()->id, $visibles);
        $this->assertContains($this->proyectoAjeno()->id, $visibles);
    }

    public function test_el_cliente_solo_ve_entregables_aprobados_de_su_empresa(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $visible = EntregableIA::create([
            'titulo' => 'Manual aprobado propio',
            'contenido' => 'Contenido',
            'tipo' => 'documento',
            'estado' => 'aprobado',
            'origen' => 'manual',
            'visible_cliente' => false,
            'proyecto_id' => $this->proyectoPropio()->id,
            'generado_por' => $jefe->id,
        ]);
        $borradorPropio = EntregableIA::create([
            'titulo' => 'Borrador propio',
            'contenido' => 'Contenido',
            'tipo' => 'documento',
            'estado' => 'borrador',
            'origen' => 'ia',
            'visible_cliente' => false,
            'proyecto_id' => $this->proyectoPropio()->id,
            'generado_por' => $jefe->id,
        ]);
        $aprobadoAjeno = EntregableIA::create([
            'titulo' => 'Aprobado de otra empresa',
            'contenido' => 'Contenido',
            'tipo' => 'documento',
            'estado' => 'aprobado',
            'origen' => 'manual',
            'visible_cliente' => false,
            'proyecto_id' => $this->proyectoAjeno()->id,
            'generado_por' => $jefe->id,
        ]);

        $visibles = EntregableIA::visiblePara($this->cliente())->pluck('id');

        $this->assertContains($visible->id, $visibles);
        $this->assertNotContains($borradorPropio->id, $visibles);
        $this->assertNotContains($aprobadoAjeno->id, $visibles);
    }

    public function test_el_cliente_solo_ve_solicitudes_de_su_empresa(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();

        $propia = SolicitudCambio::create([
            'titulo' => 'Solicitud propia',
            'descripcion' => 'Descripcion',
            'estado' => 'pendiente',
            'prioridad' => 'media',
            'proyecto_id' => $this->proyectoPropio()->id,
            'solicitado_por' => $jefe->id,
        ]);
        $ajena = SolicitudCambio::create([
            'titulo' => 'Solicitud ajena',
            'descripcion' => 'Descripcion',
            'estado' => 'pendiente',
            'prioridad' => 'media',
            'proyecto_id' => $this->proyectoAjeno()->id,
            'solicitado_por' => $jefe->id,
        ]);

        $visibles = SolicitudCambio::visiblePara($this->cliente())->pluck('id');

        $this->assertContains($propia->id, $visibles);
        $this->assertNotContains($ajena->id, $visibles);
    }

    public function test_solo_los_roles_de_trabajo_son_asignables(): void
    {
        $jefe = User::where('email', 'jefe@example.com')->firstOrFail();
        $cliente = $this->cliente();
        $pm = User::where('email', 'pm@example.com')->firstOrFail();
        $po = User::where('email', 'po@example.com')->firstOrFail();
        $dev = User::where('email', 'dev@example.com')->firstOrFail();

        $asignables = User::asignables()->pluck('id');

        $this->assertContains($pm->id, $asignables);
        $this->assertContains($po->id, $asignables);
        $this->assertContains($dev->id, $asignables);
        $this->assertNotContains($jefe->id, $asignables);
        $this->assertNotContains($cliente->id, $asignables);
    }
}
