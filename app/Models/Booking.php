<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facility_id',
        'booking_date',
        'start_time',
        'end_time',
        'original_price',
        'discount_amount',
        'final_price',
        'status',
        'reserved_until',
        'payment_type',
        'coupon_id',
        'user_membership_id',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'reserved_until' => 'datetime',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function userMembership()
    {
        return $this->belongsTo(UserMembership::class, 'user_membership_id');
    }
}
