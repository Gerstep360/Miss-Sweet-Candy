<?php
// database/migrations/2024_01_01_000001_create_fidelidad_config_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFidelidadConfigTable extends Migration
{
    public function up()
    {
        Schema::create('fidelidad_config', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor');
            $table->string('tipo')->default('string'); // string, integer, boolean, float
            $table->text('descripcion')->nullable();
            $table->string('categoria')->default('general');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fidelidad_config');
    }
}