<?php

namespace App\Services;

use App\Models\LuponCase;
use Carbon\Carbon;

class CaseNumberGenerator
{
    /**
     * Generate the next case number in the format BP-YYYY-NNN
     */
    public function generate(): string
    {
        $year = Carbon::now()->year;

        // Get the latest case for this year
        $latestCase = LuponCase::whereYear('filed_date', $year)
            ->latest('id')
            ->first();

        if (! $latestCase) {
            $nextNumber = 1;
        } else {
            // Extract the NNN part from BP-YYYY-NNN
            $parts = explode('-', $latestCase->case_number);
            $lastNumber = isset($parts[2]) ? (int) $parts[2] : 0;
            $nextNumber = $lastNumber + 1;
        }

        // Format to exactly 3 digits (e.g., 001, 012, 145)
        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return "BP-{$year}-{$formattedNumber}";
    }
}
