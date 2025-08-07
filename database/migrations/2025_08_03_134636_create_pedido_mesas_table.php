<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_mesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesa_id')->constrained('mesas')->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('atendido_por')->constrained('users')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'enviado', 'anulado', 'servido', 'cancelado'])->default('pendiente');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
{
    Schema::dropIfExists('pedido_mesas');
}
};
