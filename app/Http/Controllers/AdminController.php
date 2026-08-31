<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Facility;
use App\Models\Membership;
use App\Models\Setting;
use App\Models\Coupon;
use App\Models\Interest;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class AdminController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Render the admin dashboard with stats and tables.
     */
    public function dashboard(Request $request)
    {
        // 1. Core Revenue and Occupancy Statistics
        $totalRevenue = Payment::where('status', 'SUCCESS')->sum('amount');
        $confirmedBookingsCount = Booking::where('status', 'CONFIRMED')->count();
        $totalInterestsCount = Interest::count();
        $newInterestsCount = Interest::where('status', 'NEW')->count();

        // 2. Load lists
        $bookings = Booking::with('facility', 'user')->orderBy('booking_date', 'desc')->orderBy('start_time', 'desc')->get();
        $payments = Payment::with('booking')->orderBy('created_at', 'desc')->get();
        $facilities = Facility::all();
        $memberships = Membership::all();
        $coupons = Coupon::orderBy('created_at', 'desc')->get();
        $interests = Interest::with('facility')->orderBy('created_at', 'desc')->get();
        $auditLogs = AuditLog::with('user')->orderBy('created_at', 'desc')->take(200)->get();
        
        $settings = [
            'friday_weekend_cutoff' => Setting::getVal('friday_weekend_cutoff', '17:00'),
            'referral_discount_percentage' => Setting::getVal('referral_discount_percentage', '10.00'),
            'operating_hours_mon_thu' => Setting::getVal('operating_hours_mon_thu', ['start' => '06:00', 'end' => '23:00']),
            'operating_hours_fri_sun' => Setting::getVal('operating_hours_fri_sun', ['start' => '06:00', 'end' => '00:00']),
        ];

        return view('admin.dashboard', compact(
            'totalRevenue',
            'confirmedBookingsCount',
            'totalInterestsCount',
            'newInterestsCount',
            'bookings',
            'payments',
            'facilities',
            'memberships',
            'coupons',
            'interests',
            'auditLogs',
            'settings'
        ));
    }

    /**
     * Staff/Admin creates a manual confirmed booking for walk-in clients.
     */
    public function manualBooking(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:15',
            'facility_id' => 'required|integer|exists:facilities,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Ensure slot is available
                $available = $this->bookingService->checkAvailability(
                    $request->facility_id,
                    $request->booking_date,
                    $request->start_time,
                    $request->end_time
                );

                if (!$available) {
                    throw new Exception("This slot is already booked and cannot be booked manually.");
                }

                // Check if user exists, else create a guest-type client profile
                $user = User::where('phone', $request->customer_phone)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $request->customer_name,
                        'email' => 'guest_' . time() . '@thecrickethub.com',
                        'phone' => $request->customer_phone,
                        'password' => bcrypt(\Illuminate\Support\Str::random(12)),
                        'role' => 'customer',
                    ]);
                }

                // Calculate price
                $pricing = $this->bookingService->calculatePrice(
                    $request->facility_id,
                    $request->booking_date,
                    $request->start_time,
                    $request->end_time
                );

                // Create CONFIRMED booking immediately
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'facility_id' => $request->facility_id,
                    'booking_date' => $request->booking_date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'original_price' => $pricing['original_price'],
                    'discount_amount' => 0.00,
                    'final_price' => $pricing['original_price'],
                    'status' => 'CONFIRMED',
                    'reserved_until' => null,
                    'payment_type' => 'FULL',
                    'notes' => '[Manual Walk-in Booking] ' . $request->notes,
                ]);

                // Create cash payment record
                Payment::create([
                    'booking_id' => $booking->id,
                    'transaction_reference' => 'MANUAL_CASH_' . uniqid(),
                    'amount' => $pricing['original_price'],
                    'gateway' => 'Cash/Counter',
                    'status' => 'SUCCESS',
                    'gateway_response' => ['operator_id' => Auth::id()],
                ]);

                AuditLog::log(Auth::id(), 'MANUAL_BOOKING', "Created manual booking #{$booking->id} for {$request->customer_name}");
            });

            return redirect()->back()->with('success', 'Manual walk-in booking logged successfully!');

        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update dynamic settings (Admin / Super Admin only).
     */
    public function updateSettings(Request $request)
    {
        // Restrict Staff from updates
        if (Auth::user()->isStaff()) {
            abort(403, 'Unauthorized settings modification.');
        }

        $request->validate([
            'friday_weekend_cutoff' => 'required|string',
            'referral_discount_percentage' => 'required|numeric|min:0|max:100',
            'operating_hours_mon_thu_start' => 'required|string',
            'operating_hours_mon_thu_end' => 'required|string',
            'operating_hours_fri_sun_start' => 'required|string',
            'operating_hours_fri_sun_end' => 'required|string',
            'facility_prices' => 'required|array',
            'facility_prices.*.weekday' => 'required|numeric|min:0',
            'facility_prices.*.weekend' => 'required|numeric|min:0',
        ]);

        // Update basic business rules
        Setting::setVal('friday_weekend_cutoff', $request->friday_weekend_cutoff);
        Setting::setVal('referral_discount_percentage', $request->referral_discount_percentage);

        // Update operating hours
        Setting::setVal('operating_hours_mon_thu', [
            'start' => $request->operating_hours_mon_thu_start,
            'end' => $request->operating_hours_mon_thu_end,
        ]);
        Setting::setVal('operating_hours_fri_sun', [
            'start' => $request->operating_hours_fri_sun_start,
            'end' => $request->operating_hours_fri_sun_end,
        ]);

        // Update facility prices
        foreach ($request->facility_prices as $facilityId => $prices) {
            $facility = Facility::findOrFail($facilityId);
            $facility->base_price_weekday = $prices['weekday'];
            $facility->base_price_weekend = $prices['weekend'];
            $facility->save();
        }

        AuditLog::log(Auth::id(), 'UPDATE_SETTINGS', 'Modified operational timings, business parameters, and facility pricing structures');

        return redirect()->back()->with('success', 'Operational settings and facility prices updated successfully!');
    }

    /**
     * Create a new Coupon (Admin / Super Admin only).
     */
    public function createCoupon(Request $request)
    {
        if (Auth::user()->isStaff()) {
            abort(403, 'Unauthorized coupon modification.');
        }

        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'discount_type' => 'required|string|in:FIXED,PERCENTAGE',
            'discount_value' => 'required|numeric|min:1',
            'min_booking_amount' => 'required|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        Coupon::create([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_booking_amount' => $request->min_booking_amount,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'usage_limit' => $request->usage_limit,
            'user_limit' => 1,
            'is_active' => true,
        ]);

        AuditLog::log(Auth::id(), 'CREATE_COUPON', "Created discount coupon code: {$request->code}");

        return redirect()->back()->with('success', 'Promo coupon code added successfully!');
    }

    /**
     * Update lead interest follow-up statuses.
     */
    public function updateInterest(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:NEW,CONTACTED,CONVERTED,CLOSED',
        ]);

        $interest = Interest::findOrFail($id);
        $oldStatus = $interest->status;
        $interest->status = $request->status;
        $interest->save();

        AuditLog::log(Auth::id(), 'UPDATE_INTEREST', "Modified interest lead #{$id} status from {$oldStatus} to {$request->status}");

        return redirect()->back()->with('success', 'Lead follow-up status updated.');
    }

    /**
     * Trigger a mock booking refund (Admin / Super Admin only).
     */
    public function refundBooking(Request $request, $id)
    {
        if (Auth::user()->isStaff()) {
            abort(403, 'Unauthorized refund access.');
        }

        try {
            DB::transaction(function () use ($id) {
                $booking = Booking::findOrFail($id);
                
                // Fetch associated payment
                $payment = Payment::where('booking_id', $booking->id)->first();
                if (!$payment || $payment->status !== 'SUCCESS') {
                    throw new Exception("No active successful payment records exist for this booking.");
                }

                // Change payment status to REFUNDED
                $payment->status = 'REFUNDED';
                $payment->save();

                // Change booking status to CANCELLED
                $booking->status = 'CANCELLED';
                $booking->save();

                AuditLog::log(Auth::id(), 'REFUND_BOOKING', "Refunded payment and cancelled booking #{$booking->id}");
            });

            return redirect()->back()->with('success', 'Booking refund processed successfully!');

        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
