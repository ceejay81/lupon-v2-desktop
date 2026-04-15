<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function luponCase()
    {
        return $this->belongsTo(LuponCase::class);
    }

    public function hearing()
    {
        return $this->belongsTo(Hearing::class);
    }

    public function getViewerUrlAttribute(): string
    {
        if ($this->file_path !== 'digital_record') {
            return route('media.show', ['path' => $this->file_path]);
        }

        // Mapping types to their editor routes
        $routes = [
            'Status of Case' => 'cases.export.status-of-case',
            'Invitation Notice' => 'cases.export.invitation-notice',
            'Notice of Hearing (KP Form 8)' => 'cases.export.notice-of-hearing',
            'Summons (KP Form 9)' => 'cases.export.summon',
            'Amicable Settlement (KP Form 16)' => 'cases.export.amicable-settlement',
            'Kasabutan' => 'cases.export.kasabutan',
            'Certification to File Action (KP Form 20)' => 'cases.export.certificate-to-file-action',
        ];

        if (isset($routes[$this->document_type])) {
            return route($routes[$this->document_type], $this->lupon_case_id);
        }

        if ($this->document_type === 'Minutes of Hearing' && $this->hearing_id) {
            return route('hearings.show', $this->hearing_id);
        }

        return '#';
    }
}
