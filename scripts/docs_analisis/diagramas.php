<?php

// Diagramas del documento 07 (y fuentes de los PNG). Cada builder devuelve el
// HTML de la capa con coordenadas absolutas.

const COLOR_AZUL = '#2563eb';
const COLOR_VIOLETA = '#7c3aed';
const COLOR_NARANJA = '#d97706';
const COLOR_VERDE = '#008c63';
const COLOR_GRIS = '#64748b';

/** Diagrama de casos de uso v4.0: 5 actores, 25 CU, troncos por actor. */
function diagramaCasosUso(): array
{
    $d = '';
    $W = 1600.0; $H = 1100.0;

    // Límite del sistema
    $d .= caja(250, 40, 1150, 1010, '', 'border: 1.6px solid #374151;');
    $d .= '<div style="position:absolute;left:250px;top:44px;width:1150px;text-align:center;font-size:12px;font-weight:bold;color:#374151;">Sistema Cruz Negra (v4.0)</div>';

    // [id, texto, color, columna, filaY]
    $fondoAzul = '#eff6ff'; $fondoVioleta = '#f5f3ff'; $fondoNaranja = '#fffbeb'; $fondoVerde = '#f0fdf9';
    $izq = 300.0; $der = 1150.0; $centro = 700.0;
    $CU = [
        // PM (izquierda, arriba) — azul + IA naranja
        ['CU-01', 'Crear proyecto', COLOR_AZUL, $fondoAzul, $izq, 80],
        ['CU-02', 'Gestionar proyecto', COLOR_AZUL, $fondoAzul, $izq, 138],
        ['CU-03', 'Crear tarea', COLOR_AZUL, $fondoAzul, $izq, 196],
        ['CU-04', 'Editar tarea', COLOR_AZUL, $fondoAzul, $izq, 254],
        ['CU-05', 'Eliminar tarea', COLOR_AZUL, $fondoAzul, $izq, 312],
        ['CU-06', 'Generar Briefing / Línea de Tiempo', COLOR_NARANJA, $fondoNaranja, $izq, 370],
        // PO (izquierda, medio) — violeta + publicación naranja
        ['CU-14', 'Validar tarea completada', COLOR_VIOLETA, $fondoVioleta, $izq, 470],
        ['CU-15', 'Priorizar backlog', COLOR_VIOLETA, $fondoVioleta, $izq, 528],
        ['CU-16', 'Validar Entregable IA', COLOR_VIOLETA, $fondoVioleta, $izq, 586],
        ['CU-20', 'Publicar / retirar informe al Cliente', COLOR_NARANJA, $fondoNaranja, $izq, 644],
        // Programador (izquierda, abajo)
        ['CU-07', 'Ver tareas asignadas', COLOR_AZUL, $fondoAzul, $izq, 744],
        ['CU-08', 'Finalizar tarea', COLOR_AZUL, $fondoAzul, $izq, 802],
        ['CU-19', 'Generar informe de avance con IA', COLOR_NARANJA, $fondoNaranja, $izq, 860],
        ['CU-21', 'Generar resumen IA de sprint', COLOR_NARANJA, $fondoNaranja, $izq, 918],
        // Jefe (derecha, arriba) — verde facturación/control + tablero azul
        ['CU-09', 'Aprobar SolicitudCambio', COLOR_VERDE, $fondoVerde, $der, 80],
        ['CU-10', 'Emitir factura', COLOR_VERDE, $fondoVerde, $der, 138],
        ['CU-11', 'Consultar reportes', COLOR_VERDE, $fondoVerde, $der, 196],
        ['CU-22', 'Consultar reportes consolidados', COLOR_VERDE, $fondoVerde, $der, 254],
        ['CU-23', 'Exportar reporte (CSV / PDF)', COLOR_VERDE, $fondoVerde, $der, 312],
        ['CU-17', 'Mover tarjeta en el tablero', COLOR_AZUL, $fondoAzul, $der, 370],
        // Cliente (derecha, medio/abajo) — naranja trazabilidad
        ['CU-12', 'Solicitar cambio', COLOR_NARANJA, $fondoNaranja, $der, 470],
        ['CU-13', 'Consultar avance', COLOR_NARANJA, $fondoNaranja, $der, 528],
        ['CU-24', 'Descargar reporte del proyecto', COLOR_NARANJA, $fondoNaranja, $der, 586],
        // Compartidos (centro, abajo)
        ['CU-18', 'Registrar novedad del proyecto', COLOR_NARANJA, $fondoNaranja, $centro, 700],
        ['CU-25', 'Aviso de hito por vencer (automático)', COLOR_GRIS, '#f8fafc', $centro, 770],
    ];

    foreach ($CU as [$id, $texto, $color, $fondo, $col, $y]) {
        $d .= casoUso($col, $y, 200, 46, $id, $texto, $color, $fondo);
    }

    // Troncos y ramas por actor
    $ramas = function (float $xTronco, float $xActor, float $xElipse, array $ys, float $yActor, string $haciaDerecha) use (&$d) {
        $top = min($ys); $bot = max($ys);
        $d .= lineaV($xTronco, min($yActor, $top) - 8, max($yActor, $bot) + 8, 'solid', '#94a3b8', 1);
        $d .= lineaH($xActor + 36, $xTronco, $yActor + 26, 'solid', '#94a3b8', 1);
        foreach ($ys as $y) {
            $d .= lineaH($xTronco, $xElipse, $y + 23, 'solid', '#94a3b8', 1);
        }
    };

    // Izquierda: PM (6), PO (4), Programador (4) — el tronco une los tres bloques
    $d .= lineaV(228, 100, 940, 'solid', '#94a3b8', 1);
    $d .= lineaH(136, 228, 350, 'solid', '#94a3b8', 1);   // PM al tronco
    $d .= lineaH(136, 228, 620, 'solid', '#94a3b8', 1);   // PO al tronco
    $d .= lineaH(136, 228, 890, 'solid', '#94a3b8', 1);   // Programador al tronco
    foreach ([80, 138, 196, 254, 312, 370] as $y) $d .= lineaH(228, 300, $y + 23, 'solid', '#94a3b8', 1);
    foreach ([470, 528, 586, 644] as $y) $d .= lineaH(228, 300, $y + 23, 'solid', '#94a3b8', 1);
    foreach ([744, 802, 860, 918] as $y) $d .= lineaH(228, 300, $y + 23, 'solid', '#94a3b8', 1);

    // Derecha: Jefe (6), Cliente (3)
    $d .= lineaV(1428, 100, 610, 'solid', '#94a3b8', 1);
    $d .= lineaH(1420, 1428, 350, 'solid', '#94a3b8', 1);
    $d .= lineaH(1420, 1428, 600, 'solid', '#94a3b8', 1);
    foreach ([80, 138, 196, 254, 312, 370] as $y) $d .= lineaH(1350, 1428, $y + 23, 'solid', '#94a3b8', 1);
    foreach ([470, 528, 586] as $y) $d .= lineaH(1350, 1428, $y + 23, 'solid', '#94a3b8', 1);

    // Compartidos: CU-18 desde los dos troncos; CU-25 del sistema (sin actor)
    $d .= lineaH(228, 700, 723, 'solid', '#94a3b8', 1);
    $d .= lineaH(1350, 900, 723, 'solid', '#94a3b8', 1);
    $d .= lineaH(900, 700, 793, 'dashed', '#64748b', 1);
    $d .= etiqueta(740, 775, 'tarea programada 08:00', '#64748b');

    // Actores
    $d .= actor(100, 300, 'PM', COLOR_AZUL);
    $d .= actor(100, 570, 'PO', COLOR_VIOLETA);
    $d .= actor(100, 840, 'Programador', COLOR_GRIS);
    $d .= actor(1455, 300, 'Jefe (Admin)', COLOR_VERDE);
    $d .= actor(1455, 550, 'Cliente', COLOR_NARANJA);

    // Leyenda
    $leyenda = [
        [COLOR_AZUL, 'Gestión y ejecución'], [COLOR_VIOLETA, 'Validación del PO'],
        [COLOR_NARANJA, 'Trazabilidad e IA'], [COLOR_VERDE, 'Facturación y control'],
        [COLOR_GRIS, 'Automático (sistema)'],
    ];
    $lx = 300;
    foreach ($leyenda as [$color, $texto]) {
        $d .= "<div style=\"position:absolute;left:{$lx}px;top:985px;width:14px;height:14px;background:{$color};border-radius:3px;\"></div>";
        $d .= etiqueta($lx + 18, 987, $texto, '#374151');
        $lx += 230;
    }

    return ['html' => capa($W, $H, $d), 'w' => $W, 'h' => $H];
}

