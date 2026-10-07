<?php

/*
 |-----------------------------------------------------------------------------
 | GENERADOR DE DOCUMENTACIÓN DE ANÁLISIS (expediente académico CRUZNEGRA)
 |-----------------------------------------------------------------------------
 | Produce los PDFs de documentación (análisis, diagramas, DSD, modelo de
 | datos, casos de prueba) con el mismo dompdf y la misma identidad visual
 | que los reportes del sistema.
 |
 | Uso:  php scripts/docs_analisis/generar.php [doc]   (sin argumento: todos)
*/

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

define('DESTINO', 'C:/Users/dani_/OneDrive/Desktop/facu/02_Analisis_y_Diseno/ANALISIS');
define('ASSETS', __DIR__.'/assets');

// -----------------------------------------------------------------------------
// PDF base
// -----------------------------------------------------------------------------

function pdf(string $html, string $archivo, string $orientacion = 'portrait'): void
{
    $pdf = app('dompdf.wrapper')->loadHtml($html)->setPaper('a4', $orientacion);
    $pdf->save(DESTINO.'/'.$archivo);
    echo "OK  {$archivo}\n";
}

/**
 * Envoltorio común: cabecera institucional repetida + pie con página X de Y.
 * $cuerpo es HTML del contenido; $margenSup deja lugar a la cabecera fija.
 */
