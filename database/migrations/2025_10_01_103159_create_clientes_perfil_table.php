<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes_perfil', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('telefono', 30)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->text('alergias')->nullable();
            $table->text('preferencias')->nullable();
            $table->boolean('acepta_marketing')->default(0);
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('clientes_perfil', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            
            $table->foreign('user_id', 'fk_cperfil_user')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes_perfil');
    }
};