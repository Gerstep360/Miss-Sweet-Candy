<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promocion_canales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promocion_id')->constrained('promociones')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('canal', ['mesa', 'mostrador', 'web']);
            
            $table->unique(['promocion_id', 'canal'], 'uk_promo_canal');
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('promocion_canales', function (Blueprint $table) {
            $table->dropForeign(['promocion_id']);
            
            $table->foreign('promocion_id', 'fk_pcanal_promo')
                  ->references('id')->on('promociones')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('promocion_canales');
    }
};