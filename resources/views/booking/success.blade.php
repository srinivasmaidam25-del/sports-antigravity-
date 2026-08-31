<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - The Cricket Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl overflow-hidden p-6 space-y-6 text-center">
        
        <!-- Success Icon -->
        <div class="mx-auto w-16 h-16 bg-emerald-500/10 border border-emerald-500/20 rounded-full flex items-center justify-center text-emerald-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-bold tracking-tight text-white">Booking Confirmed!</h1>
            <p class="text-sm text-gray-400">Your slot is locked in. Get ready to play.</p>
        </div>

        <!-- Ticket Details -->
        <div class="bg-gray-950 p-4 rounded-xl space-y-3 border border-gray-800/50 text-left text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Booking ID:</span>
                <span class="font-mono text-emerald-400">#CHUB-{{ $booking->id }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Facility:</span>
                <span class="font-semibold text-white">{{ $booking->facility->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Date:</span>
                <span class="text-white">{{ $booking->booking_date->format('l, d M Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Time Slot:</span>
                <span class="text-white">{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-800/80 pt-2 mt-2">
                <span class="text-gray-500">Payment Type:</span>
                <span class="text-white font-medium">{{ $booking->payment_type }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Total Price:</span>
                <span class="text-white font-semibold">₹{{ number_format($booking->final_price, 2) }}</span>
            </div>
        </div>

        <!-- Instructions -->
        <div class="text-xs text-gray-500 space-y-1">
            <p>Please arrive 10 minutes before your slot time.</p>
            <p>Ensure you are carrying proper sports footwear.</p>
        </div>

        <!-- CTA -->
        <div class="pt-4 border-t border-gray-800">
            <a href="{{ route('dashboard') }}" class="inline-block w-full bg-emerald-500 hover:bg-emerald-600 active:scale-[0.99] text-gray-950 font-bold py-3 px-4 rounded-xl transition duration-150 shadow-lg shadow-emerald-500/20">
                Go to Dashboard
            </a>
        </div>

    </div>
</body>
</html>
