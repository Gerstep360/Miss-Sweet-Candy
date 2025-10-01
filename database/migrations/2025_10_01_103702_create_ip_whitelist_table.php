<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ip_whitelist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->string('ip_cidr', 43);
            
            $table->index('user_id', 'idx_ipw_user');
        });

        // Renombrar constraint FK con nombre explícito
        Schema::table('ip_whitelist', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            
            $table->foreign('user_id', 'fk_ipw_user')
                  ->references('id')->on('users')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ip_whitelist');
    }
};