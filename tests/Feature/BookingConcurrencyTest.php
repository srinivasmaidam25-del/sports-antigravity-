<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Facility;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Coupon;
use App\Models\UserMembership;
use App\Models\Membership;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Exception;
use Tests\TestCase;

class BookingConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected $bookingService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->bookingService = new BookingService();

        // Seed basic settings needed for tests
        Setting::setVal('weekday_days', [1, 2, 3, 4]);
        Setting::setVal('friday_weekend_cutoff', '17:00');
        Setting::setVal('weekend_days', [6, 7]);
        Setting::setVal('operating_hours_mon_thu', ['start' => '06:00', 'end' => '23:00']);
        Setting::setVal('operating_hours_fri_sun', ['start' => '06:00', 'end' => '00:00']);
    }

    /**
     * Test double-booking prevention.
     */
    public function test_cannot_double_book_same_slot()
    {
        $user1 = User::factory()->create(['role' => 'customer']);
        $user2 = User::factory()->create(['role' => 'customer']);
        
        $facility = Facility::create([
            'name' => 'Box Cricket',
            'slug' => 'box-cricket',
            'duration_minutes' => 60,
            'base_price_weekday' => 600,
            'base_price_weekend' => 700,
            'is_active' => true,
        ]);

        $date = '2026-09-01'; // Monday
        $startTime = '09:00:00';
        $endTime = '10:00:00';

        // 1. First user reserves the slot
        $booking1 = $this->bookingService->reserveSlot($user1->id, $facility->id, $date, $startTime, $endTime, 'FULL');
        $this->assertNotNull($booking1);
        $this->assertEquals('PENDING', $booking1->status);

        // 2. Second user tries to reserve the exact same slot immediately
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('This slot is already booked or temporarily reserved');

        $this->bookingService->reserveSlot($user2->id, $facility->id, $date, $startTime, $endTime, 'FULL');
    }

    /**
     * Test cross-facility overlapping rules.
     */
    public function test_open_pitch_blocks_box_cricket_and_nets()
    {
        $user = User::factory()->create();

        $openPitch = Facility::create([
            'name' => 'Open Cricket Pitch',
            'slug' => 'open-cricket-pitch',
            'duration_minutes' => 120,
            'base_price_weekday' => 1400,
            'base_price_weekend' => 1600,
            'is_active' => true,
        ]);

        $boxCricket = Facility::create([
            'name' => 'Box Cricket',
            'slug' => 'box-cricket',
            'duration_minutes' => 60,
            'base_price_weekday' => 600,
            'base_price_weekend' => 700,
            'is_active' => true,
        ]);

        $date = '2026-09-01';
        $startTime = '10:00:00';
        $endTime = '12:00:00';

        // 1. Reserve Open Pitch (10:00 - 12:00)
        $bookingOpen = $this->bookingService->reserveSlot($user->id, $openPitch->id, $date, $startTime, $endTime, 'FULL');
        $this->assertNotNull($bookingOpen);

        // 2. Try to reserve Box Cricket for an overlapping slot (11:00 - 12:00)
        $isAvailable = $this->bookingService->checkAvailability($boxCricket->id, $date, '11:00:00', '12:00:00');
        $this->assertFalse($isAvailable, 'Box Cricket should be blocked because Open Pitch is reserved during this time.');
    }

    /**
     * Test that box cricket bookings block open pitch.
     */
    public function test_box_cricket_blocks_open_pitch()
    {
        $user = User::factory()->create();

        $openPitch = Facility::create([
            'name' => 'Open Cricket Pitch',
            'slug' => 'open-cricket-pitch',
            'duration_minutes' => 120,
            'base_price_weekday' => 1400,
            'base_price_weekend' => 1600,
            'is_active' => true,
        ]);

        $boxCricket = Facility::create([
            'name' => 'Box Cricket',
            'slug' => 'box-cricket',
            'duration_minutes' => 60,
            'base_price_weekday' => 600,
            'base_price_weekend' => 700,
            'is_active' => true,
        ]);

        $date = '2026-09-01';

        // 1. Reserve Box Cricket (10:00 - 11:00)
        $bookingBox = $this->bookingService->reserveSlot($user->id, $boxCricket->id, $date, '10:00:00', '11:00:00', 'FULL');
        $this->assertNotNull($bookingBox);

        // 2. Try to reserve Open Pitch for overlapping period (09:00 - 11:00)
        $isAvailable = $this->bookingService->checkAvailability($openPitch->id, $date, '09:00:00', '11:00:00');
        $this->assertFalse($isAvailable, 'Open Pitch should be blocked because Box Cricket has an active booking.');
    }

    /**
     * Test automatic reservation release.
     */
    public function test_release_expired_reservations()
    {
        $user = User::factory()->create();
        $facility = Facility::create([
            'name' => 'Box Cricket',
            'slug' => 'box-cricket',
            'duration_minutes' => 60,
            'base_price_weekday' => 600,
            'base_price_weekend' => 700,
            'is_active' => true,
        ]);

        // Create a booking that expired 1 minute ago
        $booking = Booking::create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'booking_date' => '2026-09-01',
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
            'original_price' => 600,
            'discount_amount' => 0,
            'final_price' => 600,
            'status' => 'PENDING',
            'reserved_until' => now()->subMinutes(1),
            'payment_type' => 'FULL',
        ]);

        $this->assertEquals('PENDING', $booking->status);

        // Run the expiry cleanup
        Artisan::call('bookings:release-expired');

        $booking->refresh();
        $this->assertEquals('EXPIRED', $booking->status);
    }

    /**
     * Test webhook idempotency.
     */
    public function test_webhook_confirmation_is_idempotent()
    {
        $user = User::factory()->create();
        $facility = Facility::create([
            'name' => 'Box Cricket',
            'slug' => 'box-cricket',
            'duration_minutes' => 60,
            'base_price_weekday' => 600,
            'base_price_weekend' => 700,
            'is_active' => true,
        ]);

        $booking = $this->bookingService->reserveSlot($user->id, $facility->id, '2026-09-01', '14:00:00', '15:00:00', 'FULL');
        
        // 1. Confirm first time
        $result1 = $this->bookingService->confirmBooking($booking->id, 'TXN_REF_123');
        $this->assertTrue($result1);
        
        $booking->refresh();
        $this->assertEquals('CONFIRMED', $booking->status);
        $this->assertEquals(1, Payment::where('booking_id', $booking->id)->count());

        // 2. Confirm second time (duplicate webhook hit)
        $result2 = $this->bookingService->confirmBooking($booking->id, 'TXN_REF_123');
        $this->assertTrue($result2);
        
        // Ensure no duplicate payment entry was made
        $this->assertEquals(1, Payment::where('booking_id', $booking->id)->count());
    }

    /**
     * Test server-side price calculation.
     */
    public function test_server_calculates_price_correctly()
    {
        $facility = Facility::create([
            'name' => 'Practice Net 1',
            'slug' => 'practice-net-1',
            'duration_minutes' => 60,
            'base_price_weekday' => 200,
            'base_price_weekend' => 250,
            'is_active' => true,
        ]);

        // Weekday calculation (Mon 10 AM)
        $priceDetails1 = $this->bookingService->calculatePrice($facility->id, '2026-09-01', '10:00:00', '11:00:00');
        $this->assertEquals(200.00, $priceDetails1['final_price']);
        $this->assertFalse($priceDetails1['is_weekend']);

        // Weekend calculation (Sat 10 AM)
        $priceDetails2 = $this->bookingService->calculatePrice($facility->id, '2026-09-05', '10:00:00', '11:00:00');
        $this->assertEquals(250.00, $priceDetails2['final_price']);
        $this->assertTrue($priceDetails2['is_weekend']);

        // Friday Before Cutoff rate (Friday 4 PM)
        $priceDetails3 = $this->bookingService->calculatePrice($facility->id, '2026-09-04', '16:00:00', '17:00:00');
        $this->assertEquals(200.00, $priceDetails3['final_price']);
        $this->assertFalse($priceDetails3['is_weekend']);

        // Friday After Cutoff rate (Friday 6 PM)
        $priceDetails4 = $this->bookingService->calculatePrice($facility->id, '2026-09-04', '18:00:00', '19:00:00');
        $this->assertEquals(250.00, $priceDetails4['final_price']);
        $this->assertTrue($priceDetails4['is_weekend']);
    }
}
