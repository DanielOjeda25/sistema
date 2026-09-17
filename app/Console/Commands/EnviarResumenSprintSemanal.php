<?php

namespace App\Console\Commands;

use App\Mail\ResumenSprintSemanal;
use App\Models\Sprint;
use App\Models\User;
use App\Services\AI\SprintSummaryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EnviarResumenSprintSemanal extends Command
{
    protected $signature = 'informes:resumen-sprint-semanal';

    protected $description = 'Envía al Jefe el resumen IA del sprint activo de cada proyecto';

    public function handle(SprintSummaryService $servicio): int
    {
        // whereHas en vez de User::role(): si nadie creó el rol todavía,
        // esto devuelve una colección vacía en lugar de lanzar excepción.
        $jefes = User::query()->whereHas('roles', fn ($rol) => $rol->where('name', 'Jefe'))->get();

        if ($jefes->isEmpty()) {
            $this->warn('No hay usuarios con rol Jefe.');

            return self::SUCCESS;
        }

        $sprints = Sprint::where('estado', 'activo')->with('proyecto')->get();

        if ($sprints->isEmpty()) {
            $this->info('No hay sprints activos esta semana.');

            return self::SUCCESS;
        }

        $fallidos = 0;

        foreach ($sprints as $sprint) {
            try {
                $resultado = $servicio->generate($sprint, false);
                $cuerpo = $resultado['resumen'];
            } catch (Throwable $e) {
                report($e);
                $fallidos++;
                $cuerpo = 'No fue posible generar el resumen IA de este sprint. '
                    .'Revisá los logs del sistema.';
            }

            foreach ($jefes as $jefe) {
                Mail::to($jefe->email)->send(new ResumenSprintSemanal($sprint, $cuerpo));
            }
        }

        if ($fallidos > 0) {
            $this->warn("{$fallidos} sprint(s) sin resumen IA (se envió igualmente un aviso al Jefe).");
        }

        $this->info("Enviados resúmenes de {$sprints->count()} sprint(s) a {$jefes->count()} Jefe(s).");

        return self::SUCCESS;
    }
}
