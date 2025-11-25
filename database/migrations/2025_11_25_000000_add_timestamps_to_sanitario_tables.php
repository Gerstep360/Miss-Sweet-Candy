<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sanitario_respuestas', function (Blueprint $table) {
            if (!Schema::hasColumn('sanitario_respuestas', 'created_at')) {
                $table->timestamps();
            }
        });
        Schema::table('sanitario_temperaturas', function (Blueprint $table) {
            if (!Schema::hasColumn('sanitario_temperaturas', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        Schema::table('sanitario_respuestas', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('sanitario_temperaturas', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
