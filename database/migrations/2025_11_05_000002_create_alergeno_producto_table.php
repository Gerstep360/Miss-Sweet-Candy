<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('alergeno_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('alergeno_id')->constrained('alergenos')->onDelete('cascade');
            $table->enum('nivel_presencia', ['contiene', 'puede_contener', 'trazas'])
                  ->default('contiene')
                  ->comment('Nivel de presencia: contiene (directo), puede_contener (posible), trazas (residual)');
            $table->timestamps();
            
            // Índice único para evitar duplicados
            $table->unique(['producto_id', 'alergeno_id'], 'uk_producto_alergeno');
        });
    }

    public function down()
    {
        Schema::dropIfExists('alergeno_producto');
    }
};
