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
        // Tabla de registros de limpieza
        Schema::create('cumplimiento_limpieza', function (Blueprint $table) {
            $table->id();
            $table->string('area', 100);
            $table->string('tipo_limpieza', 50);
            $table->string('productos_usados', 500);
            $table->text('observaciones')->nullable();
            $table->foreignId('responsable_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_registro');
            $table->time('hora_registro');
            $table->timestamps();
            
            $table->index(['fecha_registro', 'area']);
            $table->index('responsable_id');
        });

        // Tabla de control de temperaturas
        Schema::create('cumplimiento_temperatura', function (Blueprint $table) {
            $table->id();
            $table->string('equipo', 100);
            $table->decimal('temperatura', 5, 2);
            $table->decimal('temperatura_minima', 5, 2)->nullable();
            $table->decimal('temperatura_maxima', 5, 2)->nullable();
            $table->boolean('fuera_rango')->default(false);
            $table->enum('estado_equipo', ['normal', 'alerta', 'critico'])->default('normal');
            $table->text('observaciones')->nullable();
            $table->foreignId('responsable_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_registro');
            $table->time('hora_registro');
            $table->timestamps();
            
            $table->index(['fecha_registro', 'equipo']);
            $table->index('fuera_rango');
            $table->index('estado_equipo');
            $table->index('responsable_id');
        });

        // Tabla de evidencias fotográficas
        Schema::create('cumplimiento_evidencias', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_registro', ['limpieza', 'temperatura']);
            $table->unsignedBigInteger('registro_id');
            $table->string('ruta_archivo', 500);
            $table->string('nombre_original', 255);
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['tipo_registro', 'registro_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cumplimiento_evidencias');
        Schema::dropIfExists('cumplimiento_temperatura');
        Schema::dropIfExists('cumplimiento_limpieza');
    }
};
