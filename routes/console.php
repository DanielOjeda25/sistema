<?php

use Illuminate\Support\Facades\Schedule;

// Aviso diario al PM de cada proyecto: hitos sin completar que vencen
// dentro de 7 dias (o ya vencidos). Sin duplicados por hito.
Schedule::command('avisos:hitos-por-vencer')->dailyAt('08:00');

// Lunes 08:00: al Jefe le llega el resumen IA de cada sprint activo.
Schedule::command('informes:resumen-sprint-semanal')->mondays()->at('08:00');
