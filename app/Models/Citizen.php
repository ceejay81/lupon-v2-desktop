<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    use HasFactory;

    protected $guarded = [];

    /** Alias for name for template compatibility. */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /** Cases where this citizen is the complainant. */
    public function casesAsComplainant(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(LuponCase::class, 'case_citizens')
            ->wherePivot('role', 'complainant')
            ->withTimestamps();
    }

    /** Cases where this citizen is the respondent. */
    public function casesAsRespondent(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(LuponCase::class, 'case_citizens')
            ->wherePivot('role', 'respondent')
            ->withTimestamps();
    }

    /** All cases this citizen is involved in (either side). */
    public function allCases(): \Illuminate\Support\Collection
    {
        return $this->casesAsComplainant
            ->merge($this->casesAsRespondent)
            ->sortByDesc('filed_date');
    }

    /** Summary of participation. */
    public function getCaseSummaryAttribute(): array
    {
        return [
            'complainant_count' => $this->casesAsComplainant()->count(),
            'respondent_count' => $this->casesAsRespondent()->count(),
            'total_involvement' => $this->casesAsComplainant()->count() + $this->casesAsRespondent()->count(),
            'last_case_date' => $this->allCases()->first()?->filed_date,
        ];
    }
}
