<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * List of tables to truncate (in order to respect foreign key constraints).
     */
    private array $tablesToTruncate = [
        'hearing_attendances',
        'hearings',
        'documents',
        'reports',
        'case_status_histories',
        'pangkats',
        'lupon_members',
        'case_citizens',
        'assessment_items',
        'archived_cases',
        'lupon_cases',
        'citizens',
        'jobs',
        'failed_jobs',
        'job_batches',
        'cache',
        'cache_locks',
        'sessions',
    ];

    /**
     * Run the migration: Truncate all data except admin user.
     */
    public function up(): void
    {
        // Disable foreign key checks for SQLite
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        }

        // Store admin user data before truncating
        $adminUser = DB::table('users')->orderBy('id')->first();

        // Truncate all data tables
        foreach ($this->tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // Clear personal_access_tokens if exists
        if (Schema::hasTable('personal_access_tokens')) {
            DB::table('personal_access_tokens')->truncate();
        }

        // Clear password_reset_tokens if exists
        if (Schema::hasTable('password_reset_tokens')) {
            DB::table('password_reset_tokens')->truncate();
        }

        // Restore admin user if existed
        if ($adminUser) {
            DB::table('users')->truncate();
            DB::table('users')->insert([
                'id' => 1,
                'name' => $adminUser->name,
                'email' => $adminUser->email,
                'password' => $adminUser->password,
                'remember_token' => $adminUser->remember_token ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Re-enable foreign key checks
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        }

        // Reset sequences for SQLite
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("DELETE FROM sqlite_sequence WHERE name='users'");
            DB::statement("INSERT INTO sqlite_sequence(name, seq) VALUES('users', 1)");
        }
    }

    /**
     * Reverse the migration (cannot restore deleted data).
     */
    public function down(): void
    {
        // This migration cannot be reversed as data is permanently deleted.
        // To restore, you would need a database backup.
    }
};
