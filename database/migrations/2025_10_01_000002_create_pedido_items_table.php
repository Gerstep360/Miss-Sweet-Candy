<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onUpdate('cascade')->onDelete('restrict');
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento_item', 10, 2)->nullable()->default(0.00);
            $table->decimal('subtotal_item', 12, 2);
            $table->enum('estado_item', [
                'pendiente',
                'enviado',
                'preparado',
                'servido',
                'retirado',
                'entregado',
                'anulado'
            ])->default('pendiente');
            $table->enum('destino', ['barra', 'cocina'])->nullable();
            $table->string('notas', 255)->nullable();
            $table->timestamps();

            // Índices
            $table->index('pedido_id', 'idx_item_pedido');
            $table->index('producto_id', 'idx_item_producto');
            $table->index('estado_item', 'idx_item_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
    }
};
