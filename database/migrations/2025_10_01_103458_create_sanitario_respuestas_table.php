<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sanitario_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lista_id')->constrained('sanitario_listas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('sanitario_items')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->string('valor_texto', 200)->nullable();
            $table->decimal('valor_numero', 10, 2)->nullable();
            $table->boolean('valor_check')->nullable();
            $table->string('foto_ruta', 255)->nullable();
            
            $table->index(['lista_id', 'item_id'], 'idx_sresp_li_it');
            $table->index('usuario_id', 'idx_sresp_user');
        });

        // Renombrar constraints FK con nombres explícitos
        Schema::table('sanitario_respuestas', function (Blueprint $table) {
            $table->dropForeign(['lista_id']);
            $table->dropForeign(['item_id']);
            $table->dropForeign(['usuario_id']);
            
            $table->foreign('lista_id', 'fk_sresp_lista')
                  ->references('id')->on('sanitario_listas')
                  ->onUpdate('cascade')->onDelete('cascade');
            
            $table->foreign('item_id', 'fk_sresp_item')
                  ->references('id')->on('sanitario_items')
                  ->onUpdate('cascade')->onDelete('cascade');
            
            $table->foreign('usuario_id', 'fk_sresp_user')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sanitario_respuestas');
    }
};