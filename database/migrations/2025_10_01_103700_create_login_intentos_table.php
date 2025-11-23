<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('login_intentos', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255);
            $table->boolean('exitoso');
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable();
            
            $table->index('email', 'idx_login_email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_intentos');
    }
};