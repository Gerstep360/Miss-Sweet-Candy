<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movimientos_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onUpdate('cascade')->onDelete('restrict');
            $table->enum('tipo', ['entrada', 'salida', 'ajuste', 'venta']);
            $table->integer('cantidad');
            $table->integer('saldo_anterior');
            $table->integer('saldo_nuevo');
            $table->string('motivo', 200)->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('pedido_item_id')->nullable()->constrained('pedido_items')->onUpdate('cascade')->onDelete('set null');
            $table->timestamp('created_at')->nullable();
            
            $table->index('producto_id', 'idx_mov_prod');
            $table->index('usuario_id', 'idx_mov_user');
            $table->index('pedido_id', 'idx_mov_pedido');
            $table->index('pedido_item_id', 'idx_mov_item');
        });

        // Renombrar constraints FK con nombres explícitos
        Schema::table('movimientos_producto', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['pedido_id']);
            $table->dropForeign(['pedido_item_id']);
            
            $table->foreign('producto_id', 'fk_mov_producto')
                  ->references('id')->on('productos')
                  ->onUpdate('cascade')->onDelete('restrict');
            
            $table->foreign('usuario_id', 'fk_mov_usuario')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('set null');
            
            $table->foreign('pedido_id', 'fk_mov_pedido')
                  ->references('id')->on('pedidos')
                  ->onUpdate('cascade')->onDelete('set null');
            
            $table->foreign('pedido_item_id', 'fk_mov_item')
                  ->references('id')->on('pedido_items')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos_producto');
    }
};