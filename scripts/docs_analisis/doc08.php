<?php

// DOC 08 — DSD de la segunda iteración: 7 diagramas de secuencia de diseño
// "según el código real" (uno por página en el PDF; un PNG por diagrama).

function dsdDiagrama(array $lifelines, array $mensajes): string
{
    $d = '';
    $n = count($lifelines);
    $xs = [];
    foreach ($lifelines as $i => $nombre) {
        $x = 150 + $i * (1200 / max(1, $n - 1));
        $xs[$i] = $x;
        $d .= "<div style=\"position:absolute;left:".round($x - 90)."px;top:0;width:180px;border:1.2px solid #374151;background:#f8fafc;padding:4px 2px;text-align:center;font-size:10px;font-weight:bold;color:#374151;\">{$nombre}</div>";
        $d .= lineaV($x, 30, 500, 'dashed', '#9ca3af', 1);
    }
    foreach ($mensajes as [$de, $a, $y, $texto, $tipo]) {
        $x1 = $xs[$de];
        $x2 = $xs[$a];
        if ($x1 === $x2) {
            $d .= "<div style=\"position:absolute;left:".round($x1 - 60)."px;top:".round($y - 14)."px;width:120px;text-align:center;font-size:8.5px;color:#b91c1c;\">{$texto}</div>";
            continue;
        }
        $hacia = $x2 > $x1 ? 'der' : 'izq';
        $estilo = $tipo === 'retorno' ? 'dashed' : 'solid';
        $d .= flechaH(min($x1, $x2) + ($x1 === min($x1, $x2) ? 0 : 0), max($x1, $x2), $y, $texto, $estilo, $hacia, '#374151');
    }

    return capa(1500, 510, $d);
}

function dsdPagina(string $titulo, string $intro, array $lifelines, array $mensajes, string $nota, string $mensajesTxt): array
{
    $html = "<div style=\"border:1.4px solid #e5e7eb;padding:10px 12px 2px;margin-bottom:8px;\">"
        ."<div style=\"font-size:11px;font-weight:bold;color:#008c63;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;\">{$titulo}</div>"
        .dsdDiagrama($lifelines, $mensajes)
        .'</div>'
        ."<div style=\"font-size:9.5px;line-height:1.5;color:#374151;\"><b>Qué representa:</b> {$nota}</div>"
        ."<div style=\"font-size:9px;color:#6b7280;margin-top:4px;\"><b>Mensajes:</b> {$mensajesTxt}</div>"
        .($intro !== '' ? "<div style=\"font-size:9px;color:#6b7280;margin-top:3px;\">{$intro}</div>" : '');

    return ['html' => $html, 'w' => 1500.0, 'h' => 700.0, 'titulo' => $titulo];
}

