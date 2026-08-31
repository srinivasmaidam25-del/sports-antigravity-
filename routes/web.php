<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/build/assets/{file}', function ($file) {
    $path = public_path('build/assets/' . $file);
    if (!file_exists($path)) {
        abort(404);
    }
    $mime = str_ends_with($file, '.css') ? 'text/css; charset=utf-8' : (str_ends_with($file, '.js') ? 'application/javascript; charset=utf-8' : 'application/octet-stream');
    return response()->file($path, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=31536000, immutable'
    ]);
})->where('file', '.*');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Booking Checkout
    Route::post('/booking/checkout', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('payment.checkout');
    
    // Membership Checkout
    Route::post('/membership/checkout', [\App\Http\Controllers\PaymentController::class, 'checkoutMembership'])->name('payment.checkout-membership');
});

// Payment Callback & Webhook
Route::get('/booking/callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');
Route::post('/payment/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('payment.webhook');

// Interest submission
Route::post('/interest', [\App\Http\Controllers\InterestController::class, 'store'])->name('interest.store');

// Mock Checkout Screens (Local Sandbox)
Route::get('/payment/mock-checkout', [\App\Http\Controllers\PaymentController::class, 'mockCheckout'])->name('payment.mock-checkout');
Route::post('/payment/mock-submit', [\App\Http\Controllers\PaymentController::class, 'mockSubmit'])->name('payment.mock-submit');

// Booking Landing Pages
Route::get('/booking/success/{id}', function ($id) {
    $booking = \App\Models\Booking::with('facility')->findOrFail($id);
    return view('booking.success', compact('booking'));
})->name('booking.success');

Route::get('/booking/failed/{id}', function ($id) {
    $booking = \App\Models\Booking::with('facility')->findOrFail($id);
    return view('booking.failed', compact('booking'));
})->name('booking.failed');

// Protected Admin Portal Routes
Route::middleware(['auth', 'role:super_admin,admin,staff'])->group(function () {
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/booking/manual', [\App\Http\Controllers\AdminController::class, 'manualBooking'])->name('admin.booking.manual');
    Route::post('/admin/settings', [\App\Http\Controllers\AdminController::class, 'updateSettings'])->name('admin.settings');
    Route::post('/admin/coupon', [\App\Http\Controllers\AdminController::class, 'createCoupon'])->name('admin.coupon');
    Route::post('/admin/interest/{id}', [\App\Http\Controllers\AdminController::class, 'updateInterest'])->name('admin.interest.update');
    Route::post('/admin/booking/{id}/refund', [\App\Http\Controllers\AdminController::class, 'refundBooking'])->name('admin.booking.refund');
});

require __DIR__.'/auth.php';
