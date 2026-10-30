<?php

use Illuminate\Support\Facades\Schedule;

// Aviso diario al PM de cada proyecto: hitos sin completar que vencen
// dentro de 7 dias (o ya vencidos). Sin duplicados por hito.
Schedule::command('avisos:hitos-por-vencer')->dailyAt('08:00');

// Cada madrugada: las facturas pendientes con vencimiento pasado quedan vencidas.
Schedule::command('facturas:marcar-vencidas')->dailyAt('00:10');

// Lunes 08:00: al Jefe le llega el resumen IA de cada sprint activo.
Schedule::command('informes:resumen-sprint-semanal')->mondays()->at('08:00');
