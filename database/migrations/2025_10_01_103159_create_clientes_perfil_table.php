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
            
            // Alergias con estructura JSON para nivel de severidad
            $table->json('alergias')->nullable()->comment('JSON: [{nombre: string, severidad: leve|moderado|grave}]');
            
            // Preferencias alimentarias (JSON array)
            $table->json('preferencias')->nullable()->comment('JSON: [vegetariano, vegano, sin_gluten, sin_lactosa, etc]');
            
            $table->boolean('acepta_marketing')->default(0);
            $table->timestamps(); // Registra fecha y hora de última actualización
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