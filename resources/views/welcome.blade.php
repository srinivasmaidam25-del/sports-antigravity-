<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Cricket Hub | Premium Booking Platform Vijayawada</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via Vite) & Livewire Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Inline Custom Styles -->
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 antialiased min-h-screen">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 bg-gray-950/80 backdrop-blur-md border-b border-gray-900 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="#" class="flex items-center space-x-2">
                <span class="text-xl sm:text-2xl font-extrabold tracking-wider text-emerald-400">THE CRICKET HUB</span>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-gray-300">
                <a href="#facilities" class="hover:text-emerald-400 hover:scale-110 transition-all duration-200">Facilities</a>
                <a href="#pricing" class="hover:text-emerald-400 hover:scale-110 transition-all duration-200">Pricing</a>
                <a href="#memberships" class="hover:text-emerald-400 hover:scale-110 transition-all duration-200">Memberships</a>
                <a href="#book" class="hover:text-emerald-400 hover:scale-110 transition-all duration-200">Book Turf</a>
                <a href="#about" class="hover:text-emerald-400 hover:scale-110 transition-all duration-200">About Hub</a>
            </nav>

            <!-- Dashboard / CTA buttons -->
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-emerald-400 hover:underline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-400 hover:text-white transition">Sign In</a>
                    <a href="{{ route('register') }}" class="hidden sm:inline-block bg-emerald-500 hover:bg-emerald-600 text-gray-950 font-bold text-sm px-5 py-2.5 rounded-lg transition">Sign Up</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Cinematic Video Hero -->
    <section class="relative h-[80vh] min-h-[500px] flex items-center justify-center overflow-hidden border-b border-gray-900">
        
        <!-- Video Background -->
        <video autoplay muted loop playsinline class="absolute z-10 w-auto min-w-full min-h-full max-w-none object-cover opacity-30">
            <source src="https://assets.mixkit.co/videos/preview/mixkit-cricket-batsman-hitting-a-ball-34346-large.mp4" type="video/mp4">
            <!-- Fallback Image if video fails -->
            <img src="https://images.unsplash.com/photo-1531415074968-036ba1b575da?q=80&w=2000" alt="Premium Cricket Turf" class="w-full h-full object-cover">
        </video>
        
        <!-- Overlay for cinematic depth -->
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/40 to-transparent z-20"></div>

        <!-- Hero content -->
        <div class="relative z-30 text-center max-w-4xl px-4 space-y-6">
            <span class="inline-block bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 font-bold text-xs uppercase tracking-widest px-4 py-1.5 rounded-full">
                PREMIUM 2-ACRE OUTDOOR DESTINATION
            </span>
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-white leading-none">
                PLAY CRICKET.<br><span class="text-emerald-400">YOUR WAY.</span>
            </h1>
            <p class="text-base sm:text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed">
                Experience Vijayawada's ultimate box arenas, professional practice nets, and a massive 1-acre open match field.
            </p>
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#book" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-400 hover:scale-[1.08] active:scale-[0.98] text-gray-950 font-extrabold text-base px-8 py-4 rounded-xl transition-all duration-200 shadow-lg shadow-emerald-500/25 text-center">
                    BOOK NOW
                </a>
                <a href="#facilities" class="w-full sm:w-auto bg-gray-900 hover:bg-gray-800 hover:scale-[1.08] hover:border-gray-700 active:scale-[0.98] text-white font-bold text-base px-8 py-4 rounded-xl border border-gray-800 transition-all duration-200 text-center">
                    Explore Facilities
                </a>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section id="facilities" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center space-y-3">
            <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white">FACILITIES & PRICING</h2>
            <p class="text-gray-400 max-w-xl mx-auto">Explore our world-class playing areas, optimized for practice sessions and tournaments.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Box Cricket -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <span class="text-emerald-400 text-xs font-bold uppercase tracking-wider">Premium turf</span>
                    <h3 class="text-xl font-bold text-white">Box Cricket</h3>
                    <p class="text-sm text-gray-400">Enclosed professional box arena. Perfect for fast-paced, action-packed team play.</p>
                    <div class="border-t border-gray-800 pt-3 space-y-2 text-xs text-gray-400">
                        <div class="flex justify-between"><span>Dimensions</span><span class="text-white font-medium">110 × 60 ft</span></div>
                        <div class="flex justify-between"><span>Slot Unit</span><span class="text-white font-medium">1 Hour</span></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="text-xs text-gray-500">Starting from</div>
                    <div class="text-2xl font-bold text-white">₹600 <span class="text-xs font-normal text-gray-400">/ hour</span></div>
                </div>
            </div>

            <!-- Practice Net 1 -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <span class="text-emerald-400 text-xs font-bold uppercase tracking-wider">Batting & Bowling</span>
                    <h3 class="text-xl font-bold text-white">Practice Net 1</h3>
                    <p class="text-sm text-gray-400">Equipped net for batting practice, bowling simulation, and individual drills.</p>
                    <div class="border-t border-gray-800 pt-3 space-y-2 text-xs text-gray-400">
                        <div class="flex justify-between"><span>Dimensions</span><span class="text-white font-medium">110 × 16 ft</span></div>
                        <div class="flex justify-between"><span>Slot Unit</span><span class="text-white font-medium">1 Hour</span></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="text-xs text-gray-500">Starting from</div>
                    <div class="text-2xl font-bold text-white">₹200 <span class="text-xs font-normal text-gray-400">/ hour</span></div>
                </div>
            </div>

            <!-- Practice Net 2 -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <span class="text-emerald-400 text-xs font-bold uppercase tracking-wider">Independent Lane</span>
                    <h3 class="text-xl font-bold text-white">Practice Net 2</h3>
                    <p class="text-sm text-gray-400">Matches Lane 1 specifications. Available for separate reservations.</p>
                    <div class="border-t border-gray-800 pt-3 space-y-2 text-xs text-gray-400">
                        <div class="flex justify-between"><span>Dimensions</span><span class="text-white font-medium">110 × 16 ft</span></div>
                        <div class="flex justify-between"><span>Slot Unit</span><span class="text-white font-medium">1 Hour</span></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="text-xs text-gray-500">Starting from</div>
                    <div class="text-2xl font-bold text-white">₹200 <span class="text-xs font-normal text-gray-400">/ hour</span></div>
                </div>
            </div>

            <!-- Open Pitch -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <span class="text-emerald-400 text-xs font-bold uppercase tracking-wider">Championship pitch</span>
                    <h3 class="text-xl font-bold text-white">Open Cricket Pitch</h3>
                    <p class="text-sm text-gray-400">Premium 1-acre open outfield. Best for full-field competitive matches.</p>
                    <div class="border-t border-gray-800 pt-3 space-y-2 text-xs text-gray-400">
                        <div class="flex justify-between"><span>Dimensions</span><span class="text-white font-medium">1 Acre field</span></div>
                        <div class="flex justify-between"><span>Slot Unit</span><span class="text-white font-medium">2 Hours</span></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="text-xs text-gray-500">Starting from</div>
                    <div class="text-2xl font-bold text-white">₹1,400 <span class="text-xs font-normal text-gray-400">/ 2 hours</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Widget Area -->
    <section id="book" class="bg-gray-900 py-20 border-t border-b border-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center space-y-3">
                <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white">SELECT YOUR SLOT</h2>
                <p class="text-gray-400 max-w-xl mx-auto">Pick a turf, select an open time slot, apply coupons, and checkout securely in seconds.</p>
            </div>

            <!-- Livewire Booking Widget Component -->
            <div class="bg-gray-950 border border-gray-800 rounded-2xl p-6 md:p-8 shadow-2xl">
                @livewire('booking-widget')
            </div>
        </div>
    </section>

    <!-- Memberships Section -->
    <section id="memberships" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="text-center space-y-3">
            <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-white">CLUB MEMBERSHIPS</h2>
            <p class="text-gray-400 max-w-xl mx-auto">Get free hours, priority slot reservations, kit rentals, and exclusive weekend discounts.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Bronze -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <h3 class="text-lg font-bold text-white">Bronze</h3>
                    <p class="text-sm text-gray-400">Perfect for casual cricket enthusiasts starting out.</p>
                    <ul class="text-xs text-gray-400 space-y-2 pt-4">
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> 4 Included Hours</li>
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> 10% discount on additional slots</li>
                    </ul>
                </div>
                <div class="space-y-4 border-t border-gray-800 pt-4">
                    <div class="text-2xl font-bold text-white">₹2,499</div>
                    <form action="{{ route('payment.checkout-membership') }}" method="POST">
                        @csrf
                        <input type="hidden" name="membership_id" value="1">
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 hover:scale-[1.07] active:scale-[0.98] text-gray-950 font-bold py-2.5 px-4 rounded-lg text-sm transition-all duration-200 shadow-md hover:shadow-emerald-500/20">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Silver -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <h3 class="text-lg font-bold text-white">Silver</h3>
                    <p class="text-sm text-gray-400">For regular groups playing weekly training matches.</p>
                    <ul class="text-xs text-gray-400 space-y-2 pt-4">
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> 8 Included Hours</li>
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> 15% discount on additional slots</li>
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> Priority Booking windows</li>
                    </ul>
                </div>
                <div class="space-y-4 border-t border-gray-800 pt-4">
                    <div class="text-2xl font-bold text-white">₹4,999</div>
                    <form action="{{ route('payment.checkout-membership') }}" method="POST">
                        @csrf
                        <input type="hidden" name="membership_id" value="2">
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 hover:scale-[1.07] active:scale-[0.98] text-gray-950 font-bold py-2.5 px-4 rounded-lg text-sm transition-all duration-200 shadow-md hover:shadow-emerald-500/20">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Gold -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 flex flex-col justify-between space-y-6 ring-2 ring-emerald-500/20">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white">Gold</h3>
                        <span class="bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded">Popular</span>
                    </div>
                    <p class="text-sm text-gray-400">For cricket academies and regular match organizers.</p>
                    <ul class="text-xs text-gray-400 space-y-2 pt-4">
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> 16 Included Hours</li>
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> 20% discount on additional slots</li>
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> Priority Booking</li>
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> Free Kit Rental inclusion</li>
                    </ul>
                </div>
                <div class="space-y-4 border-t border-gray-800 pt-4">
                    <div class="text-2xl font-bold text-white">₹9,499</div>
                    <form action="{{ route('payment.checkout-membership') }}" method="POST">
                        @csrf
                        <input type="hidden" name="membership_id" value="3">
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 hover:scale-[1.07] active:scale-[0.98] text-gray-950 font-bold py-2.5 px-4 rounded-lg text-sm transition-all duration-200 shadow-md hover:shadow-emerald-500/20">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Platinum -->
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <h3 class="text-lg font-bold text-white">Platinum</h3>
                    <p class="text-sm text-gray-400">Ultimate plan for teams, leagues, and tournaments.</p>
                    <ul class="text-xs text-gray-400 space-y-2 pt-4">
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> 24 Included Hours</li>
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> 25% Weekend discounts</li>
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> Kit Rental & Priority booking</li>
                        <li class="flex items-center"><span class="text-emerald-400 mr-2">✓</span> Tournament & Birthday offers</li>
                    </ul>
                </div>
                <div class="space-y-4 border-t border-gray-800 pt-4">
                    <div class="text-2xl font-bold text-white">₹14,999</div>
                    <form action="{{ route('payment.checkout-membership') }}" method="POST">
                        @csrf
                        <input type="hidden" name="membership_id" value="4">
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 hover:scale-[1.07] active:scale-[0.98] text-gray-950 font-bold py-2.5 px-4 rounded-lg text-sm transition-all duration-200 shadow-md hover:shadow-emerald-500/20">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Interest Lead Generation Widget -->
    <section class="bg-gray-900 py-16 border-t border-gray-800">
        <div class="max-w-xl mx-auto px-4 text-center space-y-8">
            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-white">Can't Find an Available Slot?</h2>
                <p class="text-sm text-gray-400">Register your slot interest below. Our staff will notify you if a slot is cancelled or when new periods open.</p>
            </div>

            @if(session('success_interest'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl text-sm">
                    {{ session('success_interest') }}
                </div>
            @endif

            <form action="{{ route('interest.store') }}" method="POST" class="bg-gray-950 p-6 rounded-2xl border border-gray-850 space-y-4 text-left">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="int_name" class="text-xs text-gray-400 block mb-1">Full Name</label>
                        <input type="text" id="int_name" name="name" required class="w-full bg-gray-900 border border-gray-800 rounded p-2 text-sm outline-none text-white focus:border-emerald-500">
                    </div>
                    <div>
                        <label for="int_mobile" class="text-xs text-gray-400 block mb-1">Mobile Number</label>
                        <input type="text" id="int_mobile" name="mobile" required class="w-full bg-gray-900 border border-gray-800 rounded p-2 text-sm outline-none text-white focus:border-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="int_fac" class="text-xs text-gray-400 block mb-1">Facility</label>
                        <select id="int_fac" name="facility_id" required class="w-full bg-gray-900 border border-gray-800 rounded p-2 text-sm outline-none text-white focus:border-emerald-500">
                            <option value="1">Box Cricket</option>
                            <option value="2">Practice Net 1</option>
                            <option value="3">Practice Net 2</option>
                            <option value="4">Open Pitch</option>
                        </select>
                    </div>
                    <div>
                        <label for="int_date" class="text-xs text-gray-400 block mb-1">Date</label>
                        <input type="date" id="int_date" name="interested_date" min="{{ now()->format('Y-m-d') }}" required class="w-full bg-gray-900 border border-gray-800 rounded p-2 text-sm outline-none text-white focus:border-emerald-500">
                    </div>
                    <div>
                        <label for="int_time" class="text-xs text-gray-400 block mb-1">Time Slot (e.g. 6PM)</label>
                        <input type="text" id="int_time" name="interested_time" placeholder="18:00:00" required class="w-full bg-gray-900 border border-gray-800 rounded p-2 text-sm outline-none text-white focus:border-emerald-500">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 hover:scale-[1.06] active:scale-[0.98] text-gray-950 font-bold py-3 px-4 rounded-xl transition-all duration-200 text-center text-sm shadow-md hover:shadow-emerald-500/20">
                        Submit Interest Request
                    </button>
                    <span class="block text-[10px] text-center text-gray-500 mt-2">
                        *Note: This does not reserve the slot.
                    </span>
                </div>
            </form>
        </div>
    </section>

    <!-- Local SEO Outfield / Footer Section -->
    <footer class="bg-gray-950 border-t border-gray-900 py-12 text-sm text-gray-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-3">
                <span class="text-white font-bold text-emerald-400">THE CRICKET HUB</span>
                <p class="text-xs">Vijayawada's premium 2-acre outdoor cricket turf and tournament destination. Play cricket. Your way.</p>
                <div class="text-xs space-y-1 pt-2">
                    <p>📍 Ambapuram, Vijayawada, Andhra Pradesh, India</p>
                    <p>📞 +91 99999 88888</p>
                    <p>✉ hello@thecrickethub.com</p>
                </div>
            </div>
            
            <div class="space-y-3">
                <span class="text-white font-semibold">Local Turf Booking Searches</span>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="bg-gray-900 border border-gray-850 px-2 py-1 rounded">Box Cricket Vijayawada</span>
                    <span class="bg-gray-900 border border-gray-850 px-2 py-1 rounded">Cricket Nets Vijayawada</span>
                    <span class="bg-gray-900 border border-gray-850 px-2 py-1 rounded">Box Cricket Ambapuram</span>
                    <span class="bg-gray-900 border border-gray-850 px-2 py-1 rounded">Open Cricket Ground Vijayawada</span>
                    <span class="bg-gray-900 border border-gray-850 px-2 py-1 rounded">Cricket Ground Booking Vijayawada</span>
                </div>
            </div>

            <div class="space-y-3">
                <span class="text-white font-semibold">Operational Hours</span>
                <div class="text-xs space-y-1">
                    <p class="flex justify-between"><span>Monday – Thursday</span><span class="text-white">6:00 AM – 11:00 PM</span></p>
                    <p class="flex justify-between"><span>Friday – Sunday</span><span class="text-white">6:00 AM – 12:00 AM</span></p>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-900 mt-8 pt-8 text-center text-xs">
            &copy; 2026 The Cricket Hub. All rights reserved. Built for Hostinger Shared Hosting.
        </div>
    </footer>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>
