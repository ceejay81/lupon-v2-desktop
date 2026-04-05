<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lupon_cases', function (Blueprint $table) {
            $table->string('complainant_address')->nullable()->after('complainant');
            $table->string('respondent_address')->nullable()->after('respondent');
            $table->text('description')->nullable()->after('nature_of_case');
        });
    }

    public function down(): void
    {
        Schema::table('lupon_cases', function (Blueprint $table) {
            $table->dropColumn(['complainant_address', 'respondent_address', 'description']);
        });
    }
};
