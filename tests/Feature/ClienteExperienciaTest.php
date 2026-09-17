<?php

namespace Tests\Feature;

use App\Models\EntregableIA;
use App\Models\Hito;
use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Experiencia del rol Cliente: qué ve, qué no, y qué puede hacer.
 * Trabaja las decisiones de alcance del rol para que no se rompan.
 */
class ClienteExperienciaTest extends TestCase
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

    /** @test */
    public function ningun_listado_le_muestra_botones_de_creacion(): void
    {
        $cliente = $this->cliente();

        foreach ([
            '/proyectos' => 'Nuevo Proyecto',
            '/tareas' => 'Nueva Tarea',
            '/hitos' => 'Nuevo Hito',
            '/solicitudes-cambio' => 'Nueva Solicitud de Cambio',
            '/entregables' => 'Nuevo Entregable',
            '/facturas' => 'Nueva Factura',
            '/sprints' => 'Nuevo Sprint',
        ] as $ruta => $boton) {
            $this->actingAs($cliente)
                ->get($ruta)
                ->assertOk()
                ->assertDontSee($boton, false);
        }
    }

    /** @test */
    public function los_modales_de_crud_no_se_renderizan_para_el_cliente(): void
    {
        $cliente = $this->cliente();

        foreach (['/proyectos', '/entregables', '/facturas'] as $ruta) {
            $html = $this->actingAs($cliente)->get($ruta)->getContent();

            $this->assertStringNotContainsString('-crear"', $html, "La pagina $ruta filtra el modal de creacion");
            $this->assertStringNotContainsString('Seleccioná un proyecto', $html, "La pagina $ruta filtra listas internas");
        }
    }

    /** @test */
    public function entregables_solo_aprobados_y_solo_de_su_empresa(): void
    {
        $cliente = $this->cliente();

        // Escenario deterministico sobre el seed:
        // - "Borrador de manual" (su empresa, borrador) se aprueba -> visible.
        // - "Resumen de reunion" (su empresa, revisado) -> oculto.
        // - "Esquema legal" (otra empresa, aprobado) -> oculto.
        EntregableIA::where('titulo', 'Borrador de manual de usuario')
            ->update(['estado' => 'aprobado']);

        $html = $this->actingAs($cliente)->get('/entregables')->getContent();

        $this->assertStringContainsString('Borrador de manual de usuario', $html);
        $this->assertStringNotContainsString('Resumen de reunion', $html);
        $this->assertStringNotContainsString('Esquema legal', $html);
    }

    /** @test */
    public function el_menu_del_cliente_solo_muestra_lo_que_le_corresponde(): void
    {
        $html = $this->actingAs($this->cliente())->get('/proyectos')->getContent();

        // Visibles: solo lo que le importa (pantallas minimas).
        foreach (['Dashboard', 'Mis proyectos', 'Entregables', 'Facturas'] as $link) {
            $this->assertStringContainsString($link, $html, "Falta el link $link en el menu del Cliente");
        }

        // Ocultos: herramientas internas del equipo y pantallas que quedan
        // absorbidas (hitos y cambios se ven dentro del detalle del proyecto).
        foreach (['Mi trabajo', 'Usuarios y roles', 'Auditoría', '>Sprints</a>', '>Tareas</a>', '>Hitos</a>', '>Cambios</a>'] as $link) {
            $this->assertStringNotContainsString($link, $html);
        }
    }

    /** @test */
    public function el_dashboard_del_cliente_no_muestra_reportes_internos(): void
    {
        $this->actingAs($this->cliente())
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Reportes generales')
            ->assertDontSee('Total facturado')
            ->assertDontSee('Tareas vencidas');
    }

    /** @test */
    public function acciones_de_escritura_por_url_son_rechazadas(): void
    {
        $cliente = $this->cliente();

        $this->actingAs($cliente)->post('/proyectos', [])->assertForbidden();
        $this->actingAs($cliente)->patchJson(route('tareas.mover'), [
            'columnas' => [['estado' => 'completada', 'ids' => [1]]],
        ])->assertForbidden();
        $this->actingAs($cliente)->delete('/facturas/1')->assertForbidden();
    }

    /** @test */
    public function el_detalle_del_proyecto_del_cliente_es_una_linea_de_tiempo(): void
    {
        $cliente = $this->cliente();
        $proyecto = Proyecto::where('cliente_id', $cliente->cliente_id)->firstOrFail();

        // hitos y sprints del proyecto para tener contenido en la linea
        $hito = Hito::create([
            'nombre' => 'Hito timeline',
            'fecha_objetivo' => today()->addDays(10),
            'completado' => false,
            'proyecto_id' => $proyecto->id,
        ]);
        $sprint = Sprint::create([
            'nombre' => 'Sprint timeline',
            'proyecto_id' => $proyecto->id,
            'fecha_inicio' => today()->subDays(5),
        ]);

        $respuesta = $this->actingAs($cliente)->get(route('proyectos.show', $proyecto));

        $respuesta->assertOk()
            ->assertSee('Cómo viene el proyecto')
            ->assertSee('Hito timeline')
            ->assertSee('Sprint timeline')
            ->assertSee('Avance del proyecto')
            // nada de herramientas internas
            ->assertDontSee('Nueva Tarea', false)
            ->assertDontSee('Asignar a', false);
    }
}
