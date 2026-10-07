<?php

require __DIR__.'/_base.php';
require __DIR__.'/diagramas.php';

$docs = [
    '06' => fn () => require __DIR__.'/doc06.php',
    '07' => function () {
        $manifesto = [];
        $pares = [
            'casos_uso' => ['Diagrama de casos de uso', diagramaCasosUso()],
            'dominio' => ['Modelo de dominio', diagramaDominio()],
        ];

        $cuerpo = '';
        $i = 0;
        foreach ($pares as $clave => [$nombre, $diag]) {
            $escala = 722 / $diag['w'];
            $anchoEsc = round($diag['w'] * $escala);
            $altoEsc = round($diag['h'] * $escala);
            $cuerpo .= '<div style="page-break-before: '.($i++ === 0 ? 'avoid' : 'always').';">'
                .'<h2 class="seccion">'.$nombre.' (v4.0)</h2>'
                ."<div style=\"width: {$anchoEsc}px; height: {$altoEsc}px; overflow: hidden;\">"
                ."<div style=\"transform: scale({$escala}); transform-origin: top left; width: {$diag['w']}px;\">{$diag['html']}</div>"
                .'</div></div>';
            registrarPng($manifesto, "07_{$clave}", $nombre.' — CRUZNEGRA v4.0', $diag['html'], $diag['w'], $diag['h']);
        }
        escribirManifesto($manifesto);

        pdf(documento(
            'Diagramas v4.0 — Casos de uso y dominio',
            'Cruz Negra — 5 actores, 25 casos de uso y 10 entidades · Octubre 2026',
            $cuerpo,
            'DOC-DIA-2026-007',
        ), '07_CruzNegra_Diagramas_v4.pdf');
    },
    '08' => fn () => (require __DIR__.'/doc08.php')(),
    '09' => function () {
        $pares = [
            'relacional' => ['Modelo relacional (v4.0) — 10 tablas, según migraciones de la BD cruznegra', diagramaRelacional()],
            'clases' => ['Diagrama de clases del modelo (app/Models) — 10 modelos Eloquent', diagramaClases()],
        ];

        $cuerpo = '';
        $i = 0;
        foreach ($pares as [$titulo, $diag]) {
            $escala = 722 / $diag['w'];
            $anchoEsc = round($diag['w'] * $escala);
            $altoEsc = round($diag['h'] * $escala);
            $cuerpo .= '<div style="page-break-before: '.($i++ === 0 ? 'avoid' : 'always').';">'
                .'<h2 class="seccion">'.$titulo.'</h2>'
                ."<div style=\"width: {$anchoEsc}px; height: {$altoEsc}px; overflow: hidden;\">"
                ."<div style=\"transform: scale({$escala}); transform-origin: top left; width: {$diag['w']}px;\">{$diag['html']}</div>"
                .'</div></div>';
        }

        pdf(documento(
            'Modelo de datos v4.0',
            'Cruz Negra — Modelo relacional y clases Eloquent · Octubre 2026',
            $cuerpo,
            'DOC-DAT-2026-009',
        ), '09_CruzNegra_Laravel_Relacional_Clases_v4.pdf');
    },
];

$cuales = array_slice($argv, 1) ?: array_keys($docs);
foreach ($cuales as $cual) {
    if (! isset($docs[$cual])) {
        echo "No existe el doc '{$cual}'. Disponibles: ".implode(', ', array_keys($docs))."\n";
        exit(1);
    }
    ($docs[$cual])();
}
