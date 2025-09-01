<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('especial_del_dia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->enum('dia_semana', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo']);
            $table->date('fecha_especifica')->nullable(); // Para fechas específicas como promociones
            $table->decimal('precio_especial', 8, 2)->nullable(); // Precio con descuento
            $table->decimal('descuento_porcentaje', 5, 2)->nullable(); // % de descuento
            $table->text('descripcion_especial')->nullable(); // Descripción especial para el día
            $table->boolean('activo')->default(true);
            $table->datetime('fecha_inicio')->nullable(); // Cuando inicia la promoción
            $table->datetime('fecha_fin')->nullable(); // Cuando termina la promoción
            $table->integer('prioridad')->default(1); // Para manejar múltiples especiales
            $table->timestamps();

            // Índices para mejorar performance
            $table->index(['dia_semana', 'activo']);
            $table->index(['fecha_especifica', 'activo']);
            $table->index(['fecha_inicio', 'fecha_fin', 'activo']);
            
            // Constraint único para evitar duplicados por día
            $table->unique(['producto_id', 'dia_semana'], 'unique_producto_dia');
        });
    }

    public function down()
    {
        Schema::dropIfExists('especial_del_dia');
    }
};