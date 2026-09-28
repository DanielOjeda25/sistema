<?php

namespace Tests\Unit;

use App\Support\LimpiaMarkdown;
use PHPUnit\Framework\TestCase;

class LimpiaMarkdownTest extends TestCase
{
    public function test_quita_titulos_negritas_itulicas_y_codigo(): void
    {
        $this->assertSame(
            "Resumen general\nQ1 sigue avanzando",
            LimpiaMarkdown::limpiar("## Resumen general\n**Q1** sigue `avanzando`")
        );
    }

    public function test_quita_negritas_con_guiones_bajos_e_italicas(): void
    {
        $this->assertSame(
            'Importante leer y compartir',
            LimpiaMarkdown::limpiar('__Importante__ *leer* y compartir')
        );
    }

    public function test_deja_el_texto_plano_intacto(): void
    {
        $this->assertSame('Texto comun de informe', LimpiaMarkdown::limpiar('Texto comun de informe'));
    }

    public function test_recorta_espacios_y_lineas_sobrantes(): void
    {
        $this->assertSame('Hola', LimpiaMarkdown::limpiar("   \nHola\n\n  "));
    }
}
