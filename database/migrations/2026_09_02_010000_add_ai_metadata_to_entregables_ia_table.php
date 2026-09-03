<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entregables_ia', function (Blueprint $table) {
            $table->string('origen')->default('manual')->after('tipo');
            $table->string('modelo_ia')->nullable()->after('origen');
            $table->string('version_prompt')->nullable()->after('modelo_ia');
            $table->json('contexto_fuente')->nullable()->after('version_prompt');
            $table->boolean('visible_cliente')->default(false)->after('estado');
            $table->timestamp('generado_en')->nullable()->after('visible_cliente');
            $table->foreignId('aprobado_por')->nullable()->after('generado_por')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('aprobado_en')->nullable()->after('aprobado_por');
            $table->text('mensaje_error')->nullable()->after('aprobado_en');

            $table->index(['proyecto_id', 'origen']);
            $table->index(['proyecto_id', 'visible_cliente', 'estado'], 'entregables_cliente_estado_idx');
        });
    }

    public function down(): void
    {
        Schema::table('entregables_ia', function (Blueprint $table) {
            $table->dropForeign(['aprobado_por']);
            $table->dropIndex(['proyecto_id', 'origen']);
            $table->dropIndex('entregables_cliente_estado_idx');
            $table->dropColumn([
                'origen',
                'modelo_ia',
                'version_prompt',
                'contexto_fuente',
                'visible_cliente',
                'generado_en',
                'aprobado_por',
                'aprobado_en',
                'mensaje_error',
            ]);
        });
    }
};
