<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_two_factor', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('secret', 255);
            $table->json('recovery_codes')->nullable();
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('user_two_factor', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            
            $table->foreign('user_id', 'fk_u2f_user')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_two_factor');
    }
};