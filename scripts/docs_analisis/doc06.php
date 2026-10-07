<?php

// DOC 06 — Análisis v4.0: actualización del documento de análisis (CU-17 a CU-25,
// glosario, restricciones y reglas nuevas, correspondencia con la implementación).

$c = '';

// --- 1. Control de versiones -------------------------------------------------
$c .= '<h2 class="seccion">1 · Control de versiones</h2>';
$c .= '<table class="detalle"><tr><th style="width:10%">Versión</th><th style="width:16%">Fecha</th><th>Cambios</th></tr>';
$c .= '<tr><td>v3.0</td><td>Junio 2026</td><td>Versión final de alcance original: 16 casos de uso, 8 entidades de dominio.</td></tr>';
$c .= '<tr><td>v3.1</td><td>Junio 2026</td><td>Alineación con la implementación Laravel; corrección de numeración; CU-05 Eliminar tarea.</td></tr>';
$c .= '<tr><td><b>v4.0</b></td><td><b>Octubre 2026</b></td><td><b>Actualización por segunda iteración:</b> se incorporan Sprints, Novedades de proyecto, IA generativa real (informes de avance y resúmenes de sprint), Reportes consolidados con exportación, Notificaciones automáticas y Tablero con arrastre de tarjetas. Pasa de 16 a 25 casos de uso y de 8 a 10 entidades de dominio. Los casos de uso nuevos van de CU-17 a CU-25.</td></tr>';
$c .= '</table>';

$c .= '<div class="nota">Este documento <b>no reemplaza</b> a 01_CruzNegra_Analisis_v3.1.docx: lo <b>amplía</b>. Todo lo no modificado acá conserva vigencia (glosario base, escenario, actores, CU-01 a CU-16 y reglas de negocio RN-1 a RN-6).</div>';

// --- 2. Glosario: términos nuevos -------------------------------------------
$c .= '<h2 class="seccion">2 · Glosario — términos incorporados en v4.0</h2>';
$c .= '<table class="detalle"><tr><th style="width:24%">Término</th><th>Definición</th></tr>';
$glosario = [
    ['Sprint', 'Etapa de trabajo de un proyecto con rango de fechas (fecha_inicio / fecha_fin), estado (planificado, en curso, cerrado) y un conjunto de tareas. Agrupa las tareas del proyecto en la línea de tiempo del cliente.'],
    ['Resumen IA de sprint', 'Texto generado automáticamente a partir de las tareas del sprint. Se cachea: mientras exista, se reutiliza salvo regeneración forzada. El sistema también lo envía semanalmente al Jefe por los sprints activos.'],
    ['Novedad (Actualización de proyecto)', 'Nota corta que el equipo registra en la ficha del proyecto (avance, problema, decisión o próximo paso), con fecha y un interruptor visible_cliente que decide si el cliente la ve en su portal.'],
    ['Informe de avance IA', 'Documento (EntregableIA de tipo informe_avance, origen = ia) que la IA redacta con el contexto del proyecto. Nace siempre en borrador e invisible al cliente; solo el PO/Jefe/PM lo publica.'],
    ['Publicación / Retiro', 'Acción de control que pasa un informe IA a aprobado + visible al cliente, o lo retira (vuelve a revisado e invisible). Registra quién aprobó y cuándo.'],
    ['Tablero', 'Vista tipo Trello del trabajo: columnas pendiente / en progreso / completada / cancelada. Las tarjetas se arrastran y el sistema guarda estado y orden sin recargar la página.'],
    ['Reporte consolidado', 'Documento generado por el sistema que responde una pregunta del negocio (estado y avance, facturación y cobranzas) con filtros, totales y exportación.'],
    ['Código de emisión (REP-XXX-AAAA-NNN)', 'Identificador correlativo de cada emisión de reporte (módulo, año, número secuencial) que permite rastrearla. La numeración se lleva por módulo y año.'],
    ['Notificación', 'Aviso interno que el sistema deja a un usuario en la campana del header (ej.: hito por vencer). Los hitos sin completar que vencen dentro de 7 días se notifican al PM una vez por hito.'],
];
foreach ($glosario as [$t, $d]) {
    $c .= "<tr><td><b>{$t}</b></td><td>{$d}</td></tr>";
}
$c .= '</table>';

