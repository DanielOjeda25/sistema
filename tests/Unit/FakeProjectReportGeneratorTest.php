<?php

namespace Tests\Unit;

use App\Services\AI\FakeProjectReportGenerator;
use PHPUnit\Framework\TestCase;

class FakeProjectReportGeneratorTest extends TestCase
{
    private function contexto(array $sobrescribir = []): array
    {
        return array_merge([
            'proyecto' => ['nombre' => 'Proyecto Demo'],
            'progreso' => [
                'porcentaje' => 40,
                'hitos_total' => 2,
                'hitos_completados' => 1,
                'tareas_vencidas' => 0,
            ],
            'tareas' => [
                ['titulo' => 'Login de usuarios', 'estado' => 'completada'],
                ['titulo' => 'Listado de obras', 'estado' => 'en_progreso'],
            ],
            'actualizaciones' => [],
        ], $sobrescribir);
    }

    public function test_cierra_con_la_nota_de_borrador_automatico(): void
    {
        $texto = (new FakeProjectReportGenerator)->generate($this->contexto());

        $this->assertStringEndsWith(
            '(Borrador automatico: el equipo lo revisa y ajusta antes de publicarlo al cliente.)',
            $texto
        );
    }

    public function test_menciona_lo_terminado_y_lo_que_esta_en_marcha(): void
    {
        $texto = (new FakeProjectReportGenerator)->generate($this->contexto());

        $this->assertStringContainsString('Login de usuarios', $texto);
        $this->assertStringContainsString('Listado de obras', $texto);
    }

    public function test_avisa_cuando_hay_una_cosa_vencida(): void
    {
        $texto = (new FakeProjectReportGenerator)->generate(
            $this->contexto(['progreso' => ['porcentaje' => 10, 'hitos_total' => 0, 'hitos_completados' => 0, 'tareas_vencidas' => 1]])
        );

        $this->assertStringContainsString('hay 1 cosa', $texto);
    }

    public function test_pluraliza_cuando_hay_varias_vencidas(): void
    {
        $texto = (new FakeProjectReportGenerator)->generate(
            $this->contexto(['progreso' => ['porcentaje' => 10, 'hitos_total' => 0, 'hitos_completados' => 0, 'tareas_vencidas' => 3]])
        );

        $this->assertStringContainsString('hay 3 cosas', $texto);
    }

    public function test_no_habla_de_atrasos_cuando_no_hay(): void
    {
        $texto = (new FakeProjectReportGenerator)->generate($this->contexto());

        $this->assertStringNotContainsString('atrasaron', $texto);
    }

    public function test_con_progreso_en_cero_dice_que_se_esta_arrancando(): void
    {
        $texto = (new FakeProjectReportGenerator)->generate(
            $this->contexto(['progreso' => ['porcentaje' => 0, 'hitos_total' => 0, 'hitos_completados' => 0, 'tareas_vencidas' => 0]])
        );

        $this->assertStringContainsString('arrancando', $texto);
    }
}
