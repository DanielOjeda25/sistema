<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Rango de fechas de los filtros "Desde / Hasta" de los listados.
     * Ignora valores que no sean Y-m-d validos y, si vienen al reves, los
     * invierte para no devolver cero resultados.
     *
     * @return array{0: ?string, 1: ?string} [desde, hasta]
     */
    protected function rangoFechas(Request $request, string $campoDesde = 'desde', string $campoHasta = 'hasta'): array
    {
        $valida = function (string $campo) use ($request): ?string {
            $valor = (string) $request->input($campo);

            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor) && strtotime($valor) !== false ? $valor : null;
        };

        $desde = $valida($campoDesde);
        $hasta = $valida($campoHasta);

        if ($desde && $hasta && $desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];
        }

        return [$desde, $hasta];
    }
}
