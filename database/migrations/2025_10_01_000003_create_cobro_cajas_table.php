<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobro_cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onUpdate('cascade')->onDelete('restrict');
            $table->decimal('importe', 12, 2);
            $table->enum('metodo', ['efectivo', 'pos', 'qr']);
            $table->enum('estado', ['cobrado', 'cancelado'])->default('cobrado');
            $table->string('comprobante', 255)->nullable();
            $table->foreignId('cajero_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->string('qr_tx_id', 80)->nullable();
            $table->enum('qr_estado', ['pendiente', 'aprobado', 'rechazado'])->nullable();
            $table->string('qr_proveedor', 50)->nullable();
            $table->string('qr_referencia', 120)->nullable();
            $table->timestamps();
            
            $table->index('pedido_id', 'idx_cobro_pedido');
            $table->index('cajero_id', 'idx_cobro_cajero');
        });

        // Renombrar constraints FK con nombres explícitos
        Schema::table('cobro_cajas', function (Blueprint $table) {
            $table->dropForeign(['pedido_id']);
            $table->dropForeign(['cajero_id']);
            
            $table->foreign('pedido_id', 'fk_cobro_pedido')
                  ->references('id')->on('pedidos')
                  ->onUpdate('cascade')->onDelete('restrict');
            
            $table->foreign('cajero_id', 'fk_cobro_cajero')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobro_cajas');
    }
};