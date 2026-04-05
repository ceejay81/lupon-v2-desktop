<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pangkat extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    public function luponCase()
    {
        return $this->belongsTo(LuponCase::class);
    }

    public function chairperson()
    {
        return $this->belongsTo(LuponMember::class, 'chairperson_id');
    }

    public function secretary()
    {
        return $this->belongsTo(LuponMember::class, 'secretary_id');
    }

    public function member()
    {
        return $this->belongsTo(LuponMember::class, 'member_id');
    }
}
