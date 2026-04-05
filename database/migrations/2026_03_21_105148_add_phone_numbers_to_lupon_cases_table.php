<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lupon_cases', function (Blueprint $table) {
            $table->string('complainant_phone')->nullable()->after('complainant_address');
            $table->string('respondent_phone')->nullable()->after('respondent_address');
        });
    }

    public function down(): void
    {
        Schema::table('lupon_cases', function (Blueprint $table) {
            $table->dropColumn(['complainant_phone', 'respondent_phone']);
        });
    }
};
