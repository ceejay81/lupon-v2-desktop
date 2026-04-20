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
        // case_citizens table - composite index for faster joins
        Schema::table('case_citizens', function (Blueprint $table) {
            $table->index(['lupon_case_id', 'citizen_id', 'role'], 'idx_case_citizens_lookup');
            $table->index('citizen_id', 'idx_citizen_cases');
        });

        // documents table - index for case document lookups
        Schema::table('documents', function (Blueprint $table) {
            $table->index(['lupon_case_id', 'document_type'], 'idx_documents_case_type');
            $table->index('uploaded_by', 'idx_documents_uploader');
        });

        // hearings table - index for upcoming hearings queries
        Schema::table('hearings', function (Blueprint $table) {
            $table->index(['lupon_case_id', 'scheduled_at', 'status'], 'idx_hearings_case_schedule');
            $table->index(['scheduled_at', 'status'], 'idx_hearings_upcoming');
        });

        // case_status_histories table - index for audit trails
        Schema::table('case_status_histories', function (Blueprint $table) {
            $table->index(['lupon_case_id', 'created_at'], 'idx_status_history_case');
        });

        // pangkats table - index for pangkat assignments
        Schema::table('pangkats', function (Blueprint $table) {
            $table->index('lupon_case_id', 'idx_pangkats_case');
        });

        // reports table - index for monthly report lookups
        Schema::table('reports', function (Blueprint $table) {
            $table->index(['year', 'month', 'type'], 'idx_reports_period');
            $table->index('lupon_case_id', 'idx_reports_case');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_citizens', function (Blueprint $table) {
            $table->dropIndex('idx_case_citizens_lookup');
            $table->dropIndex('idx_citizen_cases');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('idx_documents_case_type');
            $table->dropIndex('idx_documents_uploader');
        });

        Schema::table('hearings', function (Blueprint $table) {
            $table->dropIndex('idx_hearings_case_schedule');
            $table->dropIndex('idx_hearings_upcoming');
        });

        Schema::table('case_status_histories', function (Blueprint $table) {
            $table->dropIndex('idx_status_history_case');
        });

        Schema::table('pangkats', function (Blueprint $table) {
            $table->dropIndex('idx_pangkats_case');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('idx_reports_period');
            $table->dropIndex('idx_reports_case');
        });
    }
};
