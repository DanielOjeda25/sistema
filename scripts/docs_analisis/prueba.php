<?php

// Página de prueba de las primitivas de diagrama antes de generar los docs.

$d = '';
$d .= actor(30, 30, 'PM');
$d .= actor(30, 130, 'PO');
$d .= casoUso(120, 30, 150, 46, 'CU-01', 'Crear proyecto');
$d .= casoUso(120, 110, 150, 46, 'CU-14', 'Validar tarea completada', '#7c3aed', '#f5f3ff');
$d .= conex([[66, 53], [120, 53]]);
$d .= conex([[66, 153], [140, 153], [140, 133], [120, 133]]);
$d .= etiqueta(78, 42, 'incluye');

[$ent, ] = entidadAlto(320, 30, 170, 'PROYECTO', ['id : PK', 'cliente_id : FK', 'nombre', 'estado']);
$d .= $ent;

// DSD en miniatura: dos lifelines y flechas
$d .= lineaV(140, 240, 420, 'dashed');
$d .= lineaV(420, 240, 420, 'dashed');
$d .= caja(100, 215, 84, 24, '<div style="text-align:center; font-size:8.6px; font-weight:bold; padding-top:4px;">:Usuario</div>', 'border: 1.2px solid #374151; background:#ffffff;');
$d .= caja(380, 215, 84, 24, '<div style="text-align:center; font-size:8.6px; font-weight:bold; padding-top:4px;">:Controller</div>', 'border: 1.2px solid #374151; background:#ffffff;');
$d .= flechaH(140, 420, 270, 'store(req: Request)');
$d .= flechaH(420, 140, 330, 'redirect() + success', 'dashed', 'izq');

pdf(documento(
    'Prueba de primitivas',
    'Verificación de diagramas antes de generar la documentación',
    capa(700, 440, $d),
    'DOC-PRU-2026-000',
), 'zz_prueba.pdf');
