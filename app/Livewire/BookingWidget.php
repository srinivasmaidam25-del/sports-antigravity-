<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Facility;
use App\Models\Coupon;
use App\Models\UserMembership;
use App\Models\Setting;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingWidget extends Component
{
    // State properties
    public $facilities;
    public $selectedFacilityId;
    public $selectedDate;
    public $paymentType = 'FULL'; // FULL, ADVANCE, MEMBERSHIP
    public $couponCode;
    public $notes;

    // Computed / Dynamic variables for views
    public $availableSlots = [];
    public $selectedStart;
    public $selectedEnd;
    
    // Pricing details
    public $originalPrice = 0.00;
    public $discountAmount = 0.00;
    public $finalPrice = 0.00;
    
    // Coupons / Membership states
    public $couponError;
    public $couponSuccess;
    public $userMemberships = [];
    public $selectedUserMembershipId;

    protected $bookingService;

    public function boot(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function mount()
    {
        $this->facilities = Facility::where('is_active', true)->get();
        $this->selectedFacilityId = $this->facilities->first()?->id;
        $this->selectedDate = now()->format('Y-m-d');
        
        $this->loadUserMemberships();
        $this->refreshSlotsAndPricing();
    }

    public function loadUserMemberships()
    {
        if (Auth::check()) {
            $this->userMemberships = UserMembership::with('membership')
                ->where('user_id', Auth::id())
                ->where('status', 'ACTIVE')
                ->whereDate('expires_at', '>=', now())
                ->where('hours_remaining', '>', 0)
                ->get();
        } else {
            $this->userMemberships = [];
        }
    }

    /**
     * Triggered when facility, date, or selected slot changes.
     */
    public function updatedSelectedFacilityId()
    {
        $this->selectedStart = null;
        $this->selectedEnd = null;
        $this->refreshSlotsAndPricing();
    }

    public function updatedSelectedDate()
    {
        $this->selectedStart = null;
        $this->selectedEnd = null;
        $this->refreshSlotsAndPricing();
    }

    public function updatedCouponCode()
    {
        $this->validateCoupon();
        $this->calculatePricing();
    }

    public function updatedPaymentType()
    {
        if ($this->paymentType !== 'MEMBERSHIP') {
            $this->selectedUserMembershipId = null;
        } else {
            $this->selectedUserMembershipId = $this->userMemberships->first()?->id;
        }
        $this->calculatePricing();
    }

    public function updatedSelectedUserMembershipId()
    {
        $this->calculatePricing();
    }

    /**
     * Generate possible slots and check their database occupancy.
     */
    public function refreshSlotsAndPricing()
    {
        if (!$this->selectedFacilityId) return;

        $facility = Facility::find($this->selectedFacilityId);
        $durationHours = $facility->duration_minutes / 60;
        
        // 1. Determine operating hours for this date
        $dayOfWeek = Carbon::parse($this->selectedDate)->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
        
        if (in_array($dayOfWeek, [5, 6, 7])) { // Fri - Sun
            $hours = Setting::getVal('operating_hours_fri_sun', ['start' => '06:00', 'end' => '00:00']);
        } else { // Mon - Thu
            $hours = Setting::getVal('operating_hours_mon_thu', ['start' => '06:00', 'end' => '23:00']);
        }

        $startHour = (int)explode(':', $hours['start'])[0];
        $endHour = (int)explode(':', $hours['end'])[0];
        
        // If end hour is 00:00, that is effectively hour 24
        if ($endHour === 0) {
            $endHour = 24;
        }

        $slots = [];
        
        // Loop and build slots
        for ($hour = $startHour; $hour < $endHour; $hour += $durationHours) {
            
            // Format times
            $hStart = str_pad(floor($hour), 2, '0', STR_PAD_LEFT) . ':' . str_pad(($hour - floor($hour)) * 60, 2, '0', STR_PAD_LEFT) . ':00';
            
            $nextHour = $hour + $durationHours;
            if ($nextHour > $endHour) {
                break;
            }
            $hEnd = str_pad(floor($nextHour), 2, '0', STR_PAD_LEFT) . ':' . str_pad(($nextHour - floor($nextHour)) * 60, 2, '0', STR_PAD_LEFT) . ':00';
            
            // Check availability
            $available = $this->bookingService->checkAvailability($facility->id, $this->selectedDate, $hStart, $hEnd);
            
            // Hide past slots if selected date is today
            $isPast = false;
            if ($this->selectedDate === now()->format('Y-m-d')) {
                $slotStartDateTime = Carbon::parse($this->selectedDate . ' ' . $hStart);
                $isPast = $slotStartDateTime->isPast();
            }

            if (!$isPast) {
                $slots[] = [
                    'start' => $hStart,
                    'end' => $hEnd,
                    'label' => Carbon::parse($hStart)->format('h:i A') . ' - ' . Carbon::parse($hEnd)->format('h:i A'),
                    'available' => $available,
                ];
            }
        }

        $this->availableSlots = $slots;
        $this->calculatePricing();
    }

    /**
     * Select a specific slot.
     */
    public function selectSlot(string $start, string $end)
    {
        $this->selectedStart = $start;
        $this->selectedEnd = $end;
        $this->calculatePricing();
    }

    /**
     * Validate the entered coupon.
     */
    public function validateCoupon()
    {
        $this->couponError = null;
        $this->couponSuccess = null;

        if (empty($this->couponCode)) {
            return;
        }

        $coupon = Coupon::where('code', $this->couponCode)->first();

        if (!$coupon) {
            $this->couponError = 'Invalid coupon code.';
            return;
        }

        if (!Auth::check()) {
            $this->couponError = 'You must be logged in to apply coupons.';
            return;
        }

        $facility = Facility::find($this->selectedFacilityId);
        $durationHours = $facility ? ($facility->duration_minutes / 60) : 1;
        $baseHourPrice = $facility ? ($this->bookingService->isWeekend($this->selectedDate, $this->selectedStart ?? '06:00:00') ? $facility->base_price_weekend : $facility->base_price_weekday) : 0;
        $approxPrice = $baseHourPrice * $durationHours;

        if (!$coupon->isValidFor(Auth::user(), $approxPrice, (int)$this->selectedFacilityId)) {
            $this->couponError = 'This coupon is not applicable for this booking.';
            return;
        }

        $this->couponSuccess = 'Coupon applied successfully!';
    }

    /**
     * Execute server-side price calculations.
     */
    public function calculatePricing()
    {
        if (!$this->selectedFacilityId || !$this->selectedStart || !$this->selectedEnd) {
            $this->originalPrice = 0.00;
            $this->discountAmount = 0.00;
            $this->finalPrice = 0.00;
            return;
        }

        $pricing = $this->bookingService->calculatePrice(
            (int)$this->selectedFacilityId,
            $this->selectedDate,
            $this->selectedStart,
            $this->selectedEnd,
            $this->couponSuccess ? $this->couponCode : null,
            $this->paymentType === 'MEMBERSHIP' ? $this->selectedUserMembershipId : null
        );

        $this->originalPrice = $pricing['original_price'];
        $this->discountAmount = $pricing['discount_amount'];
        $this->finalPrice = $pricing['final_price'];
    }

    public function render()
    {
        return view('livewire.booking-widget');
    }
}
