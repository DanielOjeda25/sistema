<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actualizaciones_proyecto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->foreignId('creado_por')->constrained('users')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('tipo')->default('avance');
            $table->date('fecha');
            $table->boolean('visible_cliente')->default(false);
            $table->timestamps();

            $table->index(['proyecto_id', 'fecha']);
            $table->index(['proyecto_id', 'visible_cliente']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actualizaciones_proyecto');
    }
};