function documento(string $titulo, string $subtitulo, string $cuerpo, string $codigo, string $orientacion = 'portrait', string $cssAdicional = ''): string
{
    $ancho = $orientacion === 'landscape' ? '1060px' : '722px';

    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>{$titulo}</title>
<style>
    @page {
        margin: 118px 36px 52px 36px;
        @bottom-left { content: "CRUZNEGRA · Documentación de Análisis y Diseño"; font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #9ca3af; }
        @bottom-right { content: "{$codigo} · Página " counter(page) " de " counter(pages); font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #6b7280; font-weight: bold; }
    }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #374151; }
    h1 { font-size: 19px; text-transform: uppercase; letter-spacing: 0.5px; color: #374151; margin: 0; }
    h2.seccion { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #008c63; margin: 16px 0 5px; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; }
    h3 { font-size: 10.5px; color: #374151; margin: 12px 0 4px; }
    p { margin: 0 0 6px; line-height: 1.5; }
    table.detalle { width: 100%; border-collapse: collapse; }
    table.detalle th { background: #008c63; color: #ffffff; border: 1px solid #e5e7eb; padding: 4px 6px; font-size: 8px; text-transform: uppercase; text-align: left; letter-spacing: 0.5px; }
    table.detalle td { border: 1px solid #e5e7eb; padding: 4px 6px; vertical-align: top; }
    table.detalle tr:nth-child(even) td { background: #f8fafc; }
    .pildora { display: inline-block; padding: 1px 7px; border-radius: 8px; font-size: 8px; font-weight: bold; }
    .nota { border-left: 3px solid #008c63; background: #f8fafc; padding: 7px 10px; margin: 8px 0; font-size: 9.5px; color: #374151; }
    {$cssAdicional}
</style>
</head>
<body>
    <div style="position: fixed; top: -100px; left: -36px; right: -36px; background: #ffffff; padding: 16px 36px 9px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: middle; width: 62%;">
                    <table style="border-collapse: collapse;"><tr>
                        <td style="padding-right: 10px;"><img src="{ASSETS}/cruznegra-logo.png" style="height: 40px;"></td>
                        <td style="vertical-align: middle;">
                            <span style="font-size: 17px; font-weight: bold; color: #374151; letter-spacing: 1px;">CRUZNEGRA</span><br>
                            <span style="font-size: 8.5px; color: #008c63;">Sistema de Gestión Interna · Documentación técnica</span>
                        </td>
                    </tr></table>
                </td>
                <td style="vertical-align: middle; text-align: right; font-size: 8.5px; color: #6b7280; line-height: 1.5;">
                    Tecnicatura Superior en Desarrollo de Software<br>
                    Análisis y Diseño de Sistemas · ISRG<br>
                    Ojeda · Cunha · Cáceres · Morinigo
                </td>
            </tr>
        </table>
        <div style="border-bottom: 2px solid #00b87d; margin-top: 9px;"></div>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 2px;">
        <tr>
            <td style="vertical-align: bottom;"><h1>{$titulo}</h1></td>
            <td style="vertical-align: bottom; text-align: right;"><span style="font-size: 10.5px; font-weight: bold; color: #6b7280;">{$codigo}</span></td>
        </tr>
    </table>
    <p style="margin: 0 0 10px; font-size: 9.5px; color: #6b7280;">{$subtitulo}</p>

    {$cuerpo}
</body>
</html>
HTML;
}

// -----------------------------------------------------------------------------
// PRIMITIVAS DE DIAGRAMA (posiciones absolutas en px sobre una capa)
// -----------------------------------------------------------------------------

/** Capa contenedora de un diagrama con coordenadas absolutas. */
function capa(float $ancho, float $alto, string $interno): string
{
    $ancho = round($ancho);
    $alto = round($alto);

    return "<div style=\"position: relative; width: {$ancho}px; height: {$alto}px; margin: 4px 0;\">{$interno}</div>";
}

/** Caja genérica con contenido HTML centrado. */
function caja(float $x, float $y, float $w, float $h, string $html, string $extra = ''): string
{
    $x = round($x);
    $y = round($y);
    $w = round($w);
    $h = round($h);

    return "<div style=\"position: absolute; left: {$x}px; top: {$y}px; width: {$w}px; height: {$h}px; overflow: hidden; {$extra}\">{$html}</div>";
}

/** Caso de uso: elipse con etiqueta. */
function casoUso(float $x, float $y, float $w, float $h, string $id, string $texto, string $borde = '#008c63', string $fondo = '#f0fdf9'): string
{
    $html = "<div style=\"display: table; width: 100%; height: 100%;\"><div style=\"display: table-cell; vertical-align: middle; text-align: center; padding: 0 11%;\"><b style=\"color: {$borde}; font-size: 7.5px;\">{$id}</b><br><span style=\"font-size: 8px; color: #374151;\">{$texto}</span></div></div>";

    return caja($x, $y, $w, $h, $html, "border: 1.4px solid {$borde}; border-radius: 50%; background: {$fondo};");
}

/** Actor: monigote ortogonal (cabeza + torso + brazos + piernas) y nombre. */
function actor(float $x, float $y, string $nombre, string $color = '#374151'): string
{
    $c = $color;
    $cabeza = "<div style=\"position: absolute; left: 11px; top: 0; width: 14px; height: 14px; border: 1.6px solid {$c}; border-radius: 50%;\"></div>";
    $cuerpo = "<div style=\"position: absolute; left: 17px; top: 15px; width: 2px; height: 16px; background: {$c};\"></div>";
    $brazos = "<div style=\"position: absolute; left: 6px; top: 20px; width: 24px; height: 2px; background: {$c};\"></div>";
    $piernas = "<div style=\"position: absolute; left: 10px; top: 31px; border-bottom: 14px solid {$c}; border-left: 7px solid transparent; border-right: 0 solid transparent; width: 0;\"></div>"
        ."<div style=\"position: absolute; left: 20px; top: 31px; border-bottom: 14px solid {$c}; border-right: 7px solid transparent; border-left: 0 solid transparent; width: 0;\"></div>";
    $rotulo = "<div style=\"position: absolute; left: -14px; top: 48px; width: 64px; text-align: center; font-size: 8px; font-weight: bold; color: {$c};\">{$nombre}</div>";

    return "<div style=\"position: absolute; left: ".round($x)."px; top: ".round($y)."px; width: 36px; height: 62px;\">{$cabeza}{$cuerpo}{$brazos}{$piernas}{$rotulo}</div>";
}

/** Línea vertical (sólida o punteada). */
function lineaV(float $x, float $y0, float $y1, string $estilo = 'solid', string $color = '#9ca3af', float $grosor = 1.2): string
{
    $x = round($x);
    $y0 = round($y0);
    $y1 = round($y1);
    $alto = max(1, round(abs($y1 - $y0)));

    return "<div style=\"position: absolute; left: {$x}px; top: {$y0}px; width: 0; height: {$alto}px; border-left: {$grosor}px {$estilo} {$color};\"></div>";
}

/** Línea horizontal (sólida o punteada). */
function lineaH(float $x0, float $x1, float $y, string $estilo = 'solid', string $color = '#9ca3af', float $grosor = 1.2): string
{
    $x0 = round($x0);
    $x1 = round($x1);
    $y = round($y);
    $ancho = max(1, round(abs($x1 - $x0)));

    return "<div style=\"position: absolute; left: {$x0}px; top: {$y}px; height: 0; width: {$ancho}px; border-top: {$grosor}px {$estilo} {$color};\"></div>";
}

/** Flecha horizontal con etiqueta (para DSD). $hacia: 'der'|'izq'. */
function flechaH(float $x1, float $x2, float $y, string $etiqueta = '', string $estilo = 'solid', string $hacia = 'der', string $color = '#374151'): string
{
    $html = lineaH($x1, $x2, $y, $estilo, $color);

    // Punta de flecha con triángulo CSS
    $punta = $hacia === 'der'
        ? "<div style=\"position: absolute; left: ".round($x2 - 1)."px; top: ".round($y - 4)."px; width: 0; height: 0; border-left: 7px solid {$color}; border-top: 4.5px solid transparent; border-bottom: 4.5px solid transparent;\"></div>"
        : "<div style=\"position: absolute; left: ".round($x1 - 6)."px; top: ".round($y - 4)."px; width: 0; height: 0; border-right: 7px solid {$color}; border-top: 4.5px solid transparent; border-bottom: 4.5px solid transparent;\"></div>";

    $rotulo = '';
    if ($etiqueta !== '') {
        $izq = round(min($x1, $x2));
        $ancho = round(abs($x2 - $x1));
        $rotulo = "<div style=\"position: absolute; left: {$izq}px; top: ".round($y - 15)."px; width: {$ancho}px; text-align: center; font-size: 8px; color: #374151;\">{$etiqueta}</div>";
    }

    return $html.$punta.$rotulo;
}

/** Conexión ortogonal entre puntos [x,y] (para diagramas de dominio/CU). */
function conex(array $puntos, string $estilo = 'solid', string $color = '#9ca3af'): string
{
    $html = '';
    for ($i = 0; $i < count($puntos) - 1; $i++) {
        [$x1, $y1] = $puntos[$i];
        [$x2, $y2] = $puntos[$i + 1];
        $html .= abs($y2 - $y1) < abs($x2 - $x1) * 0.001 || abs($y2 - $y1) < 0.5
            ? lineaH($x1, $x2, $y1, $estilo, $color, 1)
            : lineaV($x1, $y1, $y2, $estilo, $color, 1);
    }

    return $html;
}

/** Línea recta entre dos puntos cualesquiera (SVG, válida en navegador y dompdf). */
function diagonal(float $x1, float $y1, float $x2, float $y2, string $color = '#9ca3af', float $grosor = 1): string
{
    $x = round(min($x1, $x2));
    $y = round(min($y1, $y2));
    $w = max(1, round(abs($x2 - $x1)));
    $h = max(1, round(abs($y2 - $y1)));
    $ax = round($x1 - $x);
    $ay = round($y1 - $y);
    $bx = round($x2 - $x);
    $by = round($y2 - $y);

    return "<div style=\"position:absolute;left:{$x}px;top:{$y}px;width:{$w}px;height:{$h}px;\"><svg width=\"{$w}\" height=\"{$h}\" style=\"display:block\"><line x1=\"{$ax}\" y1=\"{$ay}\" x2=\"{$bx}\" y2=\"{$by}\" stroke=\"{$color}\" stroke-width=\"{$grosor}\"/></svg></div>";
}

/** Etiqueta flotante (multiplicidad, rol) en un diagrama. */
function etiqueta(float $x, float $y, string $texto, string $color = '#6b7280'): string
{
    return "<div style=\"position: absolute; left: ".round($x)."px; top: ".round($y)."px; font-size: 7.5px; color: {$color}; background: #ffffff; padding: 0 2px;\">{$texto}</div>";
}

/** Caja de entidad: título + lista de atributos (dominio / relacional / clases). */
function entidad(float $x, float $y, float $w, string $titulo, array $atributos, string $color = '#008c63'): string
{
    $filas = '';
    foreach ($atributos as $a) {
        $esClave = str_contains($a, 'PK') || str_contains($a, 'FK') || str_contains($a, 'UK');
        $negrita = $esClave ? 'font-weight: bold;' : '';
        $filas .= "<div style=\"padding: 1.5px 7px; font-size: 7.6px; color: #374151; {$negrita}border-top: 0.5px solid #f1f5f9;\">{$a}</div>";
    }
    $altoTitulo = 17;
    $alto = $altoTitulo + count($atributos) * 12.5 + 6;
    $html = "<div style=\"background: {$color}; color: #ffffff; font-size: 8.6px; font-weight: bold; text-align: center; padding: 3px 4px; letter-spacing: 0.4px;\">{$titulo}</div>{$filas}";

    return caja($x, $y, $w, $alto, $html, 'border: 1.2px solid '.$color.'; background: #ffffff;')
        ."<!--ALTO:{$alto}-->";
}

/** Igual que entidad() pero devuelve también el alto (para layout calculado). */
function entidadAlto(float $x, float $y, float $w, string $titulo, array $atributos, string $color = '#008c63'): array
{
    $alto = 17 + count($atributos) * 12.5 + 6;

    return [entidad($x, $y, $w, $titulo, $atributos, $color), $alto];
}

// -----------------------------------------------------------------------------
// SALIDA A PNG: cada diagrama se publica como página HTML en public/tmp_docs y
// un paso posterior la captura desde el navegador (zoom 1.5 para nitidez).
// -----------------------------------------------------------------------------

const ZOOM_PNG = 1.5;

/** Página HTML independiente para capturar como imagen. */
function htmlPagina(string $titulo, string $cuerpo, float $ancho, float $alto): string
{
    $w = round($ancho);
    $h = round($alto);
    $zoom = round(ZOOM_PNG * 100);

    return <<<HTML
<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>{$titulo}</title>
<style>
    html { margin: 0; padding: 0; background: #ffffff; zoom: {$zoom}%; }
    #lienzo { position: relative; width: {$w}px; height: {$h}px; overflow: hidden; background: #ffffff; font-family: Arial, Helvetica, sans-serif; }
    .nota { border-left: 3px solid #008c63; background: #f8fafc; padding: 7px 10px; font-size: 9.5px; color: #374151; }
</style></head>
<body><div id="lienzo">{$cuerpo}</div></body></html>
HTML;
}

/** Registra un diagrama para captura: escribe el HTML y suma al manifiesto. */
function registrarPng(array &$manifesto, string $nombre, string $titulo, string $cuerpo, float $ancho, float $alto): void
{
    $archivo = 'd-'.$nombre.'.html';
    file_put_contents(public_path('tmp_docs/'.$archivo), htmlPagina($titulo, $cuerpo, $ancho, $alto));
    $manifesto[] = [
        'archivo' => $nombre.'.png',
        'url' => '/tmp_docs/'.$archivo,
        'w' => (int) round($ancho * ZOOM_PNG),
        'h' => (int) round($alto * ZOOM_PNG),
    ];
    echo "PNG  {$nombre}.html ({$ancho}x{$alto})\n";
}

/** Escribe el manifiesto que consume el capturador del navegador. */
function escribirManifesto(array $manifesto): void
{
    file_put_contents(public_path('tmp_docs/manifest.json'), json_encode($manifesto, JSON_PRETTY_PRINT));
    echo 'Manifesto: '.count($manifesto)." diagramas\n";
}

