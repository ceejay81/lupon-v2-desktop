<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lupon_cases', function (Blueprint $table) {
            $table->renameColumn('assigned_to', 'filed_by');
        });
    }

    public function down(): void
    {
        Schema::table('lupon_cases', function (Blueprint $table) {
            $table->renameColumn('filed_by', 'assigned_to');
        });
    }
};
