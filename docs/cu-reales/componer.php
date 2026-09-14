<?php
/**
 * Compone los storyboards de los CU Reales como PNG (sin navegador).
 * Uso: php docs/cu-reales/componer.php
 */

$CAP = __DIR__ . '/capturas';
$OUT = __DIR__ . '/png';
@mkdir($OUT, 0777, true);

const ANCHO_IMG = 760;
const MARGEN = 30;
const TEXTO_W = 300;
const FONDO = [244, 245, 247];
const BLANCO = [255, 255, 255];
const INDIGO = [79, 70, 229];
const GRIS = [75, 85, 99];
const OSCURO = [17, 24, 39];

$cus = [
    'cu1' => [
        'titulo' => 'CU Real 1 - El PM crea un proyecto',
        'meta' => 'DSD 01 - Usuario: Laura (PM)',
        'pasos' => [
            ['create()', 'El PM abre el formulario desde el listado. El controller carga clientes y usuarios para los desplegables.', 'cu1_paso1_listado.png'],
            ['Formulario del modal', 'El sistema muestra el modal Nuevo Proyecto en lugar de una pagina aparte (diferencia con el DSD original).', 'cu1_paso2_modal.png'],
            ['store(req : Request)', 'Completa nombre, descripcion, fechas, estado, cliente_id y pm_id, y envia. El controller valida el array de datos.', 'cu1_paso3_completo.png'],
            ['Proyecto::create(data)', 'Validacion OK: se crea el proyecto y redirect al listado con el mensaje success "Proyecto creado correctamente."', 'cu1_paso4_guardado.png'],
        ],
    ],
    'cu2' => [
        'titulo' => 'CU Real 2 - El Programador consulta las tareas',
        'meta' => 'DSD 02 - Usuario: Sofia (Programador)',
        'pasos' => [
            ['index()', 'Listado de tareas con sus relaciones proyecto y asignado cargadas (with). El Programador solo tiene accion de lectura.', 'cu2_paso1_listado.png'],
            ['when(estado)', 'Filtro por estado Pendiente sobre el scope visiblePara; la consulta aplica busqueda y paginacion (15 por pagina).', 'cu2_paso2_filtro.png'],
            ['show(t : Tarea)', 'Route Model Binding: la tarea llega por id. Se cargan proyecto, asignado y solicitudCambio y se muestran en la ficha.', 'cu2_paso3_detalle.png'],
        ],
    ],
    'cu3' => [
        'titulo' => 'CU Real 3 - Edicion de tarea desde el tablero',
        'meta' => 'DSD 03 - Usuario: Laura (PM). Solo Jefe, PM y PO mueven tarjetas; el Programador ve el tablero en modo lectura.',
        'pasos' => [
            ['Clic en la tarjeta', 'El modal Editar tarea toma los datos del atributo data-tarea (JSON en la tarjeta) y completa el formulario.', 'cu3_paso1_modal.png'],
            ['update(req, t)', 'Cambia el estado a En progreso y envia PATCH /tareas/{id} con JSON (Accept: application/json).', 'cu3_paso2_estado.png'],
            ['200 JSON + tablero actualizado', 'La respuesta trae la tarea con sus relaciones; el JS mueve la tarjeta a la columna En progreso y actualiza los contadores, sin recargar.', 'cu3_paso3_movida.png'],
        ],
    ],
    'cu4' => [
        'titulo' => 'CU Real 4 - El PM elimina una tarea',
        'meta' => 'DSD 04 - Usuario: Laura (PM)',
        'pasos' => [
            ['Tablero de tareas', 'El PM abre la tarjeta a eliminar. Cada tarjeta expone su JSON completo en data-tarea.', 'cu4_paso1_tablero.png'],
            ['destroy(t : Tarea)', 'Boton Eliminar dentro del modal de edicion: dispara DELETE /tareas/{id} por AJAX.', 'cu4_paso2_modal.png'],
            ['Confirmacion propia', 'Modal de confirmacion del sistema (no confirm() nativo): "Eliminar esta tarea? Esta accion no se puede deshacer."', 'cu4_paso3_confirmar.png'],
            ['delete() : bool', 'OK: 200 {ok:true}. La tarjeta desaparece y el contador de Pendiente baja de 2 a 1 sin recargar.', 'cu4_paso4_eliminada.png'],
        ],
    ],
    'cu5' => [
        'titulo' => 'CU Real 5 - El Jefe aprueba una solicitud de cambio',
        'meta' => 'DSD 05 - Usuario: Roberto (Jefe)',
        'pasos' => [
            ['index()', 'El Jefe revisa las solicitudes con su proyecto, solicitante, estado y prioridad.', 'cu5_paso1_listado.png'],
            ['update(req, s)', 'Abre el modal de edicion y fija estado = "aprobada" (enum: pendiente | aprobada | rechazada).', 'cu5_paso2_aprobada.png'],
            ['fill(data) + save()', 'Guardado OK: redirect back() al listado con el flash "Solicitud de cambio actualizada correctamente."; la fila pasa a Aprobada.', 'cu5_paso3_guardada.png'],
        ],
    ],
    'cu6' => [
        'titulo' => 'CU Real 6 - El PO gestiona los entregables IA',
        'meta' => 'DSD 06 - Usuario: Diego (PO)',
        'pasos' => [
            ['index()', 'Listado de entregables con proyecto, tipo, estado y generado por (LengthAwarePaginator).', 'cu6_paso1_listado.png'],
            ['update(req, e)', 'Modal de edicion: el estado solo acepta borrador | revisado | aprobado; se fija Aprobado.', 'cu6_paso2_aprobado.png'],
            ['save() : bool', 'Guardado OK: el listado muestra "Borrador de manual de usuario" como Aprobado.', 'cu6_paso3_guardado.png'],
        ],
    ],
];

