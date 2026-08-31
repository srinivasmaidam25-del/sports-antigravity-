<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'membership_id',
        'hours_remaining',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function usages()
    {
        return $this->hasMany(MembershipUsage::class);
    }

    /**
     * Check if membership is active and has enough remaining hours.
     */
    public function isValid(int $requiredHours = 1): bool
    {
        return $this->status === 'ACTIVE' && 
               $this->expires_at->isAfter(now()) && 
               $this->hours_remaining >= $requiredHours;
    }
}
