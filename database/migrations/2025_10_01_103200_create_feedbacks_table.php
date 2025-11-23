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
            
            // Tipo de feedback
            $table->enum('tipo', ['general', 'pedido', 'servicio', 'local', 'web'])->default('general');
            
            // Calificación general
            $table->unsignedTinyInteger('calificacion');
            
            // Calificaciones por categoría (0-5 estrellas, 0 = no calificado)
            $table->unsignedTinyInteger('calificacion_comida')->nullable();
            $table->unsignedTinyInteger('calificacion_servicio')->nullable();
            $table->unsignedTinyInteger('calificacion_ambiente')->nullable();
            $table->unsignedTinyInteger('calificacion_precio')->nullable();
            $table->unsignedTinyInteger('calificacion_limpieza')->nullable();
            $table->unsignedTinyInteger('calificacion_web')->nullable();
            
            // Comentarios y feedback
            $table->text('comentario')->nullable();
            $table->text('sugerencias')->nullable();
            $table->text('quejas')->nullable();
            $table->text('elogios')->nullable();
            
            // ¿Recomendaría el lugar?
            $table->boolean('recomendaria')->default(true);
            
            // ¿Es cliente frecuente?
            $table->enum('frecuencia_visita', ['primera_vez', 'ocasional', 'frecuente', 'regular'])->default('primera_vez');
            
            // Estado del feedback (para seguimiento administrativo)
            $table->enum('estado', ['pendiente', 'revisado', 'respondido', 'resuelto'])->default('pendiente');
            
            // Respuesta del administrador
            $table->text('respuesta_admin')->nullable();
            $table->foreignId('respondido_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('respondido_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Índices
            $table->index('cliente_id', 'idx_fb_cliente');
            $table->index('pedido_id', 'idx_fb_pedido');
            $table->index('tipo', 'idx_fb_tipo');
            $table->index('estado', 'idx_fb_estado');
            $table->index('created_at', 'idx_fb_created');
        });

        // Renombrar constraints FK con nombres explícitos
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['pedido_id']);
            $table->dropForeign(['respondido_por']);
            
            $table->foreign('cliente_id', 'fk_fb_cliente')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('set null');
            
            $table->foreign('pedido_id', 'fk_fb_pedido')
                  ->references('id')->on('pedidos')
                  ->onUpdate('cascade')->onDelete('set null');
            
            $table->foreign('respondido_por', 'fk_fb_respondido_por')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedbacks');
    }
};