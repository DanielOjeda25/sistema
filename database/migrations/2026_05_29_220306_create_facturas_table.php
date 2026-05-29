<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->decimal('monto', 10, 2);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['pendiente', 'pagada', 'vencida'])->default('pendiente');
            $table->text('detalle')->nullable();
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('emitida_por')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};