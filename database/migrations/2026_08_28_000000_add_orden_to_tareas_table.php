<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->integer('orden')->default(0)->after('fecha_limite');
        });

        // Orden inicial: mientras nadie arrastre nada, las tarjetas respetan el
        // orden de creación dentro de cada columna.
        DB::update('UPDATE tareas SET orden = id');
    }

    public function down(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropColumn('orden');
        });
    }
};
