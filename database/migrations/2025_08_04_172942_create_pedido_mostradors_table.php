<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pedido_mostradors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('atendido_por')->constrained('users')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'enviado', 'anulado', 'retirado', 'cancelado'])->default('pendiente');
            $table->string('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pedido_mostradors');
    }
};
