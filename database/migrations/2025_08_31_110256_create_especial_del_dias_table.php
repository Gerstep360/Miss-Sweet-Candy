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
            $table->foreignId('producto_id')->constrained('productos')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('dia_semana', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'])->comment('Día de la semana para el especial');
            $table->date('fecha_especifica')->nullable()->comment('Para promociones en fechas específicas');
            $table->decimal('precio_especial', 8, 2)->nullable()->comment('Precio con descuento');
            $table->decimal('descuento_porcentaje', 5, 2)->nullable()->comment('Porcentaje de descuento');
            $table->text('descripcion_especial')->nullable()->comment('Descripción especial del día');
            $table->boolean('activo')->default(true)->comment('Si el especial está activo');
            $table->datetime('fecha_inicio')->nullable()->comment('Inicio de la promoción');
            $table->datetime('fecha_fin')->nullable()->comment('Fin de la promoción');
            $table->integer('prioridad')->default(1)->comment('Prioridad en caso de múltiples especiales');
            $table->timestamps();

            // Constraint único: un producto solo puede tener un especial por día de la semana
            $table->unique(['producto_id', 'dia_semana'], 'uk_especial_producto_dia');
            
            // Índices compuestos para optimizar consultas
            $table->index(['dia_semana', 'activo'], 'idx_especial_semana_act');
            $table->index(['fecha_especifica', 'activo'], 'idx_especial_fecha_act');
            $table->index(['fecha_inicio', 'fecha_fin', 'activo'], 'idx_especial_rango_act');
        });

        // Agregar nombre de constraint FK explícito
        Schema::table('especial_del_dia', function (Blueprint $table) {
            $table->foreign('producto_id', 'fk_especial_producto')
                  ->references('id')->on('productos')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('especial_del_dia');
    }
};