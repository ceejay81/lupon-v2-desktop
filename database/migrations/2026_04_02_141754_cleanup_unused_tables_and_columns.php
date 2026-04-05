<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop unused tables (no model, no code references)
        Schema::dropIfExists('assessment_items');
        Schema::dropIfExists('archived_cases');

        // Drop unused backup_code columns (recovery UI was removed)
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['backup_code', 'backup_code_generated_at']);
        });
    }

    public function down(): void
    {
        Schema::create('assessment_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('item_name');
            $table->string('section');
            $table->integer('mov_number');
            $table->string('status')->default('pending');
            $table->integer('completion_percentage')->default(0);
            $table->timestamps();
        });

        Schema::create('archived_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lupon_case_id')->constrained()->cascadeOnDelete();
            $table->json('case_data');
            $table->string('archive_reason');
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('restored_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('restored_at')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('backup_code')->nullable()->after('password');
            $table->timestamp('backup_code_generated_at')->nullable()->after('backup_code');
        });
    }
};
