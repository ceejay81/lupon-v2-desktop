<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lupon_cases', function (Blueprint $table) {
            $table->foreignId('complainant_id')
                ->nullable()
                ->after('complainant_phone')
                ->constrained('citizens')
                ->nullOnDelete();

            $table->foreignId('respondent_id')
                ->nullable()
                ->after('respondent_phone')
                ->constrained('citizens')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lupon_cases', function (Blueprint $table) {
            $table->dropForeign(['complainant_id']);
            $table->dropForeign(['respondent_id']);
            $table->dropColumn(['complainant_id', 'respondent_id']);
        });
    }
};
