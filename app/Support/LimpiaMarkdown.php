<?php

namespace App\Support;

class LimpiaMarkdown
{
    /**
     * Los modelos devuelven markdown aunque se les pida texto plano: se quitan
     * titulos (#..), negritas (** __), itálicas (*) y codigo (`).
     */
    public static function limpiar(string $texto): string
    {
        $patrones = [
            '/^\s*#{1,6}\s+/m',      // títulos # ## ...
            '/\*\*([^*]+)\*\*/',     // **negrita**
            '/__([^_]+)__/',         // __negrita__
            '/\*([^*\n]+)\*/',       // *itálica*
            '/`([^`]+)`/',           // `código`
        ];

        return trim(preg_replace($patrones, '$1', $texto));
    }
}
