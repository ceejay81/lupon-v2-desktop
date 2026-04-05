<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate old status values to new granular ones
        DB::table('lupon_cases')->where('status', 'pending')->update(['status' => 'filed']);
        DB::table('lupon_cases')->where('status', 'ongoing')->update(['status' => 'under_mediation']);
        DB::table('lupon_cases')->where('status', 'unsettled')->update(['status' => 'certified_to_court']);

        // Add indexes for citizen search performance
        Schema::table('lupon_cases', function (Blueprint $table) {
            $table->index('complainant', 'idx_complainant');
            $table->index('respondent', 'idx_respondent');
            $table->index('status', 'idx_status');
            $table->index('filed_date', 'idx_filed_date');
        });
    }

    public function down(): void
    {
        // Revert status values
        DB::table('lupon_cases')->where('status', 'filed')->update(['status' => 'pending']);
        DB::table('lupon_cases')->where('status', 'under_mediation')->update(['status' => 'ongoing']);
        DB::table('lupon_cases')->where('status', 'under_conciliation')->update(['status' => 'ongoing']);
        DB::table('lupon_cases')->where('status', 'under_arbitration')->update(['status' => 'ongoing']);
        DB::table('lupon_cases')->where('status', 'certified_to_court')->update(['status' => 'unsettled']);

        Schema::table('lupon_cases', function (Blueprint $table) {
            $table->dropIndex('idx_complainant');
            $table->dropIndex('idx_respondent');
            $table->dropIndex('idx_status');
            $table->dropIndex('idx_filed_date');
        });
    }
};
