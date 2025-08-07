<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30)->unique();
            $table->enum('estado', ['libre', 'ocupada', 'reservada', 'fusionada'])->default('libre');
            $table->unsignedBigInteger('fusion_id')->nullable()->comment('Mesa principal si está fusionada');
            $table->integer('capacidad')->default(4);
            $table->timestamps();

            $table->foreign('fusion_id')->references('id')->on('mesas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('mesas');
    }
};
