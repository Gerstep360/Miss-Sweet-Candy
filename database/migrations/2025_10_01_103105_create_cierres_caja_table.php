<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cierres_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cajero_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->dateTime('inicio');
            $table->dateTime('fin');
            $table->decimal('total_sistema', 12, 2)->default(0);
            $table->decimal('total_declarado', 12, 2)->default(0);
            $table->decimal('diferencia', 12, 2)->default(0);
            $table->string('observaciones', 255)->nullable();
            
            $table->index('cajero_id', 'idx_cc_cajero');
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->dropForeign(['cajero_id']);
            
            $table->foreign('cajero_id', 'fk_cc_cajero')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cierres_caja');
    }
};