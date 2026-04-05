<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseStatusHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function luponCase()
    {
        return $this->belongsTo(LuponCase::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
