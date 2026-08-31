<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Failed - The Cricket Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl overflow-hidden p-6 space-y-6 text-center">
        
        <!-- Fail Icon -->
        <div class="mx-auto w-16 h-16 bg-rose-500/10 border border-rose-500/20 rounded-full flex items-center justify-center text-rose-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-bold tracking-tight text-white">Payment Failed</h1>
            <p class="text-sm text-gray-400">We could not complete your booking transaction.</p>
        </div>

        <!-- Details -->
        <div class="bg-gray-950 p-4 rounded-xl space-y-3 border border-gray-800/50 text-left text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Booking ID:</span>
                <span class="font-mono text-rose-400">#CHUB-{{ $booking->id }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Facility:</span>
                <span class="font-semibold text-white">{{ $booking->facility->name }}</span>
            </div>
            <div class="flex justify-between border-t border-gray-800/80 pt-2 mt-2">
                <span class="text-gray-500">Error Details:</span>
                <span class="text-rose-400 font-medium">{{ $errors->first() ?? 'Transaction declined by gateway.' }}</span>
            </div>
        </div>

        <!-- Explainer -->
        <p class="text-xs text-gray-500">
            Don't worry: If any amount was deducted from your account, it will be automatically refunded within 3-5 business days.
        </p>

        <!-- CTA -->
        <div class="pt-4 border-t border-gray-800 space-y-2">
            <a href="{{ route('dashboard') }}" class="inline-block w-full bg-gray-800 hover:bg-gray-700 active:scale-[0.99] text-white font-semibold py-3 px-4 rounded-xl transition duration-150">
                Go to Dashboard
            </a>
        </div>

    </div>
</body>
</html>