/** Modelo de dominio v4.0: 10 entidades y sus asociaciones. */
function diagramaDominio(): array
{
    $d = '';
    $W = 1600.0; $H = 1150.0;

    [$eCliente, ]      = entidadAlto(180, 80, 175, 'CLIENTE', ['id : PK', 'nombre', 'apellido', 'email : UK', 'telefono', 'empresa', 'estado']);
    [$eUsuario, ]      = entidadAlto(1200, 80, 195, 'USUARIO', ['id : PK', 'name / apellido', 'email : UK', 'password', 'estado', 'cliente_id : FK (su empresa)', 'roles via Spatie']);
    [$eProyecto, ]     = entidadAlto(700, 330, 195, 'PROYECTO', ['id : PK', 'cliente_id : FK', 'pm_id : FK', 'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin_estimada', 'estado']);
    [$eTarea, ]        = entidadAlto(180, 560, 205, 'TAREA', ['id : PK', 'proyecto_id : FK', 'sprint_id : FK (opcional)', 'asignado_a : FK', 'solicitud_cambio_id : FK', 'titulo / descripcion', 'estado / prioridad', 'fecha_limite / orden']);
    [$eHito, ]         = entidadAlto(180, 340, 160, 'HITO', ['id : PK', 'proyecto_id : FK', 'nombre / descripcion', 'fecha_objetivo', 'completado']);
    [$eSprint, ]       = entidadAlto(700, 570, 185, 'SPRINT', ['id : PK', 'proyecto_id : FK', 'nombre / descripcion', 'estado', 'fecha_inicio / fecha_fin', 'resumen_ia']);
    [$eEntregable, ]   = entidadAlto(1200, 360, 235, 'ENTREGABLE_IA', ['id : PK', 'proyecto_id : FK', 'generado_por : FK', 'aprobado_por : FK', 'tipo / origen / modelo_ia', 'contexto_fuente : JSON', 'titulo / contenido', 'estado / visible_cliente', 'generado_en / aprobado_en', 'mensaje_error']);
    [$eSolicitud, ]    = entidadAlto(430, 830, 215, 'SOLICITUD_CAMBIO', ['id : PK', 'proyecto_id : FK', 'solicitado_por : FK', 'titulo / descripcion', 'estado / prioridad']);
    [$eFactura, ]      = entidadAlto(1200, 680, 195, 'FACTURA', ['id : PK', 'proyecto_id : FK', 'emitida_por : FK (Jefe)', 'numero : UK / monto', 'detalle', 'fecha_emision', 'fecha_vencimiento', 'estado']);
    [$eActualizacion, ] = entidadAlto(720, 850, 235, 'ACTUALIZACION_PROYECTO', ['id : PK', 'proyecto_id : FK', 'creado_por : FK', 'titulo / descripcion', 'tipo / fecha', 'visible_cliente']);

    foreach ([$eCliente, $eUsuario, $eProyecto, $eTarea, $eHito, $eSprint, $eEntregable, $eSolicitud, $eFactura, $eActualizacion] as $e) {
        $d .= $e;
    }

    // Asociaciones (las cajas están en posiciones fijas, mismos anchors)
    $asoc = [
        // [[puntos], etiqueta, x_etiqueta, y_etiqueta]
        [[[355, 140], [620, 140], [620, 385], [700, 385]], 'tiene 1 — *', 430, 122],
        [[[1200, 140], [1040, 140], [1040, 390], [895, 390]], 'lidera (PM) 1 — *', 1010, 122],
        [[[1297, 224], [1297, 290], [70, 290], [70, 645], [180, 645]], 'ejecuta 1 — *', 320, 272],
        [[[700, 425], [340, 425], [340, 400]], 'marca 1 — *', 470, 407],
        [[[700, 445], [282, 445], [282, 560]], 'contiene 1 — *', 430, 428],
        [[[797, 492], [797, 570]], 'planifica 1 — *', 810, 522],
        [[[700, 620], [385, 620]], 'agrupa 1 — *', 480, 602],
        [[[700, 465], [640, 465], [640, 500], [537, 500], [537, 830]], 'recibe 1 — *', 648, 492],
        [[[430, 880], [282, 880], [282, 760]], 'dispara 1 — *', 300, 800],
        [[[895, 375], [1030, 375], [1030, 425], [1200, 425]], 'genera 1 — *', 960, 357],
        [[[895, 465], [1000, 465], [1000, 640], [1297, 640], [1297, 680]], 'factura 1 — *', 1010, 447],
        [[[265, 224], [265, 265], [130, 265], [130, 880], [430, 880]], 'solicita 1 — *', 150, 560],
        [[[955, 905], [1450, 905], [1450, 145], [1395, 145]], 'registra (equipo) * — 1', 1250, 887],
    ];
    foreach ($asoc as [$puntos, $texto, $ex, $ey]) {
        $d .= conex($puntos);
        $d .= etiqueta($ex, $ey, $texto, '#374151');
    }

    // Aviso de tablas técnicas
    $d .= "<div style=\"position:absolute;left:180px;top:1000px;width:740px;\" class=\"nota\">Tablas técnicas fuera del dominio de negocio: <b>audits</b> (Laravel Auditing), <b>notifications</b> (avisos in-app) y <b>model_has_roles</b> (roles Spatie sobre USUARIO).</div>";

    return ['html' => capa($W, $H, $d), 'w' => $W, 'h' => $H];
}

