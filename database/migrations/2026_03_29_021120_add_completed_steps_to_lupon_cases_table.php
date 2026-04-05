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
        if (!Schema::hasColumn('lupon_cases', 'completed_steps')) {
            Schema::table('lupon_cases', function (Blueprint $table) {
                $table->json('completed_steps')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('lupon_cases', 'completed_steps')) {
            Schema::table('lupon_cases', function (Blueprint $table) {
                $table->dropColumn('completed_steps');
            });
        }
    }
};
