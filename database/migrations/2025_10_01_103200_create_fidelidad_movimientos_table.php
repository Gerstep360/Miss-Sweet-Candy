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
            $table->unsignedBigInteger('cliente_id'); // Cambiar el nombre
            $table->integer('puntos');
            $table->string('tipo'); // ganancia, canje, etc.
            $table->string('descripcion');
            $table->morphs('origen');
            $table->timestamps();

            // Referenciar user_id en lugar de id
            $table->foreign('cliente_id')
                  ->references('user_id')
                  ->on('clientes_perfil')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fidelidad_movimientos');
    }
};