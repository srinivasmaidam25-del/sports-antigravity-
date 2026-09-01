<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - The Cricket Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        gray: {
                            850: '#18202F',
                            950: '#0B0F17'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @php
        $cssFile = public_path('build/assets/app-5j_CctYh.css');
        if (!file_exists($cssFile)) {
            $cssFile = base_path('public/build/assets/app-5j_CctYh.css');
        }
    @endphp
    @if(file_exists($cssFile))
        <style>
            {!! file_get_contents($cssFile) !!}
        </style>
    @endif
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-bold tracking-wider text-emerald-400">THE CRICKET HUB</span>
            <span class="bg-gray-800 text-gray-400 text-xs px-2 py-1 rounded">Control Panel</span>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-right">
                <div class="text-sm font-semibold text-white">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-400 uppercase tracking-widest">{{ Auth::user()->role }}</div>
            </div>
            <a href="/" class="text-sm bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition">
                View Site
            </a>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto p-6 space-y-6" x-data="{ tab: 'bookings' }">
        
        <!-- Flash messages -->
        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl space-y-2">
                <span class="text-xs text-gray-400 uppercase font-semibold">Total Revenue</span>
                <div class="text-2xl font-bold text-emerald-400">₹{{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl space-y-2">
                <span class="text-xs text-gray-400 uppercase font-semibold">Confirmed Bookings</span>
                <div class="text-2xl font-bold text-white">{{ $confirmedBookingsCount }}</div>
            </div>
            <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl space-y-2">
                <span class="text-xs text-gray-400 uppercase font-semibold">New Interest Leads</span>
                <div class="text-2xl font-bold text-white">{{ $newInterestsCount }}</div>
            </div>
            <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl space-y-2">
                <span class="text-xs text-gray-400 uppercase font-semibold">Staff Duty</span>
                <div class="text-sm font-semibold text-white pt-1">Active Control</div>
            </div>
        </div>

        <!-- Tab Controls Navigation -->
        <div class="flex flex-wrap border-b border-gray-850 gap-2">
            <button @click="tab = 'bookings'" :class="tab === 'bookings' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-white'" class="border-b-2 font-bold text-sm py-3 px-4 transition-all duration-200 hover:scale-[1.08] active:scale-[0.95]">
                Bookings
            </button>
            <button @click="tab = 'manual'" :class="tab === 'manual' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-white'" class="border-b-2 font-bold text-sm py-3 px-4 transition-all duration-200 hover:scale-[1.08] active:scale-[0.95]">
                Manual Booking
            </button>
            <button @click="tab = 'payments'" :class="tab === 'payments' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-white'" class="border-b-2 font-bold text-sm py-3 px-4 transition-all duration-200 hover:scale-[1.08] active:scale-[0.95]">
                Payments
            </button>
            <button @click="tab = 'interests'" :class="tab === 'interests' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-white'" class="border-b-2 font-bold text-sm py-3 px-4 transition-all duration-200 hover:scale-[1.08] active:scale-[0.95]">
                Interest Leads ({{ $totalInterestsCount }})
            </button>
            @if(Auth::user()->role !== 'staff')
                <button @click="tab = 'coupons'" :class="tab === 'coupons' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-white'" class="border-b-2 font-bold text-sm py-3 px-4 transition-all duration-200 hover:scale-[1.08] active:scale-[0.95]">
                    Coupons
                </button>
                <button @click="tab = 'settings'" :class="tab === 'settings' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-white'" class="border-b-2 font-bold text-sm py-3 px-4 transition-all duration-200 hover:scale-[1.08] active:scale-[0.95]">
                    Settings
                </button>
            @endif
            <button @click="tab = 'audit'" :class="tab === 'audit' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-gray-400 hover:text-white'" class="border-b-2 font-bold text-sm py-3 px-4 transition-all duration-200 hover:scale-[1.08] active:scale-[0.95]">
                Audit Logs
            </button>
        </div>

        <!-- 1. Bookings Tab -->
        <div x-show="tab === 'bookings'" class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden p-6 space-y-4">
            <h3 class="text-lg font-bold text-white mb-2">Bookings Manager</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-gray-950 text-xs font-semibold uppercase tracking-wider text-gray-300">
                        <tr>
                            <th class="p-4">ID</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Facility</th>
                            <th class="p-4">Date / Time</th>
                            <th class="p-4">Price</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-850">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-gray-850/30">
                                <td class="p-4 font-mono text-emerald-400">#{{ $booking->id }}</td>
                                <td class="p-4 text-white">
                                    {{ $booking->user->name ?? 'Guest/Anonymous' }}
                                    <span class="block text-xs text-gray-500">{{ $booking->user->phone ?? '' }}</span>
                                </td>
                                <td class="p-4">{{ $booking->facility->name }}</td>
                                <td class="p-4">
                                    {{ $booking->booking_date->format('d M Y') }}
                                    <span class="block text-xs text-emerald-400 font-medium">{{ Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</span>
                                </td>
                                <td class="p-4 text-white">₹{{ number_format($booking->final_price, 2) }}</td>
                                <td class="p-4">
                                    <span class="inline-block text-xs px-2.5 py-0.5 rounded-full font-semibold
                                        @if($booking->status === 'CONFIRMED') bg-emerald-500/10 text-emerald-400
                                        @elseif($booking->status === 'PENDING') bg-amber-500/10 text-amber-400
                                        @elseif($booking->status === 'CANCELLED') bg-gray-500/10 text-gray-400
                                        @else bg-rose-500/10 text-rose-400 @endif">
                                        {{ $booking->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    @if($booking->status === 'CONFIRMED' && Auth::user()->role !== 'staff')
                                        <form action="{{ route('admin.booking.refund', ['id' => $booking->id]) }}" method="POST" onsubmit="return confirm('Refund this booking? This will cancel the reservation.')">
                                            @csrf
                                            <button type="submit" class="bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 text-xs px-3 py-1 rounded transition">
                                                Refund & Cancel
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Manual Booking Tab -->
        <div x-show="tab === 'manual'" class="bg-gray-900 border border-gray-800 rounded-2xl p-6 space-y-6">
            <div>
                <h3 class="text-lg font-bold text-white">Log Manual Walk-in Booking</h3>
                <p class="text-sm text-gray-400">Instantly log cash payments at the counter. Confirms the slot immediately.</p>
            </div>
            
            <form action="{{ route('admin.booking.manual') }}" method="POST" class="max-w-2xl space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="man_name" class="text-xs text-gray-400 block mb-1">Customer Name</label>
                        <input type="text" id="man_name" name="customer_name" required class="w-full bg-gray-950 border border-gray-800 rounded p-3 text-sm text-white focus:border-emerald-500 outline-none">
                    </div>
                    <div>
                        <label for="man_phone" class="text-xs text-gray-400 block mb-1">Customer Mobile</label>
                        <input type="text" id="man_phone" name="customer_phone" required class="w-full bg-gray-950 border border-gray-800 rounded p-3 text-sm text-white focus:border-emerald-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="man_fac" class="text-xs text-gray-400 block mb-1">Facility</label>
                        <select id="man_fac" name="facility_id" required class="w-full bg-gray-950 border border-gray-800 rounded p-3 text-sm text-white focus:border-emerald-500 outline-none">
                            @foreach($facilities as $facility)
                                <option value="{{ $facility->id }}">{{ $facility->name }} ({{ $facility->dimensions }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="man_date" class="text-xs text-gray-400 block mb-1">Date</label>
                        <input type="date" id="man_date" name="booking_date" min="{{ now()->format('Y-m-d') }}" required class="w-full bg-gray-950 border border-gray-800 rounded p-3 text-sm text-white focus:border-emerald-500 outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="man_start" class="text-xs text-gray-400 block mb-1">Start Time</label>
                            <input type="text" id="man_start" name="start_time" placeholder="09:00:00" required class="w-full bg-gray-950 border border-gray-800 rounded p-3 text-sm text-white focus:border-emerald-500 outline-none">
                        </div>
                        <div>
                            <label for="man_end" class="text-xs text-gray-400 block mb-1">End Time</label>
                            <input type="text" id="man_end" name="end_time" placeholder="10:00:00" required class="w-full bg-gray-950 border border-gray-800 rounded p-3 text-sm text-white focus:border-emerald-500 outline-none">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="man_notes" class="text-xs text-gray-400 block mb-1">Staff Notes</label>
                    <textarea id="man_notes" name="notes" placeholder="Cash collection details..." class="w-full bg-gray-950 border border-gray-800 rounded p-3 text-sm text-white focus:border-emerald-500 outline-none" rows="2"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-gray-950 font-bold py-3 px-6 rounded-xl transition">
                        Confirm Manual Booking
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. Payments Tab -->
        <div x-show="tab === 'payments'" class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden p-6 space-y-4">
            <h3 class="text-lg font-bold text-white mb-2">Payments Ledger</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-gray-950 text-xs font-semibold uppercase tracking-wider text-gray-300">
                        <tr>
                            <th class="p-4">Payment ID</th>
                            <th class="p-4">Order Ref / ID</th>
                            <th class="p-4">Booking ID</th>
                            <th class="p-4">Amount</th>
                            <th class="p-4">Gateway</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-850">
                        @foreach($payments as $payment)
                            <tr class="hover:bg-gray-850/30">
                                <td class="p-4 font-mono text-gray-350">#PAY-{{ $payment->id }}</td>
                                <td class="p-4 font-mono text-gray-300">{{ $payment->transaction_reference }}</td>
                                <td class="p-4 text-emerald-400 font-semibold">
                                    @if($payment->booking_id === 0)
                                        <span class="text-blue-400 uppercase text-xs">Membership</span>
                                    @else
                                        #CHUB-{{ $payment->booking_id }}
                                    @endif
                                </td>
                                <td class="p-4 text-white">₹{{ number_format($payment->amount, 2) }}</td>
                                <td class="p-4">{{ $payment->gateway }}</td>
                                <td class="p-4">
                                    <span class="inline-block text-xs px-2.5 py-0.5 rounded-full font-semibold
                                        @if($payment->status === 'SUCCESS') bg-emerald-500/10 text-emerald-400
                                        @elseif($payment->status === 'PENDING') bg-amber-500/10 text-amber-400
                                        @elseif($payment->status === 'FAILED') bg-rose-500/10 text-rose-400
                                        @else bg-blue-500/10 text-blue-400 @endif">
                                        {{ $payment->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-xs">{{ $payment->created_at->format('d M Y h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Interest Leads Tab -->
        <div x-show="tab === 'interests'" class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden p-6 space-y-4">
            <h3 class="text-lg font-bold text-white mb-2">Interest Leads ("I'm Interested" Form Submissions)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-gray-950 text-xs font-semibold uppercase tracking-wider text-gray-300">
                        <tr>
                            <th class="p-4">Lead ID</th>
                            <th class="p-4">Customer Name</th>
                            <th class="p-4">Mobile</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Facility</th>
                            <th class="p-4">Preferred Slot</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Update Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-850">
                        @foreach($interests as $lead)
                            <tr class="hover:bg-gray-850/30">
                                <td class="p-4 font-mono text-gray-350">#INT-{{ $lead->id }}</td>
                                <td class="p-4 text-white font-semibold">{{ $lead->name }}</td>
                                <td class="p-4 font-mono">{{ $lead->mobile }}</td>
                                <td class="p-4 text-xs">{{ $lead->email ?? '—' }}</td>
                                <td class="p-4 text-gray-300">{{ $lead->facility->name }}</td>
                                <td class="p-4">
                                    {{ $lead->interested_date->format('d M Y') }}
                                    <span class="block text-xs text-emerald-400 font-medium">{{ Carbon\Carbon::parse($lead->interested_time)->format('h:i A') }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="inline-block text-xs px-2.5 py-0.5 rounded-full font-semibold
                                        @if($lead->status === 'NEW') bg-blue-500/10 text-blue-400
                                        @elseif($lead->status === 'CONTACTED') bg-amber-500/10 text-amber-400
                                        @elseif($lead->status === 'CONVERTED') bg-emerald-500/10 text-emerald-400
                                        @else bg-gray-500/10 text-gray-400 @endif">
                                        {{ $lead->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('admin.interest.update', ['id' => $lead->id]) }}" method="POST" class="inline-flex space-x-1">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="bg-gray-950 border border-gray-800 text-xs rounded text-white p-1 focus:outline-none">
                                            <option value="NEW" @if($lead->status === 'NEW') selected @endif>New</option>
                                            <option value="CONTACTED" @if($lead->status === 'CONTACTED') selected @endif>Contacted</option>
                                            <option value="CONVERTED" @if($lead->status === 'CONVERTED') selected @endif>Converted</option>
                                            <option value="CLOSED" @if($lead->status === 'CLOSED') selected @endif>Closed</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(Auth::user()->role !== 'staff')
            <!-- 5. Coupons Tab -->
            <div x-show="tab === 'coupons'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Add Coupon Form -->
                <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl space-y-4">
                    <h3 class="font-bold text-white mb-2">Create Promo Coupon</h3>
                    <form action="{{ route('admin.coupon') }}" method="POST" class="space-y-4 text-sm">
                        @csrf
                        <div>
                            <label for="cp_code" class="text-xs text-gray-400 block mb-1">Coupon Code</label>
                            <input type="text" id="cp_code" name="code" required placeholder="DIWALI50" class="w-full bg-gray-950 border border-gray-800 rounded p-2 text-white outline-none focus:border-emerald-500 uppercase">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="cp_type" class="text-xs text-gray-400 block mb-1">Type</label>
                                <select id="cp_type" name="discount_type" class="w-full bg-gray-950 border border-gray-800 rounded p-2 text-white outline-none">
                                    <option value="PERCENTAGE">Percentage</option>
                                    <option value="FIXED">Fixed Amount</option>
                                </select>
                            </div>
                            <div>
                                <label for="cp_val" class="text-xs text-gray-400 block mb-1">Value (₹ or %)</label>
                                <input type="number" id="cp_val" name="discount_value" required placeholder="10" class="w-full bg-gray-950 border border-gray-800 rounded p-2 text-white outline-none">
                            </div>
                        </div>
                        <div>
                            <label for="cp_min" class="text-xs text-gray-400 block mb-1">Minimum Booking Amount (₹)</label>
                            <input type="number" id="cp_min" name="min_booking_amount" required value="0" class="w-full bg-gray-950 border border-gray-800 rounded p-2 text-white outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="cp_start" class="text-xs text-gray-400 block mb-1">Starts At</label>
                                <input type="datetime-local" id="cp_start" name="starts_at" required class="w-full bg-gray-950 border border-gray-800 rounded p-2 text-xs text-white outline-none">
                            </div>
                            <div>
                                <label for="cp_end" class="text-xs text-gray-400 block mb-1">Ends At</label>
                                <input type="datetime-local" id="cp_end" name="ends_at" required class="w-full bg-gray-950 border border-gray-800 rounded p-2 text-xs text-white outline-none">
                            </div>
                        </div>
                        <div>
                            <label for="cp_lim" class="text-xs text-gray-400 block mb-1">Total Usage Limit</label>
                            <input type="number" id="cp_lim" name="usage_limit" placeholder="Unlimited" class="w-full bg-gray-950 border border-gray-800 rounded p-2 text-white outline-none">
                        </div>
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-gray-950 font-bold py-2.5 px-4 rounded-xl transition">
                            Create Coupon
                        </button>
                    </form>
                </div>
                <!-- Coupons List -->
                <div class="lg:col-span-2 bg-gray-900 border border-gray-800 p-6 rounded-2xl overflow-hidden space-y-4">
                    <h3 class="font-bold text-white mb-2">Active Promo Coupons</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-400">
                            <thead class="bg-gray-950 text-xs font-semibold uppercase text-gray-300">
                                <tr>
                                    <th class="p-3">Code</th>
                                    <th class="p-3">Discount</th>
                                    <th class="p-3">Usage</th>
                                    <th class="p-3">Starts At</th>
                                    <th class="p-3">Ends At</th>
                                    <th class="p-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-850">
                                @foreach($coupons as $coupon)
                                    <tr class="hover:bg-gray-850/30">
                                        <td class="p-3 font-bold text-white tracking-wide uppercase">{{ $coupon->code }}</td>
                                        <td class="p-3 text-emerald-400">
                                            {{ $coupon->discount_type === 'PERCENTAGE' ? number_format($coupon->discount_value, 0) . '%' : '₹' . number_format($coupon->discount_value, 0) }}
                                        </td>
                                        <td class="p-3 text-xs">{{ $coupon->times_used }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                                        <td class="p-3 text-xs">{{ $coupon->starts_at->format('d M Y') }}</td>
                                        <td class="p-3 text-xs">{{ $coupon->ends_at->format('d M Y') }}</td>
                                        <td class="p-3 text-xs">
                                            <span class="text-xs font-semibold {{ $coupon->is_active && $coupon->ends_at->isAfter(now()) ? 'text-emerald-400' : 'text-gray-500' }}">
                                                {{ $coupon->is_active && $coupon->ends_at->isAfter(now()) ? 'Active' : 'Expired/Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 6. Settings Tab -->
            <div x-show="tab === 'settings'" class="bg-gray-900 border border-gray-800 rounded-2xl p-6 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-white">Business Settings</h3>
                    <p class="text-sm text-gray-400">Configure operating rules, cutoffs, and reward loop coefficients.</p>
                </div>
                
                <form action="{{ route('admin.settings') }}" method="POST" class="max-w-3xl space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Block: Basic settings & Cutoffs -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-emerald-400 border-b border-gray-800 pb-2">Business Settings</h4>
                            
                            <div>
                                <label for="set_cutoff" class="text-xs text-gray-400 block mb-1">Friday Weekend Start Cutoff Time</label>
                                <input type="text" id="set_cutoff" name="friday_weekend_cutoff" required value="{{ $settings['friday_weekend_cutoff'] }}" placeholder="17:00" class="w-full bg-gray-950 border border-gray-800 rounded p-2.5 text-sm text-white focus:border-emerald-500 outline-none">
                                <span class="text-[10px] text-gray-500 mt-1 block">24-hour format (e.g. 17:00 = 5:00 PM). Bookings touching/after this are weekend priced.</span>
                            </div>

                            <div>
                                <label for="set_ref" class="text-xs text-gray-400 block mb-1">Referral Discount percentage (%)</label>
                                <input type="text" id="set_ref" name="referral_discount_percentage" required value="{{ $settings['referral_discount_percentage'] }}" class="w-full bg-gray-950 border border-gray-800 rounded p-2.5 text-sm text-white focus:border-emerald-500 outline-none">
                                <span class="text-[10px] text-gray-500 mt-1 block">Percentage value coupon awarded to referrers.</span>
                            </div>

                            <h4 class="text-sm font-bold uppercase tracking-wider text-emerald-400 border-b border-gray-800 pb-2 pt-2">Operational Timings</h4>
                            
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="mon_thu_start" class="text-xs text-gray-400 block mb-1">Mon–Thu Open</label>
                                    <input type="text" id="mon_thu_start" name="operating_hours_mon_thu_start" required value="{{ $settings['operating_hours_mon_thu']['start'] }}" placeholder="06:00" class="w-full bg-gray-950 border border-gray-800 rounded p-2 text-xs text-white focus:border-emerald-500 outline-none">
                                </div>
                                <div>
                                    <label for="mon_thu_end" class="text-xs text-gray-400 block mb-1">Mon–Thu Close</label>
                                    <input type="text" id="mon_thu_end" name="operating_hours_mon_thu_end" required value="{{ $settings['operating_hours_mon_thu']['end'] }}" placeholder="23:00" class="w-full bg-gray-950 border border-gray-800 rounded p-2 text-xs text-white focus:border-emerald-500 outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label for="fri_sun_start" class="text-xs text-gray-400 block mb-1">Fri–Sun Open</label>
                                    <input type="text" id="fri_sun_start" name="operating_hours_fri_sun_start" required value="{{ $settings['operating_hours_fri_sun']['start'] }}" placeholder="06:00" class="w-full bg-gray-950 border border-gray-800 rounded p-2 text-xs text-white focus:border-emerald-500 outline-none">
                                </div>
                                <div>
                                    <label for="fri_sun_end" class="text-xs text-gray-400 block mb-1">Fri–Sun Close</label>
                                    <input type="text" id="fri_sun_end" name="operating_hours_fri_sun_end" required value="{{ $settings['operating_hours_fri_sun']['end'] }}" placeholder="00:00" class="w-full bg-gray-950 border border-gray-800 rounded p-2 text-xs text-white focus:border-emerald-500 outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Right Block: Facility Prices -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-emerald-400 border-b border-gray-800 pb-2">Facility Prices (Hourly / Slot)</h4>
                            
                            @foreach($facilities as $facility)
                                <div class="bg-gray-950 p-3 rounded-lg border border-gray-850 space-y-2">
                                    <span class="text-xs font-bold text-white tracking-wide block border-b border-gray-900 pb-1">{{ $facility->name }}</span>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="text-[10px] text-gray-500 block mb-0.5">Weekday Rate (₹)</label>
                                            <input type="number" name="facility_prices[{{ $facility->id }}][weekday]" required value="{{ $facility->base_price_weekday }}" class="w-full bg-gray-900 border border-gray-800 rounded p-1.5 text-xs text-white focus:border-emerald-500 outline-none">
                                        </div>
                                        <div>
                                            <label class="text-[10px] text-gray-500 block mb-0.5">Weekend Rate (₹)</label>
                                            <input type="number" name="facility_prices[{{ $facility->id }}][weekend]" required value="{{ $facility->base_price_weekend }}" class="w-full bg-gray-900 border border-gray-800 rounded p-1.5 text-xs text-white focus:border-emerald-500 outline-none">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-800">
                        <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 hover:scale-[1.03] active:scale-[0.98] text-gray-950 font-bold py-3 px-6 rounded-xl transition duration-150">
                            Save Operational Settings & Pricing
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- 7. Audit Logs Tab -->
        <div x-show="tab === 'audit'" class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden p-6 space-y-4">
            <h3 class="text-lg font-bold text-white mb-2">Security Audit Trails</h3>
            <div class="overflow-y-auto max-h-[600px]">
                <table class="w-full text-left text-sm text-gray-400">
                    <thead class="bg-gray-950 text-xs font-semibold uppercase tracking-wider text-gray-300">
                        <tr>
                            <th class="p-3">User</th>
                            <th class="p-3">Action</th>
                            <th class="p-3">Details</th>
                            <th class="p-3">IP Address</th>
                            <th class="p-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-850 text-xs">
                        @foreach($auditLogs as $log)
                            <tr class="hover:bg-gray-850/30">
                                <td class="p-3 font-semibold text-white">
                                    {{ $log->user->name ?? 'System/Guest' }}
                                    <span class="block text-[10px] text-gray-500">{{ $log->user->email ?? '' }}</span>
                                </td>
                                <td class="p-3"><span class="bg-gray-800 text-gray-300 px-2 py-0.5 rounded font-mono font-bold">{{ $log->action }}</span></td>
                                <td class="p-3 text-gray-300">{{ $log->description }}</td>
                                <td class="p-3 font-mono text-[10px]">{{ $log->ip_address }}</td>
                                <td class="p-3 text-gray-500">{{ $log->created_at->format('d M Y h:i:A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
