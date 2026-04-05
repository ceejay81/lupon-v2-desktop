<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN for enums; status is stored as string,
        // so we just migrate any existing values that need remapping and the new
        // values ('confirmed', 'postponed') are now valid by convention.

        // No existing rows need remapping — just document the expanded set.
        // Validation is enforced at the application layer (Livewire/Form Requests).
    }

    public function down(): void
    {
        // Revert any 'confirmed' or 'postponed' rows back to nearest equivalent
        DB::table('hearings')->where('status', 'confirmed')->update(['status' => 'scheduled']);
        DB::table('hearings')->where('status', 'postponed')->update(['status' => 'cancelled']);
    }
};
