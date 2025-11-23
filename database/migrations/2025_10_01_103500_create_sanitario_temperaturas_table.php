<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sanitario_temperaturas', function (Blueprint $table) {
            $table->id();
            $table->string('equipo', 80);
            $table->decimal('temperatura', 5, 2);
            $table->foreignId('usuario_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            
            $table->index('usuario_id', 'idx_stemp_user');
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('sanitario_temperaturas', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            
            $table->foreign('usuario_id', 'fk_stemp_user')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sanitario_temperaturas');
    }
};