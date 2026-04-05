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
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'report_type')) {
                $table->renameColumn('report_type', 'type');
            }
            if (!Schema::hasColumn('reports', 'content')) {
                $table->longText('content')->nullable()->after('type'); // use the new name
            }
        });

        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'content')) {
                $table->longText('content')->nullable()->after('uploaded_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'type')) {
                $table->renameColumn('type', 'report_type');
            }
            if (Schema::hasColumn('reports', 'content')) {
                $table->dropColumn('content');
            }
        });

        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'content')) {
                $table->dropColumn('content');
            }
        });
    }
};
