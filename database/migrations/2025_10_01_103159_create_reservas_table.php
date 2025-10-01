<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('mesa_id')->constrained('mesas')->onUpdate('cascade')->onDelete('restrict');
            $table->date('fecha');
            $table->time('hora');
            $table->integer('numero_personas');
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'cumplida'])->default('pendiente');
            $table->string('observaciones', 255)->nullable();
            
            $table->unique(['mesa_id', 'fecha', 'hora'], 'uk_reserva_slot');
            $table->index('cliente_id', 'idx_res_cliente');
        });

        // Renombrar constraints FK con nombres explícitos
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['mesa_id']);
            
            $table->foreign('cliente_id', 'fk_res_cliente')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('restrict');
            
            $table->foreign('mesa_id', 'fk_res_mesa')
                  ->references('id')->on('mesas')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas');
    }
};