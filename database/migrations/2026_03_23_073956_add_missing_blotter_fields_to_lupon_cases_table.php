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
        Schema::table('lupon_cases', function (Blueprint $table) {
            // Only add truly missing fields that aren't already covered
            $table->date('date_of_service_summon')->nullable()->after('filed_date');
            $table->text('remarks')->nullable()->after('description'); // Separate from description for general remarks
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lupon_cases', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_service_summon',
                'remarks'
            ]);
        });
    }
};