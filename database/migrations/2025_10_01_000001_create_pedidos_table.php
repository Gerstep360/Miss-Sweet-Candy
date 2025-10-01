<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['mesa', 'mostrador', 'web']);
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('atendido_por')->nullable();
            $table->unsignedBigInteger('mesa_id')->nullable();
            $table->enum('modalidad', ['click_collect', 'delivery'])->nullable();
            $table->enum('estado', [
                'pendiente',
                'confirmado',
                'en_preparacion',
                'preparado',
                'en_reparto',
                'entregado',
                'servido',
                'retirado',
                'anulado',
                'cancelado'
            ])->default('pendiente');
            $table->dateTime('programado_para')->nullable();
            $table->string('direccion_entrega', 200)->nullable();
            $table->decimal('gps_lat', 10, 7)->nullable();
            $table->decimal('gps_lng', 10, 7)->nullable();
            $table->string('telefono_contacto', 30)->nullable();
            $table->enum('canal', ['local', 'web', 'app'])->default('local');
            $table->string('notas', 255)->nullable();
            $table->timestamps();

            // Índices
            $table->index('cliente_id', 'idx_ped_cliente');
            $table->index('atendido_por', 'idx_ped_atendido');
            $table->index('mesa_id', 'idx_ped_mesa');
            $table->index(['tipo', 'estado'], 'idx_ped_tipo_estado');
            $table->index('programado_para', 'idx_ped_programado');

            // Foreign Keys
            $table->foreign('cliente_id', 'fk_ped_cliente')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('atendido_por', 'fk_ped_atendido')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('mesa_id', 'fk_ped_mesa')->references('id')->on('mesas')->onUpdate('cascade')->onDelete('restrict');
        });

        // Constraints CHECK
        DB::statement("ALTER TABLE pedidos ADD CONSTRAINT ck_ped_modalidad_web CHECK (tipo = 'web' OR modalidad IS NULL)");
        DB::statement("ALTER TABLE pedidos ADD CONSTRAINT ck_ped_dir_delivery CHECK (modalidad <> 'delivery' OR direccion_entrega IS NOT NULL)");
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
