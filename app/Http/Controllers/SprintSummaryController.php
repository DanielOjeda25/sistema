<?php

namespace App\Http\Controllers;

use App\Models\Sprint;
use App\Services\AI\OpenRouterConfigurationException;
use App\Services\AI\SprintSummaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SprintSummaryController extends Controller
{
    public function store(
        Request $request,
        Sprint $sprint,
        SprintSummaryService $summaryService,
    ): JsonResponse {
        $data = $request->validate([
            'forzar' => ['sometimes', 'boolean'],
        ]);

        try {
            $resultado = $summaryService->generate($sprint, (bool) ($data['forzar'] ?? false));
        } catch (OpenRouterConfigurationException $exception) {
            report($exception);

            return response()->json([
                'message' => 'El resumen con IA no está configurado en este entorno.',
            ], 503);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'No fue posible generar el resumen del sprint. Intente nuevamente más tarde.',
            ], 502);
        }

        return response()->json([
            'sprint_id' => $sprint->id,
            'resumen' => $resultado['resumen'],
            'modelo' => $resultado['modelo'],
            'cacheado' => $resultado['cacheado'],
        ], $resultado['cacheado'] ? 200 : 201);
    }
}
