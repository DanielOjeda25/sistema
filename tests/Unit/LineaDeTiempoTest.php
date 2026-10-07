<?php

namespace Tests\Unit;

use App\Models\Hito;
use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\Tarea;
use App\Models\User;
use App\Support\LineaDeTiempo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Reglas de la línea de tiempo del portal del Cliente, probadas una por una
 * y sin HTTP: la clase App\Support\LineaDeTiempo recibe un proyecto con sus
 * hitos y sprints y decide el estado y el detalle de cada punto.
 *
 * Convención de lectura de cada test: armamos la situación concreta, pedimos
 * la línea y afirmamos sobre el punto que nos importa (buscarPorTitulo).
 */
class LineaDeTiempoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // ------------------------------------------------------------------
    // Ayudantes: un proyecto limpio y funciones para armar situaciones.
    // ------------------------------------------------------------------

    private function proyecto(): Proyecto
    {
        return Proyecto::create([
            'nombre' => 'Proyecto de línea de tiempo',
            'descripcion' => 'Proyecto exclusivo del test',
            'fecha_inicio' => today()->subMonths(3),
            'fecha_fin_estimada' => today()->addMonths(3),
            'estado' => 'en_progreso',
            'cliente_id' => 1,
            'pm_id' => User::where('email', 'pm@example.com')->firstOrFail()->id,
        ]);
    }

    private function linea(Proyecto $proyecto): \Illuminate\Support\Collection
    {
        return (new LineaDeTiempo)->construir($proyecto);
    }

    /** El punto de la línea con ese título (hitos y sprints comparten el espacio). */
    private function punto(\Illuminate\Support\Collection $linea, string $titulo): array
    {
        $punto = $linea->firstWhere('titulo', $titulo);
        $this->assertNotNull($punto, "No hay ningún punto llamado {$titulo} en la línea.");

        return $punto;
    }

    private function hito(Proyecto $proyecto, string $nombre, string $fecha, bool $completado = false): Hito
    {
        return Hito::create([
            'nombre' => $nombre,
            'descripcion' => 'Hito del test',
            'fecha_objetivo' => $fecha,
            'completado' => $completado,
            'proyecto_id' => $proyecto->id,
        ]);
    }

    private function sprint(Proyecto $proyecto, string $nombre, ?string $inicio = null, ?string $fin = null): Sprint
    {
        return Sprint::create([
            'nombre' => $nombre,
            'proyecto_id' => $proyecto->id,
            'fecha_inicio' => $inicio,
            'fecha_fin' => $fin,
        ]);
    }

    private function tarea(Sprint $sprint, string $titulo, string $estado = 'pendiente'): Tarea
    {
        return Tarea::create([
            'titulo' => $titulo,
            'descripcion' => 'Tarea del test',
            'estado' => $estado,
            'prioridad' => 'media',
            'proyecto_id' => $sprint->proyecto_id,
            'sprint_id' => $sprint->id,
            'asignado_a' => User::where('email', 'dev@example.com')->firstOrFail()->id,
        ]);
    }

    // ------------------------------------------------------------------
    // Hitos
    // ------------------------------------------------------------------

    #[Test]
    public function un_hito_completado_queda_como_completado(): void
    {
        $proyecto = $this->proyecto();
        $this->hito($proyecto, 'Relevamiento', today()->subDays(20)->toDateString(), completado: true);

        $punto = $this->punto($this->linea($proyecto), 'Relevamiento');

        $this->assertTrue($punto['hecho']);
        $this->assertSame('Completado', $punto['estado']);
        $this->assertFalse($punto['vencido']);
    }

    #[Test]
    public function un_hito_sin_completar_y_con_fecha_pasada_queda_atrasado(): void
    {
        $proyecto = $this->proyecto();
        $this->hito($proyecto, 'Contrato firmado', today()->subDays(5)->toDateString());

        $punto = $this->punto($this->linea($proyecto), 'Contrato firmado');

        $this->assertFalse($punto['hecho']);
        $this->assertTrue($punto['vencido']);
        $this->assertSame('Atrasado', $punto['estado']);
    }

    #[Test]
    public function el_primer_punto_sin_terminar_es_en_curso_y_lo_demas_pendiente(): void
    {
        $proyecto = $this->proyecto();
        $this->hito($proyecto, 'Arranque', today()->subDays(30)->toDateString(), completado: true);
        $this->hito($proyecto, 'Etapa actual', today()->addDays(5)->toDateString());
        $this->hito($proyecto, 'Etapa futura', today()->addDays(40)->toDateString());

        $linea = $this->linea($proyecto);

        $this->assertSame('En curso', $this->punto($linea, 'Etapa actual')['estado']);
        $this->assertSame('Pendiente', $this->punto($linea, 'Etapa futura')['estado']);
    }

    #[Test]
    public function un_atrasado_gana_el_lugar_del_en_curso(): void
    {
        // El "Estamos acá" no puede caer en un punto vencido: si lo primero
        // sin terminar está atrasado, el "En curso" pasa al punto siguiente.
        $proyecto = $this->proyecto();
        $this->hito($proyecto, 'Atrasado', today()->subDays(10)->toDateString());
        $this->hito($proyecto, 'Vigente', today()->addDays(10)->toDateString());

        $linea = $this->linea($proyecto);

        $this->assertSame('Atrasado', $this->punto($linea, 'Atrasado')['estado']);
        $this->assertSame('En curso', $this->punto($linea, 'Vigente')['estado']);
    }

    // ------------------------------------------------------------------
    // Sprints: los cinco estados del detalle en lenguaje del cliente
    // ------------------------------------------------------------------

    #[Test]
    public function sprint_terminado_con_todo_listo_queda_completado(): void
    {
        $proyecto = $this->proyecto();
        $sprint = $this->sprint($proyecto, 'Iteración 1', today()->subDays(20)->toDateString(), today()->subDays(10)->toDateString());
        $this->tarea($sprint, 'A', 'completada');
        $this->tarea($sprint, 'B', 'completada');

        $punto = $this->punto($this->linea($proyecto), 'Iteración 1');

        $this->assertTrue($punto['hecho']);
        $this->assertSame('Completado', $punto['estado']);
        $this->assertSame('Las 2 actividades están listas', $punto['detalle']);
        $this->assertSame(100, $punto['avance']);
    }

    #[Test]
    public function sprint_con_una_sola_actividad_redacta_en_singular(): void
    {
        $proyecto = $this->proyecto();
        $sprint = $this->sprint($proyecto, 'Parche rápido', today()->subDays(10)->toDateString(), today()->subDays(5)->toDateString());
        $this->tarea($sprint, 'Única', 'completada');

        $punto = $this->punto($this->linea($proyecto), 'Parche rápido');

        $this->assertSame('La actividad está lista', $punto['detalle']);
    }

    #[Test]
    public function sprint_terminado_con_pendientes_queda_atrasado_y_lo_dice(): void
    {
        $proyecto = $this->proyecto();
        $sprint = $this->sprint($proyecto, 'Iteración 2', today()->subDays(15)->toDateString(), today()->subDays(2)->toDateString());
        $this->tarea($sprint, 'A', 'completada');
        $this->tarea($sprint, 'B', 'en_progreso');

        $punto = $this->punto($this->linea($proyecto), 'Iteración 2');

        $this->assertFalse($punto['hecho']);
        $this->assertTrue($punto['vencido']);
        $this->assertSame('Atrasado', $punto['estado']);
        $this->assertSame('Finalizó el '.$sprint->fecha_fin->format('d/m/Y').' con actividades sin completar', $punto['detalle']);
        $this->assertSame(50, $punto['avance']);
    }

    #[Test]
    public function sprint_terminado_sin_actividades_cargadas_queda_cerrado(): void
    {
        $proyecto = $this->proyecto();
        $sprint = $this->sprint($proyecto, 'Sprint vacío', today()->subDays(15)->toDateString(), today()->subDays(8)->toDateString());

        $punto = $this->punto($this->linea($proyecto), 'Sprint vacío');

        // Sin tareas no hay nada pendiente: para el cliente es una etapa cerrada.
        $this->assertTrue($punto['hecho']);
        $this->assertSame('Completado', $punto['estado']);
        $this->assertSame('Cerró el '.$sprint->fecha_fin->format('d/m/Y').' sin actividades registradas', $punto['detalle']);
    }

    #[Test]
    public function sprint_futuro_muestra_lo_planificado(): void
    {
        $proyecto = $this->proyecto();
        $sprint = $this->sprint($proyecto, 'Iteración 3', today()->addDays(10)->toDateString(), today()->addDays(24)->toDateString());
        $this->tarea($sprint, 'Futura');

        $punto = $this->punto($this->linea($proyecto), 'Iteración 3');

        $this->assertFalse($punto['hecho']);
        $this->assertFalse($punto['vencido']);
        $this->assertSame('1 actividad planificada', $punto['detalle']);
        // Caso borde: si es el único punto sin terminar de toda la línea,
        // el "Estamos acá" cae en él aunque todavía no haya empezado.
        // (Con etapas previas en la línea, un sprint futuro queda Pendiente:
        // lo cubre el_primer_punto_sin_terminar_es_en_curso_y_lo_demas_pendiente.)
        $this->assertSame('En curso', $punto['estado']);
    }

    #[Test]
    public function sprint_en_curso_muestra_las_actividades_en_camino(): void
    {
        $proyecto = $this->proyecto();
        $sprint = $this->sprint($proyecto, 'Iteración actual', today()->subDays(3)->toDateString(), today()->addDays(11)->toDateString());
        $this->tarea($sprint, 'A');
        $this->tarea($sprint, 'B');

        $punto = $this->punto($this->linea($proyecto), 'Iteración actual');

        $this->assertSame('2 actividades en camino', $punto['detalle']);
        $this->assertSame('En curso', $punto['estado']);
    }

    #[Test]
    public function sprint_sin_fechas_ni_tareas_queda_por_definir(): void
    {
        $proyecto = $this->proyecto();
        $this->sprint($proyecto, 'Etapa por armar');

        $punto = $this->punto($this->linea($proyecto), 'Etapa por armar');

        $this->assertSame('Actividades por definir', $punto['detalle']);
        $this->assertFalse($punto['hecho']);
    }

    // ------------------------------------------------------------------
    // La línea completa: orden y colapso del historial
    // ------------------------------------------------------------------

    #[Test]
    public function la_linea_mezcla_hitos_y_sprints_ordenados_por_fecha(): void
    {
        $proyecto = $this->proyecto();
        $this->hito($proyecto, 'Hito central', today()->subDays(10)->toDateString(), completado: true);
        $this->sprint($proyecto, 'Sprint anterior', today()->subDays(25)->toDateString(), today()->subDays(15)->toDateString());
        $this->sprint($proyecto, 'Sprint posterior', today()->subDays(5)->toDateString(), today()->addDays(9)->toDateString());

        $titulos = $this->linea($proyecto)->pluck('titulo')->all();

        $this->assertSame(['Sprint anterior', 'Hito central', 'Sprint posterior'], $titulos);
    }

    #[Test]
    public function con_mas_de_dos_etapas_completadas_el_historial_colapsa_en_un_resumen(): void
    {
        $proyecto = $this->proyecto();
        $this->hito($proyecto, 'Primera', today()->subDays(60)->toDateString(), completado: true);
        $this->hito($proyecto, 'Segunda', today()->subDays(40)->toDateString(), completado: true);
        $this->hito($proyecto, 'Tercera', today()->subDays(20)->toDateString(), completado: true);
        $this->hito($proyecto, 'Actual', today()->addDays(5)->toDateString());

        $linea = $this->linea($proyecto);

        // El primer nodo resume el pasado (sin la última completada, que queda
        // como contexto) y las etapas sin terminar siguen visibles una a una.
        $this->assertSame('resumen', $linea[0]['tipo']);
        $this->assertSame('Etapas completadas (2)', $linea[0]['titulo']);
        $this->assertCount(2, $linea[0]['historial']);
        $this->assertSame(['Primera', 'Segunda'], collect($linea[0]['historial'])->pluck('titulo')->all());
        $this->assertSame(['Tercera', 'Actual'], $linea->pluck('titulo')->except(0)->values()->all());
    }

    #[Test]
    public function con_dos_etapas_completadas_no_colapsa_nada(): void
    {
        $proyecto = $this->proyecto();
        $this->hito($proyecto, 'Primera', today()->subDays(30)->toDateString(), completado: true);
        $this->hito($proyecto, 'Segunda', today()->subDays(10)->toDateString(), completado: true);
        $this->hito($proyecto, 'Actual', today()->addDays(5)->toDateString());

        $linea = $this->linea($proyecto);

        $this->assertNull($linea->firstWhere('tipo', 'resumen'));
        $this->assertSame(['Primera', 'Segunda', 'Actual'], $linea->pluck('titulo')->all());
    }
}