/** Modelo relacional v4.0: las 10 tablas con sus columnas reales. */
function diagramaRelacional(): array
{
    $d = '';
    $W = 1600.0; $H = 1150.0;

    $c = '#008c63';
    [$tClientes, ] = entidadAlto(80, 90, 200, 'clientes', ['id : int PK', 'nombre / apellido', 'email : UK', 'telefono', 'empresa', 'estado', 'created_at / updated_at'], $c);
    [$tUsers, ] = entidadAlto(1180, 90, 230, 'users', ['id : int PK', 'name / apellido', 'email : UK', 'password / estado', 'cliente_id : FK clientes (null)', 'roles: model_has_roles', 'created_at / updated_at'], $c);
    [$tProyectos, ] = entidadAlto(620, 80, 230, 'proyectos', ['id : int PK', 'cliente_id : FK CASCADE', 'pm_id : FK users CASCADE', 'nombre / descripcion', 'fecha_inicio / fecha_fin_estimada', 'estado', 'created_at / updated_at'], $c);
    [$tHitos, ] = entidadAlto(80, 420, 210, 'hitos', ['id : int PK', 'proyecto_id : FK CASCADE', 'nombre / descripcion', 'fecha_objetivo', 'completado : bool', 'created_at / updated_at'], $c);
    [$tSprints, ] = entidadAlto(620, 350, 230, 'sprints', ['id : int PK', 'proyecto_id : FK CASCADE', 'nombre / descripcion', 'estado (planificado…)', 'fecha_inicio / fecha_fin', 'resumen_ia : mediumText', 'created_at / updated_at'], $c);
    [$tEntregables, ] = entidadAlto(1140, 400, 280, 'entregables_ia', ['id : int PK', 'proyecto_id : FK CASCADE', 'generado_por / aprobado_por : FK', 'tipo / origen (manual|ia)', 'modelo_ia / version_prompt', 'contexto_fuente : JSON', 'titulo / contenido', 'estado / visible_cliente', 'generado_en / aprobado_en', 'mensaje_error', 'created_at / updated_at'], $c);
    [$tTareas, ] = entidadAlto(80, 700, 260, 'tareas', ['id : int PK', 'proyecto_id : FK CASCADE', 'sprint_id : FK sprints SET NULL', 'asignado_a : FK users CASCADE', 'solicitud_cambio_id : FK SET NULL', 'titulo / descripcion', 'estado / prioridad', 'fecha_limite / orden', 'created_at / updated_at'], $c);
    [$tSolicitudes, ] = entidadAlto(620, 660, 240, 'solicitudes_cambio', ['id : int PK', 'proyecto_id : FK CASCADE', 'solicitado_por : FK users', 'titulo / descripcion', 'estado / prioridad', 'created_at / updated_at'], $c);
    [$tFacturas, ] = entidadAlto(1140, 750, 250, 'facturas', ['id : int PK', 'proyecto_id : FK CASCADE', 'emitida_por : FK users', 'numero : UK / monto : decimal', 'detalle / fecha_emision', 'fecha_vencimiento / estado', 'created_at / updated_at'], $c);
    [$tActualizaciones, ] = entidadAlto(620, 920, 270, 'actualizaciones_proyecto', ['id : int PK', 'proyecto_id : FK CASCADE', 'creado_por : FK users CASCADE', 'titulo / descripcion', 'tipo / fecha / visible_cliente', 'created_at / updated_at'], $c);

    foreach ([$tClientes, $tUsers, $tProyectos, $tHitos, $tSprints, $tEntregables, $tTareas, $tSolicitudes, $tFacturas, $tActualizaciones] as $t) {
        $d .= $t;
    }

    // FKs (extremos apoyados en los bordes de cada tabla; diagonales por zonas libres)
    $d .= conex([[620, 110], [280, 110]]);                              // proyectos.cliente_id -> clientes
    $d .= conex([[850, 120], [1180, 120]]);                             // proyectos.pm_id -> users
    $d .= conex([[1295, 90], [1295, 55], [180, 55], [180, 90]]);        // users.cliente_id -> clientes
    $d .= conex([[290, 445], [455, 445]]).diagonal(455, 445, 620, 195); // hitos.proyecto_id -> proyectos
    $d .= conex([[735, 350], [735, 190]]);                              // sprints.proyecto_id -> proyectos
    $d .= diagonal(1140, 440, 850, 170);                                // entregables.proyecto_id -> proyectos
    $d .= diagonal(330, 700, 620, 190);                                 // tareas.proyecto_id -> proyectos
    $d .= diagonal(340, 730, 620, 430);                                 // tareas.sprint_id -> sprints (SET NULL)
    $d .= conex([[340, 770], [1000, 770], [1000, 180], [1180, 180]]);   // tareas.asignado_a -> users
    $d .= diagonal(340, 800, 620, 709);                                 // tareas.solicitud_cambio_id (SET NULL)
    $d .= conex([[860, 690], [1000, 690], [1000, 185], [850, 185]]);    // solicitudes.proyecto_id -> proyectos
    $d .= conex([[1140, 780], [1100, 780], [1100, 240], [800, 240], [800, 190]]); // facturas.proyecto_id -> proyectos
    $d .= conex([[620, 950], [60, 950], [60, 165], [620, 165]]);        // actualizaciones.proyecto_id -> proyectos

    $d .= "<div style=\"position:absolute;left:80px;top:1075px;width:1020px;\" class=\"nota\">Regla de borrado: <b>CASCADE</b> en todas las FK de proyecto salvo <b>tareas.solicitud_cambio_id</b> y <b>tareas.sprint_id</b> (SET NULL: borrar una solicitud o un sprint no pierde las tareas). Se dibujan las FK hacia clientes y proyectos; las FK hacia users (asignado_a, generado_por, aprobado_por, emitida_por, solicitado_por, creado_por) se omiten por claridad. Tablas técnicas: audits · notifications · model_has_roles.</div>";

    return ['html' => capa($W, $H, $d), 'w' => $W, 'h' => $H];
}

