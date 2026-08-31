<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_membership_id',
        'booking_id',
        'hours_consumed',
    ];

    public function userMembership()
    {
        return $this->belongsTo(UserMembership::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
