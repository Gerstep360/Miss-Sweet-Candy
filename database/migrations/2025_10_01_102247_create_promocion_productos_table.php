<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promocion_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promocion_id')->constrained('promociones')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('producto_id')->nullable()->constrained('productos')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->onUpdate('cascade')->onDelete('restrict');
            $table->integer('cantidad_requerida')->default(1);
            
            $table->unique(['promocion_id', 'producto_id', 'categoria_id'], 'uk_promo_prod_cat');
            $table->index('producto_id', 'idx_ppromo_prod');
            $table->index('categoria_id', 'idx_ppromo_cat');
        });

        // Renombrar constraints FK con nombres explícitos
        Schema::table('promocion_productos', function (Blueprint $table) {
            $table->dropForeign(['promocion_id']);
            $table->dropForeign(['producto_id']);
            $table->dropForeign(['categoria_id']);
            
            $table->foreign('promocion_id', 'fk_ppromo_promo')
                  ->references('id')->on('promociones')
                  ->onUpdate('cascade')->onDelete('cascade');
            
            $table->foreign('producto_id', 'fk_ppromo_producto')
                  ->references('id')->on('productos')
                  ->onUpdate('cascade')->onDelete('restrict');
            
            $table->foreign('categoria_id', 'fk_ppromo_categoria')
                  ->references('id')->on('categorias')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('promocion_productos');
    }
};