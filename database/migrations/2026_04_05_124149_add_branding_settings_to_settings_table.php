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
        \DB::table('settings')->insert([
            ['key' => 'is_branding_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'branding', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'barangay_logo_path', 'value' => '', 'type' => 'string', 'group' => 'branding', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('settings')->whereIn('key', ['is_branding_enabled', 'barangay_logo_path'])->delete();
    }
};
