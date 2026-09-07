<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sprints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->string('nombre');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->timestamps();
        });

        // Una tarea pertenece a lo sumo a un sprint; si el sprint se borra,
        // la tarea queda "sin sprint" en vez de desaparecer.
        Schema::table('tareas', function (Blueprint $table) {
            $table->foreignId('sprint_id')->nullable()
                ->constrained('sprints')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sprint_id');
        });
        Schema::dropIfExists('sprints');
    }
};
