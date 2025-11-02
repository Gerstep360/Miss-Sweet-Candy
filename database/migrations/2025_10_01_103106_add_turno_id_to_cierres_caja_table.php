<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->foreignId('turno_id')->nullable()->after('id')->constrained('turnos_caja')->onUpdate('cascade')->onDelete('restrict');
            
            $table->index('turno_id', 'idx_cc_turno');
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->dropForeign(['turno_id']);
            
            $table->foreign('turno_id', 'fk_cc_turno')
                  ->references('id')->on('turnos_caja')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('cierres_caja', function (Blueprint $table) {
            $table->dropForeign('fk_cc_turno');
            $table->dropIndex('idx_cc_turno');
            $table->dropColumn('turno_id');
        });
    }
};
