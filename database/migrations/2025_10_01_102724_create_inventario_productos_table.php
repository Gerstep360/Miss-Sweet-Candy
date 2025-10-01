<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventario_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onUpdate('cascade')->onDelete('restrict');
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->integer('punto_reposicion')->default(0);
            $table->string('ubicacion', 120)->nullable();
            
            $table->unique('producto_id', 'uk_inv_producto');
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('inventario_productos', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            
            $table->foreign('producto_id', 'fk_inv_producto')
                  ->references('id')->on('productos')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventario_productos');
    }
};