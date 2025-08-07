<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('productos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
        $table->string('nombre');
        $table->string('unidad')->nullable();
        $table->decimal('precio', 8, 2);
        $table->string('imagen')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
