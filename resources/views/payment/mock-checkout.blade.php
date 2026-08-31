<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashfree Checkout Simulation - The Cricket Hub</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl overflow-hidden p-6 space-y-6">
        
        <!-- Header -->
        <div class="text-center space-y-2 border-b border-gray-800 pb-4">
            <h1 class="text-2xl font-bold tracking-tight text-emerald-400">THE CRICKET HUB</h1>
            <p class="text-xs uppercase tracking-widest text-gray-400">Cashfree Sandbox Gateway</p>
        </div>

        <!-- Order details -->
        <div class="space-y-4 text-sm">
            <h2 class="text-base font-semibold text-gray-200">Order Summary</h2>
            
            <div class="bg-gray-950 p-4 rounded-xl space-y-3 border border-gray-800/50">
                <div class="flex justify-between">
                    <span class="text-gray-400">Facility:</span>
                    <span class="font-medium text-white">{{ $booking->facility->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Date:</span>
                    <span class="font-medium text-white">{{ $booking->booking_date->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Time:</span>
                    <span class="font-medium text-white">{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-800/80 pt-2 mt-2">
                    <span class="text-gray-400">Total Calculation:</span>
                    <span class="text-gray-400">₹{{ number_format($booking->original_price, 2) }}</span>
                </div>
                @if($booking->discount_amount > 0)
                <div class="flex justify-between text-rose-400">
                    <span>Discount:</span>
                    <span>-₹{{ number_format($booking->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-base font-bold text-emerald-400 border-t border-gray-800/80 pt-2">
                    <span>Amount Due ({{ $booking->payment_type }}):</span>
                    <span>₹{{ number_format($amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Simulation Actions -->
        <div class="space-y-3">
            <p class="text-xs text-center text-gray-500">
                Select an option below to simulate the gateway's API return state.
            </p>

            <form action="{{ route('payment.mock-submit') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="order_id" value="{{ $orderId }}">
                
                <button type="submit" name="action" value="SUCCESS" class="w-full bg-emerald-500 hover:bg-emerald-600 active:scale-[0.99] text-gray-950 font-bold py-3 px-4 rounded-xl transition duration-150 shadow-lg shadow-emerald-500/20">
                    Simulate Successful Payment
                </button>
                
                <button type="submit" name="action" value="FAILED" class="w-full bg-rose-500/10 hover:bg-rose-500/20 active:scale-[0.99] border border-rose-500/30 text-rose-400 font-semibold py-3 px-4 rounded-xl transition duration-150">
                    Simulate Failed Payment
                </button>
            </form>
        </div>

        <!-- Footer terms -->
        <div class="text-center">
            <span class="text-[10px] text-gray-600">
                This is a local simulation route for The Cricket Hub development environment.
            </span>
        </div>

    </div>
</body>
</html>
