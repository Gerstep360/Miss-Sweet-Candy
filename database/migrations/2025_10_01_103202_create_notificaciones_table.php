<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['pedido', 'stock', 'reserva', 'sistema']);
            $table->enum('canal', ['panel', 'email', 'push'])->default('panel');
            $table->string('mensaje', 255);
            $table->foreignId('usuario_destino_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->string('rel_model', 60)->nullable();
            $table->unsignedBigInteger('rel_id')->nullable();
            $table->boolean('leido')->default(0);
            
            $table->index('usuario_destino_id', 'idx_notif_user');
            $table->index(['rel_model', 'rel_id'], 'idx_notif_rel');
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropForeign(['usuario_destino_id']);
            
            $table->foreign('usuario_destino_id', 'fk_notif_user')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notificaciones');
    }
};