// --- 3. Casos de uso v4 ------------------------------------------------------
$c .= '<h2 class="seccion">3 · Modelo de casos de uso — incorporaciones</h2>';
$c .= '<p>Los 16 casos de uso de la v3.1 se mantienen sin cambios de nombre ni de actor. La segunda iteración agrega 9 casos de uso, agrupados así:</p>';
$c .= '<table class="detalle"><tr><th style="width:9%">ID</th><th style="width:30%">Caso de uso</th><th style="width:22%">Actor principal</th><th>Alcance</th></tr>';
$cus = [
    ['CU-17', 'Mover tarjeta en el tablero', 'Jefe · PM · PO', 'Arrastrar una tarjeta a otra columna o reordenarla dentro de la misma; el sistema guarda estado y orden de todas las columnas afectadas por AJAX (PATCH /tareas/mover). El Programador y el Cliente ven el tablero de solo lectura.'],
    ['CU-18', 'Registrar novedad del proyecto', 'Jefe · PM · PO · Programador', 'Cargar una actualización (avance, problema, decisión o próximo paso) en la ficha, con fecha y visibilidad al cliente opcional.'],
    ['CU-19', 'Generar informe de avance con IA', 'Jefe · PM · PO · Programador', 'Solicitar la redacción automática del informe del proyecto. El sistema arma el contexto (avance, tareas, hitos, novedades) y lo envía al proveedor de IA; el resultado nace en borrador, nunca se publica directo. Si falla, queda un entregable con el error registrado para reintentar.'],
    ['CU-20', 'Publicar / retirar informe para el Cliente', 'Jefe · PM · PO', 'Revisión humana del borrador: publicar lo aprueba (visible al cliente, registra aprobador y fecha) y retirar lo devuelve a revisado e invisible. No se puede publicar un informe que terminó con error.'],
    ['CU-21', 'Generar resumen IA de sprint', 'Jefe · PM · PO · Programador', 'Resumen automático del contenido de un sprint, cacheado por sprint; regenerable a pedido. Envío semanal automático de los sprints activos al Jefe.'],
    ['CU-22', 'Consultar reportes consolidados', 'Jefe · PM · PO', 'Sección Reportes con dos reportes: Estado y avance de proyectos (filtros por cliente, PM, estado y período; métricas por proyecto y totales) y Facturación y cobranzas (solo Jefe y PM).'],
    ['CU-23', 'Exportar reporte (CSV / PDF)', 'Jefe · PM · PO', 'Exportar el conjunto filtrado a CSV (abrible en Excel) o a PDF institucional con código de emisión REP-XXX-AAAA-NNN, identidad, alcance, criterio y paginación.'],
    ['CU-24', 'Descargar reporte del proyecto', 'Jefe · PM · PO · Cliente', 'PDF de seguimiento de un proyecto puntual desde su ficha. El cliente solo puede descargarlo de sus propios proyectos y el documento le llega con la información aprobada para su vista.'],
    ['CU-25', 'Recibir aviso de hito por vencer', 'PM (disparado por el sistema)', 'Tarea programada diaria a las 08:00: por cada hito sin completar que vence dentro de 7 días (o ya vencido) notifica a su PM, sin duplicar avisos del mismo hito.'],
];
foreach ($cus as [$id, $nombre, $actor, $alcance]) {
    $c .= "<tr><td><b>{$id}</b></td><td><b>{$nombre}</b></td><td>{$actor}</td><td>{$alcance}</td></tr>";
}
$c .= '</table>';
$c .= '<p style="font-size:9px; color:#6b7280;">Los detalles de flujo de CU-17 a CU-21 están en los diagramas de secuencia (documento 08); los de CU-22 a CU-24 en el documento 08 y en la propia sección Reportes.</p>';

// --- 4. Reglas de negocio nuevas ---------------------------------------------
$c .= '<h2 class="seccion">4 · Reglas de negocio incorporadas (RN-7 a RN-12)</h2>';
$c .= '<table class="detalle"><tr><th style="width:10%">Regla</th><th>Enunciado</th></tr>';
$rns = [
    ['RN-7', 'La IA nunca publica: todo material generado (informe de avance o resumen de sprint) nace en borrador y requiere una acción humana de publicación para llegar al cliente.'],
    ['RN-8', 'Un informe IA con error registrado no puede publicarse; solo regenerarse. La conservación del error es parte de la trazabilidad.'],
    ['RN-9', 'El cliente solo ve, de todo el material del proyecto: las novedades marcadas visible_cliente, los informes IA aprobados y publicados, hitos, sprints y solicitudes de su propio proyecto.'],
    ['RN-10', 'Cada emisión de reporte lleva código correlativo REP-XXX-AAAA-NNN por módulo y año, más usuario y fecha/hora de emisión; una página suelta del PDF debe ser autoexplicativa.'],
    ['RN-11', 'Un proyecto se reporta como RETRASADO cuando su fecha de fin estimada venció sin estar completado ni cancelado. El avance se calcula como tareas completadas sobre tareas totales.'],
    ['RN-12', 'El resumen IA de un sprint es cacheado: pedirlo de nuevo devuelve el mismo texto salvo regeneración explícita, para acotar costo y latencia del proveedor de IA.'],
];
foreach ($rns as [$r, $d]) {
    $c .= "<tr><td><b>{$r}</b></td><td>{$d}</td></tr>";
}
$c .= '</table>';

