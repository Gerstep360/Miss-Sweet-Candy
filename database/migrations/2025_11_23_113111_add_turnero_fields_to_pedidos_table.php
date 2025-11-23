<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {

            if (!Schema::hasColumn('pedidos', 'token')) {
                $table->string('token', 20)->unique()->nullable()->after('canal');
            }

            if (!Schema::hasColumn('pedidos', 'eta_minutes')) {
                $table->unsignedInteger('eta_minutes')->nullable()->after('token');
            }

            if (!Schema::hasColumn('pedidos', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('eta_minutes');
            }

            if (!Schema::hasColumn('pedidos', 'ready_at')) {
                $table->timestamp('ready_at')->nullable()->after('started_at');
            }

            if (!Schema::hasColumn('pedidos', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('ready_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos', 'token')) $table->dropColumn('token');
            if (Schema::hasColumn('pedidos', 'eta_minutes')) $table->dropColumn('eta_minutes');
            if (Schema::hasColumn('pedidos', 'started_at')) $table->dropColumn('started_at');
            if (Schema::hasColumn('pedidos', 'ready_at')) $table->dropColumn('ready_at');
            if (Schema::hasColumn('pedidos', 'delivered_at')) $table->dropColumn('delivered_at');
        });
    }
};
