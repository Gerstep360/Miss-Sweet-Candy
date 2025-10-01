<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sanitario_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lista_id')->constrained('sanitario_listas')->onUpdate('cascade')->onDelete('cascade');
            $table->string('texto', 200);
            $table->enum('tipo', ['check', 'numero', 'texto', 'foto'])->default('check');
            
            $table->index('lista_id', 'idx_sitem_lista');
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('sanitario_items', function (Blueprint $table) {
            $table->dropForeign(['lista_id']);
            
            $table->foreign('lista_id', 'fk_sitem_lista')
                  ->references('id')->on('sanitario_listas')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sanitario_items');
    }
};