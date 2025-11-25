<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fidelidad_movimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->integer('puntos');
            $table->enum('tipo', ['acumulo', 'canje']);
            $table->string('descripcion');
            $table->string('origen_type')->nullable(); // Para relación polimórfica
            $table->unsignedBigInteger('origen_id')->nullable(); // Para relación polimórfica
            $table->timestamps();

            // Foreign key CORRECTA - referencia a users.id
            $table->foreign('cliente_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // Índices para mejor performance
            $table->index('cliente_id');
            $table->index(['origen_type', 'origen_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fidelidad_movimientos');
    }
};