// --- 5. Modelo de dominio: incorporaciones -----------------------------------
$c .= '<h2 class="seccion">5 · Modelo de dominio — entidades y cambios</h2>';
$c .= '<p>El dominio pasa de 8 a 10 conceptos. Los diagramas completos están en el documento 07.</p>';
$c .= '<table class="detalle"><tr><th style="width:24%">Entidad</th><th>Tipo de cambio</th><th>Detalle</th></tr>';
$c .= '<tr><td><b>SPRINT</b></td><td>Nueva</td><td>id PK, proyecto_id FK, nombre, descripcion, estado {planificado|en_curso|cerrado}, fecha_inicio, fecha_fin, resumen_ia (texto IA cacheado). Relación: PROYECTO planifica SPRINT; SPRINT agrupa TAREA.</td></tr>';
$c .= '<tr><td><b>ACTUALIZACION_PROYECTO</b></td><td>Nueva</td><td>id PK, proyecto_id FK, creado_por FK (USUARIO), titulo, descripcion, tipo {avance|problema|decision|proximo_paso}, fecha, visible_cliente (booleano). Relación: PROYECTO registra ACTUALIZACION.</td></tr>';
$c .= '<tr><td><b>TAREA</b></td><td>Extendida</td><td>Suma sprint_id FK opcional (nullOnDelete: al borrar un sprint sus tareas no se pierden) y orden (entero para la posición en la columna del tablero).</td></tr>';
$c .= '<tr><td><b>ENTREGABLE_IA</b></td><td>Extendida</td><td>Suma: origen {manual|ia}, modelo_ia, version_prompt, contexto_fuente (JSON con los datos que vio la IA), visible_cliente, generado_en, aprobado_por FK, aprobado_en, mensaje_error. El tipo se amplía con informe_avance.</td></tr>';
$c .= '<tr><td><b>USUARIO</b></td><td>Extendida</td><td>Suma cliente_id FK opcional: vincula la cuenta del usuario Cliente con su empresa (CLIENTE). Es la base del filtrado visiblePara.</td></tr>';
$c .= '</table>';

// --- 6. Correspondencia con la implementación --------------------------------
$c .= '<h2 class="seccion">6 · Correspondencia con la implementación (actualiza sección 4.5 de v3.1)</h2>';
$c .= '<p>La pila declarada en v3.1 se mantiene (Laravel 12, Breeze, Spatie Permission, Laravel Auditing) y se concreta lo que quedaba planificado:</p>';
$c .= '<table class="detalle"><tr><th style="width:34%">Pendiente declarado en v3.1</th><th style="width:16%">Estado en v4.0</th><th>Implementación</th></tr>';
$c .= '<tr><td>Generación automática de IA</td><td><b>Implementado</b></td><td>Servicio ProjectReportService + proveedor intercambiable por config (fake para desarrollo, OpenRouter para producción). Informes de avance y resúmenes de sprint.</td></tr>';
$c .= '<tr><td>Reportes exportables a Excel</td><td><b>Implementado</b></td><td>Sección Reportes con exportación CSV (compatible Excel, con BOM UTF-8) y PDF institucional dompdf con códigos REP.</td></tr>';
$c .= '<tr><td>Notificaciones automáticas</td><td><b>Implementado</b></td><td>Notificaciones in-app Laravel (tabla notifications): aviso diario de hitos por vencer al PM (programado 08:00) y avisos en solicitudes de cambio; resumen IA de sprints activos al Jefe los lunes.</td></tr>';
$c .= '<tr><td>Importación Excel / campos económicos / estado "aprobada" de TAREA / revisión previa del PO en solicitudes</td><td>Sigue planificado</td><td>Fuera del alcance de esta iteración; se mantiene como trabajo futuro declarado.</td></tr>';
$c .= '</table>';
$c .= '<div class="nota"><b>Trazabilidad documental:</b> diagramas → documento 07 · diseño detallado de los flujos nuevos → documento 08 · modelo relacional y clases → documento 09 · verificación por casos de prueba → documento 10.</div>';

pdf(documento(
    'Análisis v4.0 — Actualización',
    'Cruz Negra — Sistema de Gestión Interna de Proyectos · Documento de Análisis (ampliación de la v3.1) · Octubre 2026',
    $c,
    'DOC-ANA-2026-006',
), '06_CruzNegra_Analisis_v4_Actualizacion.pdf');
