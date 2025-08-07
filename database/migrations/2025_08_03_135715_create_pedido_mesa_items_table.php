<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_mesa_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_mesa_id')->constrained('pedido_mesas')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad')->default(1);
            $table->decimal('precio', 10, 2); // precio unitario
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_mesa_items');
    }
};
