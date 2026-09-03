<?php

namespace App\Contracts;

interface ProjectReportGenerator
{
    /**
     * Genera el texto que verá el equipo antes de aprobarlo para el Cliente.
     *
     * @param  array<string, mixed>  $context
     */
    public function generate(array $context): string;
}
