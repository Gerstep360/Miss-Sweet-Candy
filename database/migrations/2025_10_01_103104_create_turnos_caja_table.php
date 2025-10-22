<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('turnos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cajero_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamp('inicio');
            $table->timestamp('fin')->nullable();
            $table->enum('estado', ['activo', 'cerrado'])->default('activo');
            $table->decimal('monto_inicial', 10, 2)->default(0);
            $table->text('observaciones_apertura')->nullable();
            $table->text('observaciones_cierre')->nullable();
            
            $table->index(['cajero_id', 'estado'], 'idx_turno_cajero_estado');
            $table->index('inicio', 'idx_turno_inicio');
            $table->index('estado', 'idx_turno_estado');
        });

        // Agregar foreign key con nombre explícito
        Schema::table('turnos_caja', function (Blueprint $table) {
            $table->dropForeign(['cajero_id']);
            $table->foreign('cajero_id', 'fk_turno_cajero')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('turnos_caja');
    }
};
