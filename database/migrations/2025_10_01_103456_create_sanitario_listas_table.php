<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sanitario_listas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('descripcion', 255)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sanitario_listas');
    }
};