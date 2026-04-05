<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('backup_code')->nullable()->after('password');
            $table->timestamp('backup_code_generated_at')->nullable()->after('backup_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['backup_code', 'backup_code_generated_at']);
        });
    }
};
