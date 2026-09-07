<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Columnas que necesita el módulo de resumen de sprint con IA (rama
     * devolucion-ai), agregadas a la tabla `sprints` que ya existe desde
     * 2026_09_04_000000 para no duplicar la creación de la tabla.
     */
    public function up(): void
    {
        Schema::table('sprints', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('nombre');
            $table->string('estado', 30)->default('planificado')->after('fecha_fin');
            $table->mediumText('resumen_ia')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('sprints', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'estado', 'resumen_ia']);
        });
    }
};
