<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hearing extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    public function luponCase()
    {
        return $this->belongsTo(LuponCase::class);
    }

    public function attendances()
    {
        return $this->hasMany(HearingAttendance::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
