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
        Schema::create('case_citizens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lupon_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('citizen_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['complainant', 'respondent']);
            $table->timestamps();

            // Ensure a citizen is only added to a specific role in a case once
            $table->unique(['lupon_case_id', 'citizen_id', 'role']);
        });

        // Migrate historical data
        $cases = \Illuminate\Support\Facades\DB::table('lupon_cases')->get();
        foreach ($cases as $case) {
            if ($case->complainant_id) {
                \Illuminate\Support\Facades\DB::table('case_citizens')->insertOrIgnore([
                    'lupon_case_id' => $case->id,
                    'citizen_id' => $case->complainant_id,
                    'role' => 'complainant',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($case->respondent_id) {
                \Illuminate\Support\Facades\DB::table('case_citizens')->insertOrIgnore([
                    'lupon_case_id' => $case->id,
                    'citizen_id' => $case->respondent_id,
                    'role' => 'respondent',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_citizens');
    }
};
