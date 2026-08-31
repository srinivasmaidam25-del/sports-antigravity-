<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_booking_amount',
        'max_discount_amount',
        'starts_at',
        'ends_at',
        'usage_limit',
        'times_used',
        'user_limit',
        'facility_restrictions',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'facility_restrictions' => 'array',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'min_booking_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if coupon is valid for a given user, booking amount, and facility.
     */
    public function isValidFor(User $user, float $bookingAmount, int $facilityId): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($now->lt($this->starts_at) || $now->gt($this->ends_at)) {
            return false;
        }

        if ($bookingAmount < $this->min_booking_amount) {
            return false;
        }

        if ($this->usage_limit !== null && $this->times_used >= $this->usage_limit) {
            return false;
        }

        // Check customer specific usage limits
        $userUsageCount = Booking::where('user_id', $user->id)
            ->where('coupon_id', $this->id)
            ->whereIn('status', ['CONFIRMED', 'PENDING'])
            ->count();

        if ($userUsageCount >= $this->user_limit) {
            return false;
        }

        // Check facility restrictions
        if (!empty($this->facility_restrictions)) {
            if (!in_array($facilityId, $this->facility_restrictions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate discount amount.
     */
    public function calculateDiscount(float $amount): float
    {
        if ($this->discount_type === 'PERCENTAGE') {
            $discount = $amount * ($this->discount_value / 100);
            if ($this->max_discount_amount !== null && $discount > $this->max_discount_amount) {
                return (float) $this->max_discount_amount;
            }
            return (float) $discount;
        }

        // Fixed discount
        return min((float) $this->discount_value, $amount);
    }
}
