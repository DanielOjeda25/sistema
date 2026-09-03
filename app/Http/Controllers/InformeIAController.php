<?php

namespace App\Http\Controllers;

use App\Models\EntregableIA;
use App\Models\Proyecto;
use App\Services\AI\ProjectReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class InformeIAController extends Controller
{
    public function store(
        Request $request,
        Proyecto $proyecto,
        ProjectReportService $reportService,
    ): RedirectResponse {
        try {
            $reportService->generate($proyecto, $request->user());
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('proyectos.show', $proyecto)
                ->with('error', 'No se pudo generar el informe. El error quedó registrado.');
        }

        return redirect()->route('proyectos.show', $proyecto)
            ->with('success', 'Borrador de informe generado. Revisalo antes de publicarlo.');
    }

    public function publish(Request $request, EntregableIA $entregable): RedirectResponse
    {
        abort_unless($entregable->origen === 'ia', 404);
        abort_if($entregable->mensaje_error !== null, 422, 'No se puede publicar un informe fallido.');

        $entregable->update([
            'estado' => 'aprobado',
            'visible_cliente' => true,
            'aprobado_por' => $request->user()->id,
            'aprobado_en' => now(),
            'mensaje_error' => null,
        ]);

        return redirect()->route('proyectos.show', $entregable->proyecto_id)
            ->with('success', 'Informe aprobado y publicado para el Cliente.');
    }

    public function unpublish(EntregableIA $entregable): RedirectResponse
    {
        abort_unless($entregable->origen === 'ia', 404);

        $entregable->update([
            'estado' => 'revisado',
            'visible_cliente' => false,
            'aprobado_por' => null,
            'aprobado_en' => null,
        ]);

        return redirect()->route('proyectos.show', $entregable->proyecto_id)
            ->with('success', 'Informe retirado de la vista del Cliente.');
    }
}
