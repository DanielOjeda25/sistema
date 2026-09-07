<?php

namespace App\Services\AI;

use App\Models\Sprint;

class SprintSummaryService
{
    public function __construct(
        private readonly SprintContextBuilder $contextBuilder,
        private readonly OpenRouterSprintSummaryGenerator $generator,
    ) {}

    /**
     * @return array{resumen: string, modelo: string|null, cacheado: bool}
     */
    public function generate(Sprint $sprint, bool $forzar = false): array
    {
        if (! $forzar && filled($sprint->resumen_ia)) {
            return [
                'resumen' => $sprint->resumen_ia,
                'modelo' => null,
                'cacheado' => true,
            ];
        }

        $resultado = $this->generator->generate($this->contextBuilder->build($sprint));

        $sprint->update(['resumen_ia' => $resultado['contenido']]);

        return [
            'resumen' => $resultado['contenido'],
            'modelo' => $resultado['modelo'],
            'cacheado' => false,
        ];
    }
}
