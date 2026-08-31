<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Facility;
use App\Models\User;
use App\Models\Membership;
use App\Models\Setting;
use App\Services\BookingService;
use App\Services\CashfreeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class PaymentController extends Controller
{
    protected $bookingService;
    protected $cashfreeService;

    public function __construct(BookingService $bookingService, CashfreeService $cashfreeService)
    {
        $this->bookingService = $bookingService;
        $this->cashfreeService = $cashfreeService;
    }

    /**
     * Initiate checkout: Reserve slot and redirect to payment gateway.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|integer|exists:facilities,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'payment_type' => 'required|string|in:FULL,ADVANCE,MEMBERSHIP',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'user_membership_id' => 'nullable|integer|exists:user_memberships,id',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            // 1. Reserve slot temporarily with database pessimistic locks
            $booking = $this->bookingService->reserveSlot(
                Auth::id(),
                $request->facility_id,
                $request->booking_date,
                $request->start_time,
                $request->end_time,
                $request->payment_type,
                $request->coupon_code,
                $request->user_membership_id,
                $request->notes
            );

            // 2. If it's fully covered (e.g. by membership hours), confirm immediately
            if ($booking->final_price <= 0) {
                $this->bookingService->confirmBooking($booking->id, 'MEMBERSHIP_' . uniqid());
                return redirect()->route('booking.success', ['id' => $booking->id])
                    ->with('success', 'Booking confirmed successfully using membership hours!');
            }

            // 3. Determine amount to pay (Full vs Advance)
            $amountToPay = $booking->final_price;
            if ($booking->payment_type === 'ADVANCE') {
                $facility = Facility::find($booking->facility_id);
                $advanceSetting = Setting::getVal('advance_payment_' . $facility->slug, 100.00);
                $amountToPay = min($booking->final_price, (float)$advanceSetting);
            }

            // Update booking price details temporarily if paying advance
            if ($booking->payment_type === 'ADVANCE') {
                $booking->notes .= "\n[Advance Payment required: INR " . $amountToPay . "]";
                $booking->save();
            }

            // 4. Create Cashfree Order
            $user = Auth::user();
            $orderParams = [
                'order_id' => 'CHUB_ORDER_' . $booking->id . '_' . time(),
                'amount' => $amountToPay,
                'customer_id' => $user ? (string)$user->id : 'GUEST_' . time(),
                'customer_phone' => $user->phone ?? '9999999999',
                'customer_email' => $user->email ?? 'guest@thecrickethub.com',
            ];

            $orderResponse = $this->cashfreeService->createOrder($orderParams);

            // Save transaction reference link in notes/payment model for debugging
            $booking->notes .= "\nOrder ID: " . $orderResponse['order_id'];
            $booking->save();

            // Redirect user to Cashfree hosted checkout page (or mock screen)
            return redirect()->away($orderResponse['payment_link']);

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Initiate membership checkout.
     */
    public function checkoutMembership(Request $request)
    {
        $request->validate([
            'membership_id' => 'required|integer|exists:memberships,id',
        ]);

        try {
            $user = Auth::user();
            $membership = Membership::findOrFail($request->membership_id);

            // Create Cashfree Order
            $orderId = 'CHUB_MEMB_' . $membership->id . '_' . $user->id . '_' . time();
            $orderParams = [
                'order_id' => $orderId,
                'amount' => $membership->price,
                'customer_id' => (string)$user->id,
                'customer_phone' => $user->phone ?? '9999999999',
                'customer_email' => $user->email ?? 'customer@thecrickethub.com',
            ];

            $orderResponse = $this->cashfreeService->createOrder($orderParams);

            return redirect()->away($orderResponse['payment_link']);

        } catch (Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Cashfree User Redirect Landing Page (Callback).
     */
    public function callback(Request $request)
    {
        $orderId = $request->get('order_id');

        if (!$orderId) {
            return redirect()->route('home')->withErrors(['error' => 'Missing transaction order reference.']);
        }

        try {
            // Query Cashfree status
            $cfOrder = $this->cashfreeService->getOrder($orderId);
            $cfStatus = $cfOrder['order_status'] ?? '';
            
            $parts = explode('_', $orderId);
            
            // Check if this is a membership purchase
            if (isset($parts[1]) && $parts[1] === 'MEMB') {
                $membershipId = (int)$parts[2];
                $userId = (int)$parts[3];
                
                if ($cfStatus === 'PAID') {
                    $this->confirmMembershipPurchase($membershipId, $userId, $orderId, $cfOrder);
                    return redirect()->route('dashboard')->with('success', 'Membership activated successfully!');
                }
                
                return redirect()->route('dashboard')->withErrors(['error' => 'Membership payment failed.']);
            }

            // Else, process standard booking payment
            $bookingId = isset($parts[2]) ? (int)$parts[2] : null;

            if (!$bookingId) {
                throw new Exception("Invalid order ID format received from gateway.");
            }

            $booking = Booking::findOrFail($bookingId);

            // Double check if already confirmed
            if ($booking->status === 'CONFIRMED') {
                return redirect()->route('booking.success', ['id' => $booking->id]);
            }

            if ($cfStatus === 'PAID') {
                $transactionRef = $cfOrder['cf_order_id'] ?? 'REF_' . uniqid();
                $this->bookingService->confirmBooking($booking->id, $transactionRef, $cfOrder);
                return redirect()->route('booking.success', ['id' => $booking->id]);
            }

            return redirect()->route('booking.failed', ['id' => $booking->id])
                ->withErrors(['error' => 'Payment status is: ' . $cfStatus]);

        } catch (Exception $e) {
            Log::error("Payment Callback Processing Error: " . $e->getMessage());
            return redirect()->route('home')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Helper to provision user membership upon confirmation.
     */
    protected function confirmMembershipPurchase(int $membershipId, int $userId, string $orderId, array $gatewayResponse)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($membershipId, $userId, $orderId, $gatewayResponse) {
            // Check if payment was already recorded
            $alreadyPaid = Payment::where('transaction_reference', $orderId)->exists();
            if ($alreadyPaid) {
                return;
            }

            $membership = Membership::findOrFail($membershipId);

            // Expire any existing memberships for this user
            \App\Models\UserMembership::where('user_id', $userId)
                ->where('status', 'ACTIVE')
                ->update(['status' => 'EXPIRED']);

            // Create new user membership
            $userMemb = \App\Models\UserMembership::create([
                'user_id' => $userId,
                'membership_id' => $membershipId,
                'hours_remaining' => $membership->total_hours,
                'status' => 'ACTIVE',
                'expires_at' => now()->addDays(30), // 30-day membership validity
            ]);

            // Save Payment ledger entry
            Payment::create([
                'booking_id' => 0, // 0 signifies a membership purchase rather than facility booking
                'transaction_reference' => $orderId,
                'amount' => $membership->price,
                'gateway' => 'Cashfree',
                'status' => 'SUCCESS',
                'gateway_response' => $gatewayResponse,
            ]);

            Log::info("Activated membership {$membership->name} for user #{$userId}");
            \App\Models\AuditLog::log($userId, 'PURCHASE_MEMBERSHIP', "Purchased membership tier: {$membership->name}");
        });
    }

    /**
     * Cashfree Secure HTTP Webhook.
     */
    public function webhook(Request $request)
    {
        $signature = $request->header('x-cf-signature');
        $timestamp = $request->header('x-cf-timestamp');
        $payload = $request->getContent();

        if (!$signature || !$timestamp) {
            Log::warning('Cashfree Webhook: Missing signature headers');
            return response()->json(['status' => 'missing_headers'], 400);
        }

        // Verify webhook cryptographic origin
        if (!$this->cashfreeService->verifyWebhookSignature($signature, $payload, $timestamp)) {
            Log::warning('Cashfree Webhook: Invalid cryptographic signature detected');
            return response()->json(['status' => 'invalid_signature'], 401);
        }

        try {
            $data = json_decode($payload, true);
            
            $orderData = $data['data']['order'] ?? [];
            $paymentData = $data['data']['payment'] ?? [];
            
            $orderId = $orderData['order_id'] ?? null;
            $paymentStatus = $paymentData['payment_status'] ?? null;
            $transactionRef = $paymentData['cf_payment_id'] ?? 'WEBHOOK_REF_' . uniqid();

            if (!$orderId || !$paymentStatus) {
                return response()->json(['status' => 'incomplete_payload'], 400);
            }

            $parts = explode('_', $orderId);
            
            // Check if membership webhook
            if (isset($parts[1]) && $parts[1] === 'MEMB') {
                $membershipId = (int)$parts[2];
                $userId = (int)$parts[3];
                
                if ($paymentStatus === 'SUCCESS') {
                    $this->confirmMembershipPurchase($membershipId, $userId, $orderId, $data);
                    return response()->json(['status' => 'membership_processed']);
                }
                return response()->json(['status' => 'membership_payment_failed_ignored']);
            }

            $bookingId = isset($parts[2]) ? (int)$parts[2] : null;

            if (!$bookingId) {
                return response()->json(['status' => 'invalid_order_format'], 400);
            }

            if ($paymentStatus === 'SUCCESS') {
                $this->bookingService->confirmBooking($bookingId, $transactionRef, $data);
                return response()->json(['status' => 'processed']);
            }

            Log::info("Cashfree Webhook: Non-success payment status received for Booking #{$bookingId}: {$paymentStatus}");
            return response()->json(['status' => 'not_success_ignored']);

        } catch (Exception $e) {
            Log::error('Cashfree Webhook Processing Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Render the mock checkout screen (Local testing helper).
     */
    public function mockCheckout(Request $request)
    {
        $orderId = $request->get('order_id');
        $parts = explode('_', $orderId);

        // Check if membership checkout
        if (isset($parts[1]) && $parts[1] === 'MEMB') {
            $membershipId = (int)$parts[2];
            $userId = (int)$parts[3];
            $membership = Membership::findOrFail($membershipId);
            $user = User::findOrFail($userId);

            return view('payment.mock-checkout-membership', [
                'membership' => $membership,
                'user' => $user,
                'orderId' => $orderId,
                'amount' => $membership->price,
            ]);
        }

        $bookingId = isset($parts[2]) ? (int)$parts[2] : null;
        $booking = Booking::with('facility', 'user')->findOrFail($bookingId);

        // Get total amount expected
        $amount = $booking->final_price;
        if ($booking->payment_type === 'ADVANCE') {
            $facility = $booking->facility;
            $advanceSetting = Setting::getVal('advance_payment_' . $facility->slug, 100.00);
            $amount = min($booking->final_price, (float)$advanceSetting);
        }

        return view('payment.mock-checkout', [
            'booking' => $booking,
            'orderId' => $orderId,
            'amount' => $amount,
        ]);
    }

    /**
     * Handle mock checkout submission.
     */
    public function mockSubmit(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'action' => 'required|string|in:SUCCESS,FAILED',
        ]);

        $orderId = $request->order_id;
        $action = $request->action;

        // Redirect URL to simulate callback query
        $callbackUrl = route('payment.callback') . "?order_id={$orderId}";

        if ($action === 'SUCCESS') {
            // Trigger a mock webhook call locally in a separate process or process directly
            // For simplicity and 100% reliable local testing, we can directly update it here
            // matching what the callback landing page will do.
            return redirect($callbackUrl);
        }

        // Simulating failed payment redirect
        return redirect($callbackUrl);
    }
}
