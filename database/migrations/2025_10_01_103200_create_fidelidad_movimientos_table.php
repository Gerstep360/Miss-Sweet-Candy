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
            $table->foreignId('cliente_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onUpdate('cascade')->onDelete('set null');
            $table->enum('tipo', ['acumulo', 'canje']);
            $table->integer('puntos');
            $table->string('motivo', 200)->nullable();
            
            $table->index('cliente_id', 'idx_fid_cliente');
            $table->index('pedido_id', 'idx_fid_pedido');
        });

        // Renombrar constraints FK con nombres explícitos
        Schema::table('fidelidad_movimientos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropForeign(['pedido_id']);
            
            $table->foreign('cliente_id', 'fk_fid_cliente')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('restrict');
            
            $table->foreign('pedido_id', 'fk_fid_pedido')
                  ->references('id')->on('pedidos')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fidelidad_movimientos');
    }
};