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

    public function members()
    {
        return $this->belongsToMany(LuponMember::class, 'pangkat_member');
    }
}