/** Diagrama de clases Eloquent v4.0: 10 modelos y sus relaciones reales. */
function diagramaClases(): array
{
    $d = '';
    $W = 1600.0; $H = 1150.0;

    $clase = function (float $x, float $y, float $w, string $nombre, array $metodos) {
        $filas = '';
        foreach ($metodos as $m) {
            $filas .= "<div style=\"padding:1.5px 7px;font-size:7.8px;color:#374151;border-top:0.5px solid #f1f5f9;\">+ {$m}</div>";
        }
        $alto = 20 + count($metodos) * 12.5 + 5;
        $html = "<div style=\"background:#374151;color:#fff;font-size:9px;font-weight:bold;text-align:center;padding:3px 4px;\">{$nombre}</div>{$filas}";

        return caja($x, $y, $w, $alto, $html, 'border:1.2px solid #374151;background:#fff;');
    };

    $d .= $clase(80, 90, 220, 'Cliente', ['proyectos(): HasMany', 'usuarios(): HasMany']);
    $d .= $clase(1180, 90, 280, 'User (Notifiable)', ['proyectosComoPm(): HasMany', 'tareasAsignadas(): HasMany', 'solicitudesRealizadas(): HasMany', 'entregablesGenerados(): HasMany', 'facturasEmitidas(): HasMany', 'esCliente(): bool', 'puedeVer($modelo): bool', 'asignables() : scope estático']);
    $d .= $clase(600, 80, 300, 'Proyecto (Auditable)', ['cliente(): BelongsTo', 'pm(): BelongsTo', 'tareas() / sprints(): HasMany', 'hitos(): HasMany', 'entregablesIa(): HasMany', 'facturas(): HasMany', 'actualizaciones(): HasMany', 'solicitudesCambio(): HasMany', 'scopeVisiblePara($usuario)']);
    $d .= $clase(80, 420, 240, 'Hito', ['proyecto(): BelongsTo']);
    $d .= $clase(600, 400, 260, 'Sprint', ['proyecto(): BelongsTo', 'tareas(): HasMany', 'scopeVisiblePara($usuario)']);
    $d .= $clase(1180, 430, 300, 'EntregableIA', ['proyecto(): BelongsTo', 'generador(): BelongsTo', 'aprobador(): BelongsTo', 'scopeVisiblePara($usuario)']);
    $d .= $clase(80, 660, 300, 'Tarea', ['proyecto(): BelongsTo', 'asignado(): BelongsTo', 'sprint(): BelongsTo', 'solicitudCambio(): BelongsTo', 'scopeVisiblePara($usuario)']);
    $d .= $clase(600, 680, 280, 'SolicitudCambio', ['proyecto(): BelongsTo', 'solicitante(): BelongsTo', 'tareas(): HasMany']);
    $d .= $clase(1180, 740, 280, 'Factura', ['proyecto(): BelongsTo', 'emisor(): BelongsTo', 'scopeVisiblePara($usuario)']);
    $d .= $clase(600, 900, 300, 'ActualizacionProyecto', ['proyecto(): BelongsTo', 'autor(): BelongsTo']);

    // Relaciones (geometría exacta sobre las cajas de clases)
    $d .= conex([[600, 120], [300, 120]]);                              // Proyecto -> Cliente
    $d .= conex([[900, 120], [1180, 120]]);                             // Proyecto -> User (pm)
    $d .= conex([[1320, 90], [1320, 55], [190, 55], [190, 90]]);        // User -> Cliente (usuarios)
    $d .= conex([[600, 140], [460, 140], [460, 441], [320, 441]]);      // Proyecto -> Hito
    $d .= conex([[730, 237], [730, 400]]);                              // Proyecto -> Sprint
    $d .= conex([[900, 160], [1040, 160], [1040, 470], [1180, 470]]);   // Proyecto -> EntregableIA
    $d .= conex([[900, 180], [960, 180], [960, 650], [740, 650], [740, 680]]); // Proyecto -> SolicitudCambio
    $d .= conex([[600, 200], [500, 200], [500, 706], [380, 706]]);      // Proyecto -> Tarea
    $d .= conex([[900, 220], [1000, 220], [1000, 620], [1120, 620], [1120, 773], [1180, 773]]); // Proyecto -> Factura
    $d .= conex([[600, 230], [540, 230], [540, 870], [750, 870], [750, 900]]); // Proyecto -> Actualizacion
    $d .= conex([[600, 433], [430, 433], [430, 660]]);                  // Sprint -> Tarea
    $d .= conex([[600, 713], [380, 713]]);                              // SolicitudCambio -> Tarea
    $d .= conex([[1320, 235], [1500, 235], [1500, 850], [360, 850], [360, 752]]); // User -> Tarea (asignado)

    $d .= "<div style=\"position:absolute;left:80px;top:1075px;width:1020px;\" class=\"nota\">Cada FK del modelo relacional tiene su <b>belongsTo</b> en la clase hija y su <b>hasMany</b> inverso en la clase padre · 10 modelos Eloquent · los scopes <b>visiblePara</b> concentran el filtrado por rol (el Cliente solo ve lo de su empresa). Se dibujan las relaciones por proyecto; las que apuntan a User desde otras clases se omiten por claridad.</div>";

    return ['html' => capa($W, $H, $d), 'w' => $W, 'h' => $H];
}
