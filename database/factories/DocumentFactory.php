<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement([
            'Complaint',
            'Notice of Hearing',
            'Summons',
            'Invitation Notice',
            'Amicable Settlement',
            'Certification to File Action',
            'Minutes of Hearing',
            'Attendance Sheet',
            'Affidavit',
            'Evidence / Exhibit',
        ]);

        return [
            'lupon_case_id' => null,
            'hearing_id' => null,
            'document_type' => $type,
            'filename' => str_replace(['/', ' '], '-', $type).'.pdf',
            'file_path' => 'cases/0/uploads/'.fake()->uuid().'.pdf',
            'file_size' => fake()->numberBetween(50000, 5000000),
            'mime_type' => 'application/pdf',
            'remarks' => fake()->optional(0.3)->sentence(),
            'uploaded_by' => null,
            'content' => null,
        ];
    }
}
