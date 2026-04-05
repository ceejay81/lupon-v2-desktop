<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LuponCase extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    
    protected static function booted(): void
    {
        static::saving(function ($case) {
            if ($case->isDirty('status') && $case->status === 'settled' && is_null($case->settled_at)) {
                $case->settled_at = now();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'filed_date' => 'date',
            'settled_at' => 'datetime',
            'deleted_at' => 'datetime',
            'date_of_service_summon' => 'date',
            'completed_steps' => 'array',
        ];
    }

    /** MOV 2 Document Checklist - Simplified Data Entry Mode. */
    public function getMov2ChecklistAttribute(): array
    {
        $docs = $this->documents->pluck('document_type')->toArray();
        $status = $this->status;
        
        // Smart Result Logic: What document is required for the current status?
        $resultExists = false;
        if ($status === 'settled') {
            $resultExists = in_array('Amicable Settlement (KP Form 16)', $docs) || in_array('Kasabutan', $docs) || in_array('settlement', $docs);
        } elseif ($status === 'certified_to_court') {
            $resultExists = in_array('Certification to File Action (KP Form 20)', $docs) || in_array('certificate_to_court', $docs);
        } else {
            // General result check for other statuses
            $resultExists = in_array('Amicable Settlement (KP Form 16)', $docs) || 
                           in_array('Kasabutan', $docs) || 
                           in_array('Certification to File Action (KP Form 20)', $docs) ||
                           in_array('settlement', $docs) ||
                           in_array('certificate_to_court', $docs);
        }

        return [
            'process' => [
                'label' => 'Notice / Summons',
                'exists' => in_array('notice', $docs) || 
                           in_array('summons', $docs) || 
                           in_array('Notice of Hearing (KP Form 8)', $docs) || 
                           in_array('Summons (KP Form 9)', $docs),
                'icon' => 'ph-envelope-simple',
            ],
            'result' => [
                'label' => $status === 'certified_to_court' ? 'Final Certification' : ($status === 'settled' ? 'Settlement Record' : 'Final Issuance'),
                'exists' => $resultExists,
                'icon' => 'ph-certificate',
            ],
        ];
    }

    /** Total docs completeness percentage. */
    public function getDocsCompletenessAttribute(): int
    {
        $checklist = $this->mov2_checklist;
        $total = count($checklist);
        $filled = collect($checklist)->where('exists', true)->count();

        return $total > 0 ? (int) round(($filled / $total) * 100) : 0;
    }

    /** Case statistics for the dashboard. */
    public function getMetricsAttribute(): array
    {
        return [
            'total_hearings'  => $this->hearings->count(),
            'total_documents' => $this->documents->count(),
            'has_pangkat'     => $this->pangkats->isNotEmpty(),
            'is_overdue'      => $this->filed_date && \Illuminate\Support\Carbon::parse($this->filed_date)->diffInDays(now()) > 30 && !in_array($this->status, ['settled', 'certified_to_court', 'dismissed']),
        ];
    }

    /** Human-readable status label. */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'filed'              => 'Filed',
            'under_mediation'    => 'Under Mediation',
            'under_conciliation' => 'Under Conciliation',
            'under_arbitration'  => 'Under Arbitration',
            'settled'            => 'Settled',
            'certified_to_court' => 'Certified to Court',
            'dismissed'          => 'Dismissed',
            'archived'           => 'Archived',
            default              => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /** CSS badge colour classes for the status. */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'filed'              => 'badge-filed',
            'under_mediation'    => 'badge-mediation',
            'under_conciliation' => 'badge-conciliation',
            'under_arbitration'  => 'badge-arbitration',
            'settled'            => 'badge-settled',
            'certified_to_court' => 'badge-court',
            'dismissed'          => 'badge-dismissed',
            'archived'           => 'badge-archived',
            default              => 'badge-filed',
        };
    }

    /** Blotter-specific computed properties */
    public function getDateOfFirstHearingAttribute(): ?\Carbon\Carbon
    {
        return $this->hearings()->orderBy('scheduled_at')->first()?->scheduled_at;
    }

    public function getDateOfSettlementAttribute(): ?\Carbon\Carbon
    {
        return $this->settled_at;
    }

    public function getAmicablySettledAttribute(): bool
    {
        return $this->status === 'settled';
    }

    public function getMediationAttribute(): bool
    {
        $pastMediationStatuses = [
            'under_conciliation', 'under_arbitration', 'settled', 
            'certified_to_court', 'dismissed'
        ];

        return in_array($this->status, $pastMediationStatuses) || 
               $this->status === 'under_mediation' ||
               $this->hearings()->where('hearing_type', 'mediation')->where('status', 'completed')->exists();
    }

    public function getConciliationAttribute(): bool
    {
        $pastConciliationStatuses = [
            'under_arbitration', 'settled', 'certified_to_court', 'dismissed'
        ];

        return in_array($this->status, $pastConciliationStatuses) || 
               $this->status === 'under_conciliation' ||
               $this->hearings()->where('hearing_type', 'conciliation')->where('status', 'completed')->exists();
    }

    public function getNoOfDaysInBarangayAttribute(): ?int
    {
        $endDate = $this->settled_at ?? now();

        return \Illuminate\Support\Carbon::parse($this->filed_date)->diffInDays($endDate);
    }

    /** @return array{state: string, label: string, days_remaining: int|null, days_overdue: int|null} */
    public function getSlaDataAttribute(): array
    {
        if ($this->filed_date === null) {
            return ['state' => 'on_track', 'label' => 'On Track', 'days_remaining' => null, 'days_overdue' => null];
        }

        $terminalStatuses = ['settled', 'certified_to_court', 'dismissed', 'archived'];
        $postMediationStatuses = ['under_conciliation', 'under_arbitration'];

        if (in_array($this->status, $terminalStatuses)) {
            return ['state' => 'resolved', 'label' => 'Resolved', 'days_remaining' => null, 'days_overdue' => null];
        }

        if (in_array($this->status, $postMediationStatuses)) {
            return ['state' => 'post_mediation', 'label' => 'Post-Mediation', 'days_remaining' => null, 'days_overdue' => null];
        }

        $daysActive = $this->no_of_days_in_barangay;

        if ($daysActive <= 25) {
            return ['state' => 'on_track', 'label' => 'On Track', 'days_remaining' => 30 - $daysActive, 'days_overdue' => null];
        }

        if ($daysActive <= 30) {
            return ['state' => 'approaching', 'label' => 'Approaching Deadline', 'days_remaining' => 30 - $daysActive, 'days_overdue' => null];
        }

        return ['state' => 'overdue', 'label' => 'Overdue', 'days_remaining' => null, 'days_overdue' => $daysActive - 30];
    }

    public function getPercentageOfComplianceAttribute(): float
    {
        return (float) $this->docs_completeness;
    }

    public function complainantCitizen(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'complainant_id');
    }

    public function respondentCitizen(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Citizen::class, 'respondent_id');
    }

    public function filedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'filed_by');
    }

    public function hearings()
    {
        return $this->hasMany(Hearing::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(CaseStatusHistory::class);
    }

    public function pangkats()
    {
        return $this->hasMany(Pangkat::class);
    }
}
