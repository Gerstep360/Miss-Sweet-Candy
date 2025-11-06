<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('alergenos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique()->comment('Nombre del alérgeno (ej: Gluten, Lactosa, Nueces)');
            $table->string('icono', 50)->nullable()->comment('Emoji o código del icono');
            $table->string('color', 20)->default('red')->comment('Color para el badge (red, orange, yellow)');
            $table->text('descripcion')->nullable()->comment('Descripción del alérgeno');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alergenos');
    }
};