function doc08(): void
{
    $manifesto = [];

    // DSD 7 · Mover tarjeta en el tablero (CU-17)
    $dsds = [];
    $dsds[] = dsdPagina(
        'DSD 7 · Mover tarjeta en el tablero (CU-17)',
        '',
        ['PM / Jefe / PO', ':TareaController', 'Tarea (modelo)'],
        [
            [0, 1, 70, 'PATCH /tareas/mover (Accept: application/json)', 'llamada'],
            [1, 2, 120, 'validate(columnas[estado, ids]) · 422 si es inválido', 'llamada'],
            [1, 2, 170, 'visiblePara(usuario) · whereIn(id)', 'llamada'],
            [1, 2, 220, 'update(estado, orden) por posición', 'llamada'],
            [1, 0, 290, '200 {ok:true}', 'retorno'],
        ],
        'El JS del tablero envía solo las columnas que cambiaron, con el orden final de sus tarjetas; el controlador valida (la columna origen puede viajar con ids vacíos, por eso la regla es present y no required), filtra por visiblePara y guarda estado y posición de cada tarea. La tarjeta se mueve sin recargar la página. Programador y Cliente ni siquiera ven el arrastre: la ruta corta con role:Jefe|PM|PO.',
        'PATCH /tareas/mover · validate(columnas) · visiblePara · update(estado, orden) · 200 {ok:true}'
    );

    // DSD 8 · Generar informe de avance con IA (CU-19)
    $dsds[] = dsdPagina(
        'DSD 8 · Generar informe de avance con IA (CU-19)',
        '',
        ['Jefe/PM/PO/Prog.', ':InformeIAController', 'ProjectReportService', ':ProjectContextBuilder', 'Proveedor IA (config)', 'EntregableIA'],
        [
            [0, 1, 60, 'POST /proyectos/{p}/informes-ia', 'llamada'],
            [1, 2, 105, 'generate(proyecto, usuario)', 'llamada'],
            [2, 3, 150, 'build(proyecto): contexto del proyecto', 'llamada'],
            [3, 2, 195, 'avance, tareas, hitos, novedades', 'retorno'],
            [2, 4, 240, 'generate(contexto)', 'llamada'],
            [4, 2, 285, 'texto del informe', 'retorno'],
            [2, 5, 330, 'create(estado=borrador, visible_cliente=false, contexto_fuente)', 'llamada'],
            [1, 0, 395, 'redirect + "Borrador de informe generado"', 'retorno'],
        ],
        'La IA nunca publica: el informe nace siempre en borrador e invisible al cliente, con el contexto que la IA vio guardado en contexto_fuente (JSON) y el modelo registrado en modelo_ia. El proveedor es intercambiable por configuración (fake en desarrollo, OpenRouter en producción). Si el proveedor falla, se crea igualmente el entregable con mensaje_error y se redirige con error: el borrador roto queda como evidencia y no puede publicarse.',
        'POST informes-ia · build(contexto) · generate · EntregableIA::create(borrador) · alt: create(mensaje_error) + report()'
    );

    // DSD 9 · Publicar / retirar informe (CU-20)
    $dsds[] = dsdPagina(
        'DSD 9 · Publicar / retirar el informe para el Cliente (CU-20)',
        '',
        ['Jefe / PM / PO', ':InformeIAController', 'EntregableIA', 'Cliente'],
        [
            [0, 1, 60, 'PATCH /informes-ia/{e}/publicar', 'llamada'],
            [1, 1, 105, 'abort_unless(origen=ia, 404) · abort_if(mensaje_error, 422)', 'llamada'],
            [1, 2, 160, 'update(estado=aprobado, visible_cliente=true, aprobado_por, aprobado_en)', 'llamada'],
            [1, 0, 225, 'redirect + "Informe aprobado y publicado"', 'retorno'],
            [2, 3, 285, 'el portal solo muestra entregables aprobados', 'retorno'],
        ],
        'La publicación es la acción de control del PO sobre la IA: registra quién aprobó y cuándo. Un informe con error registrado no se puede publicar (422). El retiro (PATCH /informes-ia/{e}/retirar) deshace la publicación: estado=revisado, visible_cliente=false, aprobado_por/aprobado_en en null.',
        'PATCH publicar · aborts de guarda · update(aprobado + visible_cliente) · unpublish → revisado'
    );

    // DSD 10 · Registrar novedad (CU-18)
    $dsds[] = dsdPagina(
        'DSD 10 · Registrar una novedad del proyecto (CU-18)',
        '',
        ['Usuario del equipo', ':ActualizacionProyectoController', 'ActualizacionProyecto', 'Cliente'],
        [
            [0, 1, 60, 'POST /proyectos/{p}/actualizaciones', 'llamada'],
            [1, 1, 105, 'validate[titulo, descripcion, tipo, fecha, visible_cliente]', 'llamada'],
            [1, 2, 165, 'create([...data, creado_por=usuario])', 'llamada'],
            [1, 0, 225, 'redirect a la ficha + "Actualización registrada"', 'retorno'],
            [2, 3, 285, 'si visible_cliente=true aparece en el portal (Novedades)', 'retorno'],
        ],
        'Cualquier rol del equipo puede dejar novedades en la ficha (avance, problema, decisión o próximo paso). El interruptor visible_cliente decide si la nota sale en el portal del cliente o queda como nota interna: el mismo dato alimenta las dos vistas con distinto alcance.',
        'POST actualizaciones · validate(4 tipos) · create · visible_cliente → portal'
    );

    // DSD 11 · Portal del cliente (CU-13/CU-24)
    $dsds[] = dsdPagina(
        'DSD 11 · El Cliente consulta su proyecto (CU-13, CU-24)',
        '',
        ['Cliente', ':ProyectoController', 'Proyecto (línea de tiempo)', 'EntregableIA'],
        [
            [0, 1, 60, 'GET /proyectos/{p} (portal)', 'llamada'],
            [1, 1, 105, 'puedeVer(proyecto) · 403 si no es suyo', 'llamada'],
            [1, 2, 150, 'load hitos + sprints.tareas', 'llamada'],
            [1, 1, 195, 'estados: Completado / Atrasado / En curso / Pendiente', 'llamada'],
            [1, 2, 240, 'novedades where visible_cliente=true', 'llamada'],
            [1, 3, 285, 'entregables where estado=aprobado', 'llamada'],
            [1, 0, 350, 'view cliente.proyecto (línea de tiempo + pop-ups)', 'retorno'],
        ],
        'La ficha del cliente se arma entera en el servidor: hitos y sprints se ordenan por fecha y se etiquetan (un sprint terminado con tareas sin completar es "Atrasado"; el primer punto no terminado lleva el "Estamos acá"); con más de dos etapas completadas el historial colapsa en un nodo-resumen. El cliente ve solo novedades marcadas visibles y entregables aprobados; el PDF del CU-24 usa la misma regla de filtrado.',
        'GET proyectos/{p} · puedeVer · armado de línea · visible_cliente · estado=aprobado'
    );

    // DSD 12 · Resumen IA de sprint (CU-21)
    $dsds[] = dsdPagina(
        'DSD 12 · Generar el resumen IA de un sprint (CU-21)',
        '',
        ['Usuario del equipo', ':SprintSummaryController', 'SprintSummaryService', 'Modelo IA / caché'],
        [
            [0, 1, 60, 'POST /sprints/{s}/resumen-ia', 'llamada'],
            [1, 2, 105, 'generate(sprint, forzar)', 'llamada'],
            [2, 3, 150, 'ya existe resumen (y sin forzar) → devolver cacheado', 'llamada'],
            [2, 3, 195, 'si no: armar contexto de tareas → pedir resumen', 'llamada'],
            [3, 2, 240, 'resumen + modelo', 'retorno'],
            [1, 0, 300, 'JSON {sprint_id, resumen, modelo, cacheado} · 200 cacheado / 201 nuevo', 'retorno'],
        ],
        'El resumen se cachea por sprint: pedirlo de nuevo no vuelve a gastar tokens del proveedor salvo regeneración forzada. Sin configuración de IA la API responde 503 con un mensaje claro; ante un error del proveedor, 502 y el error queda registrado. Además, los lunes 08:00 una tarea programada envía al Jefe el resumen de cada sprint activo.',
        'POST resumen-ia · cache por sprint · 200/201 · alt 503 configuración, 502 proveedor · schedule lunes 08:00'
    );

    // DSD 13 · Reportes y exportación (CU-22/CU-23)
    $dsds[] = dsdPagina(
        'DSD 13 · Consultar y exportar reportes (CU-22, CU-23, CU-24)',
        '',
        ['Jefe / PM / PO', ':ReportesController', 'Proyectos / Facturas', 'dompdf (PDF)'],
        [
            [0, 1, 60, 'GET /reportes/proyectos?cliente_id&pm_id&estado&desde&hasta', 'llamada'],
            [1, 2, 105, 'armarReporteProyectos: filtros + métricas por proyecto', 'llamada'],
            [1, 2, 150, 'regla RETRASADO: fin estimada vencida sin completar/cancelar', 'llamada'],
            [1, 0, 195, 'pantalla con totales, gráficos y paginación de 15', 'retorno'],
            [0, 1, 255, 'GET .../exportar?formato=pdf', 'llamada'],
            [1, 1, 300, 'codigoReporte: Cache::increment → REP-PRO-2026-NNN', 'llamada'],
            [1, 3, 345, 'vista institucional (identidad, alcance, criterio, paginación)', 'llamada'],
            [1, 0, 400, 'PDF stream/download (o CSV con BOM y fila TOTAL)', 'retorno'],
        ],
        'La pantalla y las dos exportaciones consumen exactamente la misma consulta filtrada. El PDF sigue el patrón de la cátedra: identidad institucional repetida, alcance (período y filtros), trazabilidad (usuario, fecha/hora y código correlativo por módulo y año), resultados con banner y totales, y navegación (Página X de Y). El CSV se transmite por streaming con BOM UTF-8 para que Excel respete los acentos.',
        'armarReporte · Cache::increment → REP-XXX-AAAA-NNN · dompdf · StreamedResponse (CSV)'
    );

    // PDF: una página landscape por DSD
    $cuerpo = '';
    foreach ($dsds as $i => $dsd) {
        $escala = 750 / $dsd['w'];
        $anchoEsc = round($dsd['w'] * $escala);
        $altoEsc = round($dsd['h'] * $escala);
        $cuerpo .= '<div style="page-break-before: '.($i === 0 ? 'avoid' : 'always').';">'
            ."<div style=\"width: {$anchoEsc}px; height: {$altoEsc}px; overflow: hidden;\">"
            ."<div style=\"transform: scale({$escala}); transform-origin: top left; width: {$dsd['w']}px;\">{$dsd['html']}</div>"
            .'</div></div>';
        registrarPng($manifesto, sprintf('08_DSD_%02d', $i + 7), $dsd['titulo'], $dsd['html'], $dsd['w'], $dsd['h']);
    }
    escribirManifesto($manifesto);

    pdf(documento(
        'DSD — Diseño Detallado v4.0',
        'Cruz Negra — Diagramas de secuencia de la segunda iteración, según el código real · Octubre 2026',
        $cuerpo,
        'DOC-DSD-2026-008',
        'landscape',
    ), '08_CruzNegra_DSD_Nuevos.pdf', 'landscape');
}

return 'doc08';
