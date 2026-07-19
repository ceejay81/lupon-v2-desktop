<?php

namespace App\Services;

use App\Models\LuponCase;
use Carbon\Carbon;

class CaseNumberGenerator
{
    /**
     * Generate the next case number in the format BP-YYYY-NNN.
     *
     * Scopes by case_number pattern (not filed_date) to prevent sequence gaps
     * when a case's filed_date is set to a different year.
     */
    public function generate(): string
    {
        $year = Carbon::now()->year;

        // Find the highest-numbered case for this year by matching the case_number prefix
        $latestCase = LuponCase::where('case_number', 'like', "BP-{$year}-%")
            ->orderByRaw('CAST(SUBSTR(case_number, -3) AS INTEGER) DESC')
            ->first();

        if (! $latestCase) {
            $nextNumber = 1;
        } else {
            $parts = explode('-', $latestCase->case_number);
            $lastNumber = isset($parts[2]) ? (int) $parts[2] : 0;
            $nextNumber = $lastNumber + 1;
        }

        // Format to exactly 3 digits (e.g., 001, 012, 145)
        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return "BP-{$year}-{$formattedNumber}";
    }
}
