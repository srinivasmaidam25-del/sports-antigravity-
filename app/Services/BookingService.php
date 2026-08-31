<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\Coupon;
use App\Models\UserMembership;
use App\Models\MembershipUsage;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class BookingService
{
    /**
     * Check if a specific slot is available, considering cross-facility dependencies.
     */
    public function checkAvailability(int $facilityId, string $date, string $startTime, string $endTime): bool
    {
        $facility = Facility::findOrFail($facilityId);

        // Fetch bookings for the date that are CONFIRMED or PENDING (not expired)
        $existingBookings = Booking::whereDate('booking_date', $date)
            ->whereIn('status', ['CONFIRMED', 'PENDING'])
            ->where(function ($query) {
                $query->whereNull('reserved_until')
                      ->orWhere('reserved_until', '>', now()->toDateTimeString());
            })
            ->get();

        foreach ($existingBookings as $booking) {
            // Standard time overlap check: (start_time < new_end && end_time > new_start)
            $overlap = ($booking->start_time < $endTime && $booking->end_time > $startTime);
            if (!$overlap) {
                continue;
            }

            // Scenario 1: Booking is for the exact same facility
            if ($booking->facility_id === $facilityId) {
                return false;
            }

            // Scenario 2: Cross-facility physical overlap
            // 2a. If booking the Open Cricket Pitch, it covers the entire area -> block Box Cricket & practice nets
            if ($facility->slug === 'open-cricket-pitch') {
                return false; // Any overlapping booking on any other facility blocks the Open Pitch
            }

            // 2b. If booking Box Cricket or Practice Nets, they cannot run if the Open Pitch is currently active
            $bookingFacility = Facility::find($booking->facility_id);
            if ($bookingFacility && $bookingFacility->slug === 'open-cricket-pitch') {
                return false;
            }
        }

        return true;
    }

    /**
     * Verify if a date and time is classified as a Weekday or Weekend based on rules.
     */
    public function isWeekend(string $date, string $time): bool
    {
        $carbon = Carbon::parse("$date $time");
        $dayOfWeek = $carbon->dayOfWeekIso; // 1 (Mon) to 7 (Sun)

        $weekendDays = Setting::getVal('weekend_days', [6, 7]); // Default Sat, Sun
        $fridayCutoff = Setting::getVal('friday_weekend_cutoff', '17:00');

        // If it's Friday, check the cutoff time
        if ($dayOfWeek === 5) {
            $cutoff = Carbon::parse("$date $fridayCutoff");
            return $carbon->greaterThanOrEqualTo($cutoff);
        }

        return in_array($dayOfWeek, $weekendDays);
    }

    /**
     * Calculate price for a slot booking dynamically on the server side.
     */
    public function calculatePrice(int $facilityId, string $date, string $startTime, string $endTime, ?string $couponCode = null, ?int $userMembershipId = null): array
    {
        $facility = Facility::findOrFail($facilityId);

        // Determine if it is weekday or weekend rates
        // For a slot bridging the cutoff (e.g. 4 PM to 6 PM on Friday), we price by start time (Option B)
        $isWeekendRate = $this->isWeekend($date, $startTime);
        $baseHourPrice = $isWeekendRate ? $facility->base_price_weekend : $facility->base_price_weekday;

        // Calculate hours duration
        $start = Carbon::parse("$date $startTime");
        $end = Carbon::parse("$date $endTime");
        $durationHours = $start->diffInMinutes($end) / 60.0;

        $originalPrice = $baseHourPrice * $durationHours;
        $discountAmount = 0.00;
        $finalPrice = $originalPrice;
        $coupon = null;
        $membership = null;

        // 1. If using Membership hours
        if ($userMembershipId) {
            $userMembership = UserMembership::findOrFail($userMembershipId);
            $requiredHours = $facility->slug === 'open-cricket-pitch' ? 2 : (int) ceil($durationHours);

            if ($userMembership->isValid($requiredHours)) {
                $discountAmount = $originalPrice; // Membership hours cover the full booking price
                $finalPrice = 0.00;
                $membership = $userMembership;
            }
        }

        // 2. If using Coupon (only applies to unpaid balances)
        if ($finalPrice > 0 && $couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->is_active) {
                // Perform quick validation (min amount check, starts_at, ends_at etc.)
                $now = now();
                if ($now->greaterThanOrEqualTo($coupon->starts_at) && $now->lessThanOrEqualTo($coupon->ends_at)) {
                    if ($originalPrice >= $coupon->min_booking_amount) {
                        $discount = $coupon->calculateDiscount($originalPrice);
                        $discountAmount += $discount;
                        $finalPrice = max(0.00, $originalPrice - $discountAmount);
                    }
                }
            }
        }

        return [
            'original_price' => $originalPrice,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'is_weekend' => $isWeekendRate,
            'coupon_id' => $coupon ? $coupon->id : null,
            'user_membership_id' => $membership ? $membership->id : null,
        ];
    }

    /**
     * Create a temporary pending reservation with a 10-minute hold using database locking.
     */
    public function reserveSlot(?int $userId, int $facilityId, string $date, string $startTime, string $endTime, string $paymentType, ?string $couponCode = null, ?int $userMembershipId = null, ?string $notes = null): Booking
    {
        return DB::transaction(function () use ($userId, $facilityId, $date, $startTime, $endTime, $paymentType, $couponCode, $userMembershipId, $notes) {
            
            // Lock the bookings table for overlaps
            // InnoDB will row-lock any overlapping confirmed/pending bookings
            $overlappingCount = Booking::whereDate('booking_date', $date)
                ->whereIn('status', ['CONFIRMED', 'PENDING'])
                ->where(function ($query) {
                    $query->whereNull('reserved_until')
                          ->orWhere('reserved_until', '>', now()->toDateTimeString());
                })
                ->where(function ($query) use ($startTime, $endTime, $facilityId) {
                    $query->where('facility_id', $facilityId)
                          ->orWhere('facility_id', function ($sub) {
                              $sub->select('id')->from('facilities')->where('slug', 'open-cricket-pitch')->limit(1);
                          });
                })
                ->lockForUpdate()
                ->get();

            // Run structural check inside lock
            if (!$this->checkAvailability($facilityId, $date, $startTime, $endTime)) {
                throw new Exception("This slot is already booked or temporarily reserved by another customer.");
            }

            // Calculate price inside transaction
            $pricing = $this->calculatePrice($facilityId, $date, $startTime, $endTime, $couponCode, $userMembershipId);

            // Create the pending booking record
            $booking = Booking::create([
                'user_id' => $userId,
                'facility_id' => $facilityId,
                'booking_date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'original_price' => $pricing['original_price'],
                'discount_amount' => $pricing['discount_amount'],
                'final_price' => $pricing['final_price'],
                'status' => 'PENDING',
                'reserved_until' => now()->addMinutes(10), // 10 minutes temporary hold
                'payment_type' => $paymentType,
                'coupon_id' => $pricing['coupon_id'],
                'user_membership_id' => $pricing['user_membership_id'],
                'notes' => $notes,
            ]);

            AuditLog::log($userId, 'RESERVE_SLOT', "Reserved facility #{$facilityId} on {$date} at {$startTime}-{$endTime}");

            return $booking;
        });
    }

    /**
     * Confirm a pending booking (called on successful payment callback or webhook).
     */
    public function confirmBooking(int $bookingId, string $transactionRef, array $gatewayResponse = []): bool
    {
        return DB::transaction(function () use ($bookingId, $transactionRef, $gatewayResponse) {
            $booking = Booking::lockForUpdate()->findOrFail($bookingId);

            if ($booking->status === 'CONFIRMED') {
                return true; // Already verified and confirmed
            }

            $now = now();
            // Handle Expired recovery edge-case
            if ($booking->status === 'EXPIRED' || ($booking->status === 'PENDING' && $booking->reserved_until->lt($now))) {
                // Check if the slot is still vacant
                if ($this->checkAvailability($booking->facility_id, $booking->booking_date->format('Y-m-d'), $booking->start_time, $booking->end_time)) {
                    // Reactivate and confirm
                    $booking->status = 'CONFIRMED';
                    $booking->reserved_until = null;
                    $booking->save();

                    $this->createPaymentRecord($booking, $transactionRef, 'SUCCESS', $gatewayResponse);
                    $this->processMembershipDeduction($booking);
                    $this->processReferralRewards($booking);

                    AuditLog::log($booking->user_id, 'PAYMENT_RECOVERY_CONFIRM', "Recovered expired booking #{$booking->id} via post-payment");
                    return true;
                } else {
                    // Slot was taken by another user during the delay
                    $booking->status = 'EXPIRED';
                    $booking->save();

                    // Create payment record with REFUND_REQUIRED status
                    $this->createPaymentRecord($booking, $transactionRef, 'REFUND_REQUIRED', $gatewayResponse);

                    AuditLog::log($booking->user_id, 'PAYMENT_RECOVERY_REFUND', "Double-booking recovery required for expired booking #{$booking->id}");
                    throw new Exception("Slot was booked by another customer. A refund has been queued.");
                }
            }

            // Normal pending confirmation
            $booking->status = 'CONFIRMED';
            $booking->reserved_until = null;
            $booking->save();

            $this->createPaymentRecord($booking, $transactionRef, 'SUCCESS', $gatewayResponse);
            $this->processMembershipDeduction($booking);
            $this->processReferralRewards($booking);

            // Increment coupon use if coupon was applied
            if ($booking->coupon_id) {
                Coupon::where('id', $booking->coupon_id)->increment('times_used');
            }

            AuditLog::log($booking->user_id, 'CONFIRM_BOOKING', "Confirmed booking #{$booking->id} successfully");
            return true;
        });
    }

    /**
     * Helper to create payment history record.
     */
    protected function createPaymentRecord(Booking $booking, string $ref, string $status, array $response): Payment
    {
        return Payment::create([
            'booking_id' => $booking->id,
            'transaction_reference' => $ref,
            'amount' => $booking->final_price,
            'gateway' => 'Cashfree',
            'status' => $status,
            'gateway_response' => $response,
        ]);
    }

    /**
     * Process membership hour deduction on booking confirmation.
     */
    protected function processMembershipDeduction(Booking $booking)
    {
        if ($booking->payment_type === 'MEMBERSHIP' && $booking->user_membership_id) {
            $userMembership = UserMembership::findOrFail($booking->user_membership_id);
            $facility = Facility::find($booking->facility_id);
            
            $start = Carbon::parse($booking->start_time);
            $end = Carbon::parse($booking->end_time);
            $durationHours = $start->diffInMinutes($end) / 60.0;
            
            $hoursToDeduct = $facility->slug === 'open-cricket-pitch' ? 2 : (int) ceil($durationHours);

            if ($userMembership->hours_remaining >= $hoursToDeduct) {
                $userMembership->decrement('hours_remaining', $hoursToDeduct);
                
                MembershipUsage::create([
                    'user_membership_id' => $userMembership->id,
                    'booking_id' => $booking->id,
                    'hours_consumed' => $hoursToDeduct,
                ]);

                if ($userMembership->hours_remaining <= 0) {
                    $userMembership->update(['status' => 'EXPIRED']);
                }
            }
        }
    }

    /**
     * Process referral checks and rewards.
     */
    protected function processReferralRewards(Booking $booking)
    {
        $user = $booking->user;
        if (!$user || !$user->referred_by_id) {
            return;
        }

        // Check if this is the customer's first confirmed booking
        $confirmedCount = Booking::where('user_id', $user->id)
            ->where('status', 'CONFIRMED')
            ->count();

        if ($confirmedCount === 1) {
            // First booking confirmed! Reward the referrer with a coupon or reward
            $referrer = User::find($user->referred_by_id);
            if ($referrer) {
                // Generate a 10% discount referral coupon for the referrer
                $couponCode = 'REF-' . strtoupper(\Illuminate\Support\Str::random(6));
                Coupon::create([
                    'code' => $couponCode,
                    'discount_type' => 'PERCENTAGE',
                    'discount_value' => 10.00,
                    'starts_at' => now(),
                    'ends_at' => now()->addDays(30),
                    'usage_limit' => 1,
                    'user_limit' => 1,
                    'is_active' => true,
                ]);

                AuditLog::log($referrer->id, 'REFERRAL_REWARD', "Earned coupon {$couponCode} for referring user #{$user->id}");
            }
        }
    }

    /**
     * Cleanup and expire bookings that exceeded the 10-minute hold window.
     */
    public function releaseExpiredReservations(): int
    {
        return DB::transaction(function () {
            $expiredBookings = Booking::where('status', 'PENDING')
                ->where('reserved_until', '<', now()->toDateTimeString())
                ->lockForUpdate()
                ->get();

            $count = 0;
            foreach ($expiredBookings as $booking) {
                $booking->status = 'EXPIRED';
                $booking->save();
                $count++;

                AuditLog::log($booking->user_id, 'EXPIRE_RESERVATION', "Released expired slot for booking #{$booking->id}");
            }

            return $count;
        });
    }
}
