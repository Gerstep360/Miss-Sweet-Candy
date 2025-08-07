<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cobro_cajas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_mostrador_id')->nullable();
            $table->unsignedBigInteger('pedido_mesa_id')->nullable();
            $table->decimal('importe', 10, 2);
            $table->enum('metodo', ['efectivo', 'pos']);
            $table->enum('estado', ['cobrado', 'cancelado'])->default('cobrado');
            $table->string('comprobante')->nullable();
            $table->unsignedBigInteger('cajero_id');
            $table->timestamps();

            $table->foreign('pedido_mostrador_id')->references('id')->on('pedido_mostradors')->onDelete('cascade');
            $table->foreign('pedido_mesa_id')->references('id')->on('pedido_mesas')->onDelete('cascade');
            $table->foreign('cajero_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobro_cajas');
    }
};
