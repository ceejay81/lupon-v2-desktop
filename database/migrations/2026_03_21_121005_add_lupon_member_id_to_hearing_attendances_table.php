<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hearing_attendances', function (Blueprint $table) {
            $table->foreignId('lupon_member_id')
                ->nullable()
                ->after('name')
                ->constrained('lupon_members')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hearing_attendances', function (Blueprint $table) {
            $table->dropForeign(['lupon_member_id']);
            $table->dropColumn('lupon_member_id');
        });
    }
};
