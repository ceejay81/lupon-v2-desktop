<?php

namespace App\Services;

use App\Models\LuponCase;

class DocumentOrganizer
{
    /**
     * Define the 4 conditionally required document types for MOV 2 completeness.
     */
    const REQUIRED_TYPES = [
        'notice',
        'minutes',
        'attendance',
        'certificate' // This could be agreement or certificate to file action
    ];

    /**
     * Calculate document completeness for a given case.
     * 
     * @return array{percentage: int, missing: array<string>}
     */
    public function getCompleteness(LuponCase $case): array
    {
        // Get all document types attached to this case
        $uploadedTypes = $case->documents()->pluck('document_type')->unique()->toArray();

        $missingTypes = [];
        $matchedCount = 0;

        foreach (self::REQUIRED_TYPES as $required) {
            if (in_array($required, $uploadedTypes)) {
                $matchedCount++;
            } else {
                $missingTypes[] = $required;
            }
        }

        $totalRequired = count(self::REQUIRED_TYPES);
        $percentage = $totalRequired > 0 ? (int) round(($matchedCount / $totalRequired) * 100) : 0;

        return [
            'percentage' => $percentage,
            'missing' => $missingTypes,
        ];
    }
}
