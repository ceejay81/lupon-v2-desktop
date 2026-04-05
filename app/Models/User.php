<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function filedCases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LuponCase::class, 'filed_by');
    }

    public function isAdmin(): bool
    {
        return $this->email === 'admin@bula.gov.ph';
    }
}
