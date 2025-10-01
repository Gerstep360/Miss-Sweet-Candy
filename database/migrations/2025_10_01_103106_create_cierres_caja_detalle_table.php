<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cierres_caja_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cierre_caja_id')->constrained('cierres_caja')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('metodo', ['efectivo', 'pos', 'qr']);
            $table->decimal('monto_sistema', 12, 2)->default(0);
            $table->decimal('monto_declarado', 12, 2)->default(0);
            
            $table->index('cierre_caja_id', 'idx_ccd_cierre');
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('cierres_caja_detalle', function (Blueprint $table) {
            $table->dropForeign(['cierre_caja_id']);
            
            $table->foreign('cierre_caja_id', 'fk_ccd_cierre')
                  ->references('id')->on('cierres_caja')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cierres_caja_detalle');
    }
};