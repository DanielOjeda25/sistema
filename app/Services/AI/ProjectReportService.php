<?php

namespace App\Services\AI;

use App\Contracts\ProjectReportGenerator;
use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Models\User;
use Throwable;

class ProjectReportService
{
    public function __construct(
        private readonly ProjectContextBuilder $contextBuilder,
        private readonly ProjectReportGenerator $generator,
    ) {}

    public function generate(Proyecto $proyecto, User $usuario): EntregableIA
    {
        $contexto = $this->contextBuilder->build($proyecto);

        $base = [
            'titulo' => 'Informe de avance - '.$proyecto->nombre.' - '.now()->format('d/m/Y H:i'),
            'tipo' => 'informe_avance',
            'origen' => 'ia',
            'modelo_ia' => config('ai.model'),
            'version_prompt' => config('ai.prompt_version'),
            'contexto_fuente' => $contexto,
            'estado' => 'borrador',
            'visible_cliente' => false,
            'generado_en' => now(),
            'proyecto_id' => $proyecto->id,
            'generado_por' => $usuario->id,
        ];

        try {
            if (! config('ai.enabled') && config('ai.provider') !== 'fake') {
                throw new \RuntimeException('La generación de informes con IA está desactivada.');
            }

            return EntregableIA::create([
                ...$base,
                'contenido' => $this->generator->generate($contexto),
            ]);
        } catch (Throwable $exception) {
            EntregableIA::create([
                ...$base,
                'contenido' => 'No se pudo generar el informe. Revisá el error registrado.',
                'mensaje_error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
