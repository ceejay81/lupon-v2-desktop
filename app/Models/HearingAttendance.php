<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LuponMember;

class HearingAttendance extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'attended' => 'boolean',
        ];
    }

    public function hearing()
    {
        return $this->belongsTo(Hearing::class);
    }

    public function luponMember(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LuponMember::class);
    }
}
