<?php

namespace App\Services;

use App\Models\LuponCase;

class SettlementCalculator
{
    /**
     * Calculate the settlement rate of all cases or for a specific year/month.
     * Formula: (Settled Cases / Total Cases) * 100
     */
    public function getRate(?int $year = null, ?int $month = null): int
    {
        $query = LuponCase::query();

        if ($year) {
            $query->whereYear('filed_date', $year);
        }

        if ($month) {
            $query->whereMonth('filed_date', $month);
        }

        $totalCases = $query->count();

        if ($totalCases === 0) {
            return 0;
        }

        $settledCases = (clone $query)->where('status', 'settled')->count();

        return (int) round(($settledCases / $totalCases) * 100);
    }
}
