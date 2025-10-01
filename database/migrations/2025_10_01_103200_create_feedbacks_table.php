<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedTinyInteger('calificacion');
            $table->string('comentario', 255)->nullable();
            
            $table->index('cliente_id', 'idx_fb_cliente');
            $table->index('pedido_id', 'idx_fb_pedido');
        });

        // Renombrar constraints FK con nombres explícitos
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['pedido_id']);
            
            $table->foreign('cliente_id', 'fk_fb_cliente')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('set null');
            
            $table->foreign('pedido_id', 'fk_fb_pedido')
                  ->references('id')->on('pedidos')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedbacks');
    }
};