$fuente = 'C:/Windows/Fonts/arial.ttf';
$fuenteBold = 'C:/Windows/Fonts/arialbd.ttf';

function texto($im, $txt, $x, $y, $size, $color, $bold = false, $anchoMax = null) {
    global $fuente, $fuenteBold;
    $font = $bold ? $fuenteBold : $fuente;
    if ($anchoMax === null) {
        imagettftext($im, $size, 0, $x, $y, $color, $font, $txt);
        return;
    }
    // corte simple por palabras segun ancho
    $linea = '';
    $yy = $y;
    foreach (preg_split('/\s+/', $txt) as $palabra) {
        $prueba = trim($linea . ' ' . $palabra);
        $caja = imagettfbbox($size, 0, $font, $prueba);
        if (($caja[2] - $caja[0]) > $anchoMax && $linea !== '') {
            imagettftext($im, $size, 0, $x, $yy, $color, $font, $linea);
            $yy += (int) ($size * 1.45);
            $linea = $palabra;
        } else {
            $linea = $prueba;
        }
    }
    if ($linea !== '') imagettftext($im, $size, 0, $x, $yy, $color, $font, $linea);
}

foreach ($cus as $cu => $info) {
    $imgs = [];
    $altoPasos = 0;
    foreach ($info['pasos'] as $p) {
        $src = imagecreatefrompng("$CAP/{$p[2]}");
        $imgs[] = imagescale($src, ANCHO_IMG, -1, IMG_BICUBIC);
        imagedestroy($src);
    }

    $cabecera = 110;
    $gap = 26;
    $altoTotal = $cabecera;
    foreach ($imgs as $im) { $altoTotal += imagesy($im) + $gap; }
    $altoTotal += 10;

    $lienzo = imagecreatetruecolor(MARGEN * 2 + ANCHO_IMG + 120 + TEXTO_W, $altoTotal);
    $bg = imagecolorallocate($lienzo, ...FONDO);
    imagefill($lienzo, 0, 0, $bg);
    $indigo = imagecolorallocate($lienzo, ...INDIGO);
    $gris = imagecolorallocate($lienzo, ...GRIS);
    $oscuro = imagecolorallocate($lienzo, ...OSCURO);
    $blanco = imagecolorallocate($lienzo, ...BLANCO);

    texto($lienzo, $info['titulo'], MARGEN, 48, 24, $oscuro, true);
    texto($lienzo, $info['meta'] . '   (capturas de localhost:8000, septiembre 2026)', MARGEN, 80, 13, $gris);

    $y = $cabecera;
    $n = 1;
    foreach ($imgs as $im) {
        // circulo numerado
        $cx = MARGEN + 26;
        $cy = $y + 26;
        imagefilledellipse($lienzo, $cx, $cy, 52, 52, $indigo);
        $caja = imagettfbbox(24, 0, $fuenteBold, (string) $n);
        $tw = $caja[2] - $caja[0];
        $th = $caja[1] - $caja[7];
        imagettftext($lienzo, 24, 0, (int) ($cx - $tw / 2), (int) ($cy + $th / 2), $blanco, $fuenteBold, (string) $n);

        $ix = MARGEN + 70;
        imagecopy($lienzo, $im, $ix, $y, 0, 0, imagesx($im), imagesy($im));

        $tx = $ix + ANCHO_IMG + 24;
        $titulo = $info['pasos'][$n - 1][0];
        $desc = $info['pasos'][$n - 1][1];
        texto($lienzo, $titulo, $tx, $y + 20, 14, $oscuro, true, TEXTO_W);
        texto($lienzo, $desc, $tx, $y + 50, 12, $gris, false, TEXTO_W);

        $y += imagesy($im) + $gap;
        $n++;
    }

    imagepng($lienzo, "$OUT/{$cu}_pasos.png", 8);
    echo "$cu ok (" . number_format(filesize("$OUT/{$cu}_pasos.png")) . " bytes)\n";
    imagedestroy($lienzo);
    foreach ($imgs as $im) imagedestroy($im);
}
