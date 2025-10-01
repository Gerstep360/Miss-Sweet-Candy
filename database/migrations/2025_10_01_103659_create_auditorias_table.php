<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->string('accion', 40);
            $table->string('entidad', 60);
            $table->unsignedBigInteger('entidad_id');
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 200)->nullable();
            $table->timestamp('created_at')->nullable();
            
            $table->index('usuario_id', 'idx_aud_user');
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('auditorias', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            
            $table->foreign('usuario_id', 'fk_aud_user')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('auditorias');
    }
};