<?php
// database/migrations/2025_10_22_xxxxxx_add_timestamps_to_promociones_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('promociones', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('promociones', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};