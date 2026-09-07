<?php

namespace Tests\Feature;

use App\Models\Proyecto;
use App\Models\Sprint;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SprintSummaryEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        config()->set('services.openrouter.enabled', true);
        config()->set('services.openrouter.api_key', 'test-openrouter-key');
    }

    public function test_internal_user_can_generate_a_summary_with_minimal_context(): void
    {
        $sprint = $this->createSprintWithTasks();
        $pm = User::where('email', 'pm@example.com')->firstOrFail();

        Http::fake([
            'https://openrouter.ai/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Resumen ejecutivo del sprint.']],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($pm)
            ->postJson(route('sprints.resumen-ia.store', $sprint));

        $response->assertCreated()
            ->assertJson([
                'sprint_id' => $sprint->id,
                'resumen' => 'Resumen ejecutivo del sprint.',
                'cacheado' => false,
            ]);

        $this->assertDatabaseHas('sprints', [
            'id' => $sprint->id,
            'resumen_ia' => 'Resumen ejecutivo del sprint.',
        ]);

        Http::assertSent(function (ClientRequest $request): bool {
            $data = $request->data();
            $prompt = $data['messages'][1]['content'];

            $this->assertSame('meta-llama/llama-3.3-70b-instruct:free', $data['model']);
            $this->assertStringContainsString('Crear modelo Expediente', $prompt);
            $this->assertStringContainsString('"tarjetas"', $prompt);
            $this->assertStringNotContainsString('"id"', $prompt);
            $this->assertStringNotContainsString('"created_at"', $prompt);

            return true;
        });
    }

    public function test_cached_summary_does_not_call_openrouter_without_forzar(): void
    {
        $sprint = $this->createSprintWithTasks();
        $sprint->update(['resumen_ia' => 'Resumen ya guardado.']);
        $pm = User::where('email', 'pm@example.com')->firstOrFail();
        Http::fake();

        $this->actingAs($pm)
            ->postJson(route('sprints.resumen-ia.store', $sprint))
            ->assertOk()
            ->assertJson([
                'resumen' => 'Resumen ya guardado.',
                'cacheado' => true,
            ]);

        Http::assertNothingSent();
    }

    public function test_rate_limit_uses_the_fallback_model(): void
    {
        $sprint = $this->createSprintWithTasks();
        $pm = User::where('email', 'pm@example.com')->firstOrFail();

        Http::fakeSequence()
            ->pushStatus(429)
            ->push([
                'choices' => [
                    ['message' => ['content' => 'Resumen generado con fallback.']],
                ],
            ], 200);

        $response = $this->actingAs($pm)
            ->postJson(route('sprints.resumen-ia.store', $sprint));

        $response->assertCreated()
            ->assertJson([
                'resumen' => 'Resumen generado con fallback.',
                'modelo' => 'qwen/qwen-2.5-coder-32b-instruct:free',
            ]);

        $requests = Http::recorded();
        $this->assertCount(2, $requests);
        $this->assertSame(
            'meta-llama/llama-3.3-70b-instruct:free',
            $requests[0][0]->data()['model']
        );
        $this->assertSame(
            'qwen/qwen-2.5-coder-32b-instruct:free',
            $requests[1][0]->data()['model']
        );
    }

    public function test_client_cannot_call_the_summary_endpoint(): void
    {
        $sprint = $this->createSprintWithTasks();
        $cliente = User::where('email', 'cliente@example.com')->firstOrFail();
        Http::fake();

        $this->actingAs($cliente)
            ->postJson(route('sprints.resumen-ia.store', $sprint))
            ->assertForbidden();

        Http::assertNothingSent();
    }

    public function test_provider_failure_does_not_persist_a_summary(): void
    {
        $sprint = $this->createSprintWithTasks();
        $pm = User::where('email', 'pm@example.com')->firstOrFail();

        Http::fakeSequence()
            ->pushStatus(429)
            ->pushStatus(503);

        $this->actingAs($pm)
            ->postJson(route('sprints.resumen-ia.store', $sprint))
            ->assertStatus(502)
            ->assertJson([
                'message' => 'No fue posible generar el resumen del sprint. Intente nuevamente más tarde.',
            ]);

        $this->assertDatabaseHas('sprints', [
            'id' => $sprint->id,
            'resumen_ia' => null,
        ]);
    }

    private function createSprintWithTasks(): Sprint
    {
        $proyecto = Proyecto::where('nombre', 'Gestor de expedientes Gimenez')->firstOrFail();
        $sprint = Sprint::create([
            'proyecto_id' => $proyecto->id,
            'nombre' => 'Sprint de integración',
            'descripcion' => 'Trabajo funcional del ciclo.',
            'fecha_inicio' => '2026-08-31',
            'fecha_fin' => '2026-09-11',
            'estado' => 'en_progreso',
        ]);

        Tarea::where('proyecto_id', $proyecto->id)
            ->update(['sprint_id' => $sprint->id]);

        return $sprint;
    }
}
