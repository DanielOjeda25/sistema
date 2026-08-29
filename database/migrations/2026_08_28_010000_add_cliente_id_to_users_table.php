<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vincula una cuenta de usuario con el cliente de la empresa que
            // representa. Solo aplica a usuarios con rol Cliente: los internos
            // (Jefe, PM, PO, Programador) lo dejan en NULL.
            $table->foreignId('cliente_id')
                ->nullable()
                ->after('estado')
                ->constrained('clientes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cliente_id');
        });
    }
};
