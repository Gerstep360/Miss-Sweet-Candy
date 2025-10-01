<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->enum('tipo', ['porcentaje', 'monto_fijo', '2x1', 'combo']);
            $table->enum('aplica_sobre', ['item', 'pedido'])->default('item');
            $table->decimal('valor', 10, 2)->default(0);
            $table->decimal('tope_descuento', 10, 2)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->integer('prioridad')->default(1);
            $table->boolean('activo')->default(true);
            
            $table->index(['activo', 'fecha_inicio', 'fecha_fin', 'prioridad'], 'idx_promo_vigencia');
        });

        // Agregar columna SET para dias_semana usando SQL directo
        DB::statement("ALTER TABLE promociones ADD COLUMN dias_semana SET('lun','mar','mie','jue','vie','sab','dom') NULL AFTER hora_fin");
    }

    public function down()
    {
        Schema::dropIfExists('promociones');
    }
};