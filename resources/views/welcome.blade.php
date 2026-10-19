<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Cricket Hub | Premium Booking Platform</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN (with matched config) & Livewire Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            400: '#4ade80',
                            500: '#22c55e', // Pitch Green
                            600: '#16a34a',
                        },
                        surface: {
                            800: '#1e293b',
                            850: '#151e2e',
                            900: '#0f172a', // Midnight Blue
                            950: '#020617', // Deep Dark
                        },
                        accent: {
                            gold: '#fbbf24',
                            orange: '#f97316',
                        }
                    },
                    boxShadow: {
                        'glow': '0 0 20px rgba(34, 197, 94, 0.15)',
                        'glow-lg': '0 0 30px rgba(34, 197, 94, 0.25)',
                    }
                }
            }
        }
    </script>
    
    @livewireStyles

    <style>
        /* Base styles */
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, h5, h6, .font-display { font-family: 'Outfit', sans-serif; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #020617; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #22c55e; }
        
        /* Pitch Pattern Background */
        .bg-pitch-pattern {
            background-image: url('data:image/svg+xml,%3Csvg width="40" height="40" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M0 0h40v40H0z" fill="none"/%3E%3Cpath d="M0 0h1v40H0zM0 0h40v1H0z" fill="rgba(255,255,255,0.03)"/%3E%3C/svg%3E');
        }
    </style>
</head>
<body class="bg-surface-950 text-gray-200 antialiased min-h-screen selection:bg-brand-500 selection:text-white">

    <!-- Header Navigation -->
    <header class="fixed top-0 w-full z-50 bg-surface-950/80 backdrop-blur-xl border-b border-surface-800 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center shadow-glow group-hover:shadow-glow-lg transition-all duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="text-2xl font-display font-black tracking-wide text-white">
                        THE<span class="text-brand-500">CRICKET</span>HUB
                    </span>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden lg:flex items-center space-x-8">
                    <a href="#facilities" class="text-sm font-semibold text-gray-300 hover:text-brand-400 transition-colors">Facilities</a>
                    <a href="#pricing" class="text-sm font-semibold text-gray-300 hover:text-brand-400 transition-colors">Pricing</a>
                    <a href="#memberships" class="text-sm font-semibold text-gray-300 hover:text-brand-400 transition-colors">Memberships</a>
                    <a href="#live-action" class="text-sm font-semibold text-gray-300 hover:text-brand-400 transition-colors flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        Live Action
                    </a>
                </nav>

                <!-- Actions -->
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="hidden sm:block text-sm font-semibold text-brand-400 hover:text-brand-300 transition-colors">Dashboard</a>
                        <div class="w-8 h-8 rounded-full bg-surface-800 border border-surface-700 flex items-center justify-center overflow-hidden">
                            <span class="text-xs font-bold text-white">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:block text-sm font-semibold text-gray-400 hover:text-white transition-colors">Log In</a>
                        <a href="{{ route('register') }}" class="bg-brand-500 hover:bg-brand-400 text-surface-950 font-bold text-sm px-6 py-2.5 rounded-lg shadow-glow hover:shadow-glow-lg hover:-translate-y-0.5 transition-all duration-300">
                            Book Turf
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-surface-950">
        <!-- Background Elements -->
        <div class="absolute inset-0 bg-pitch-pattern opacity-50"></div>
        <div class="absolute top-0 right-0 -mr-40 -mt-40 w-96 h-96 rounded-full bg-brand-500/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-40 -mb-40 w-96 h-96 rounded-full bg-surface-800/50 blur-3xl"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                <!-- Hero Content -->
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-800/50 border border-surface-700 backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                        <span class="text-xs font-bold tracking-wider text-brand-400 uppercase">Premium 2-Acre Turf</span>
                    </div>
                    
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-display font-black text-white leading-[1.1] tracking-tight">
                        ELEVATE YOUR <br />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-400 to-brand-600">CRICKET GAME</span>
                    </h1>
                    
                    <p class="text-lg text-gray-400 max-w-xl leading-relaxed">
                        Experience professional-grade pitches, advanced netting, and floodlit arenas. Book your slot instantly and play like a pro.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="#book" class="inline-flex justify-center items-center gap-2 bg-brand-500 hover:bg-brand-400 text-surface-950 font-bold text-base px-8 py-4 rounded-xl shadow-glow hover:shadow-glow-lg transition-all duration-300">
                            Book Your Slot
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                        <a href="#facilities" class="inline-flex justify-center items-center gap-2 bg-surface-800 hover:bg-surface-700 border border-surface-700 text-white font-semibold text-base px-8 py-4 rounded-xl transition-all duration-300">
                            View Facilities
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-surface-800/50">
                        <div>
                            <div class="text-2xl font-display font-bold text-white">4+</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Arenas</div>
                        </div>
                        <div>
                            <div class="text-2xl font-display font-bold text-white">24/7</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Access</div>
                        </div>
                        <div>
                            <div class="text-2xl font-display font-bold text-white">10k+</div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider mt-1">Players</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Visual (Live Match Card Mockup) -->
                <div class="relative lg:ml-auto w-full max-w-md">
                    <!-- Decorative ring -->
                    <div class="absolute inset-0 border-2 border-brand-500/20 rounded-3xl transform rotate-3 scale-105"></div>
                    
                    <div class="relative bg-surface-900 border border-surface-700 rounded-3xl p-6 shadow-2xl backdrop-blur-xl">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-display font-bold text-lg text-white">Live Match in Box 1</h3>
                            <span class="px-2.5 py-1 rounded bg-red-500/10 text-red-500 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                Live
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center mb-8">
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-full bg-surface-800 flex items-center justify-center mb-2 mx-auto border border-surface-700">
                                    <span class="font-display font-bold text-xl text-gray-300">KKR</span>
                                </div>
                                <div class="font-bold text-white text-xl">142/4</div>
                                <div class="text-xs text-gray-500">14.2 Overs</div>
                            </div>
                            
                            <div class="text-center px-4">
                                <span class="text-xs font-bold text-gray-500 uppercase">VS</span>
                            </div>
                            
                            <div class="text-center">
                                <div class="w-16 h-16 rounded-full bg-brand-500/10 flex items-center justify-center mb-2 mx-auto border border-brand-500/30">
                                    <span class="font-display font-bold text-xl text-brand-400">CSK</span>
                                </div>
                                <div class="font-bold text-white text-xl">--</div>
                                <div class="text-xs text-brand-500">To Bat</div>
                            </div>
                        </div>
                        
                        <div class="bg-surface-800 rounded-xl p-4 mb-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-400">Current RR</span>
                                <span class="font-bold text-white">9.90</span>
                            </div>
                            <div class="w-full bg-surface-950 rounded-full h-1.5">
                                <div class="bg-brand-500 h-1.5 rounded-full w-[70%]"></div>
                            </div>
                        </div>
                        
                        <p class="text-xs text-center text-gray-500">Box 1 is currently occupied until 8:00 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section id="facilities" class="py-24 bg-surface-900 border-y border-surface-800 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div class="max-w-2xl">
                    <span class="text-brand-500 font-bold tracking-wider uppercase text-sm mb-2 block">Our Arenas</span>
                    <h2 class="text-3xl md:text-5xl font-display font-bold text-white">Professional Grade <br/>Facilities</h2>
                </div>
                <div class="text-gray-400 max-w-sm md:text-right">
                    Designed for peak performance. Whether you're practicing in the nets or playing a tournament match.
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Box Cricket -->
                <div class="group relative bg-surface-850 border border-surface-700 rounded-2xl overflow-hidden hover:border-brand-500/50 transition-all duration-300 hover:shadow-glow">
                    <div class="h-48 bg-surface-950 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1531415074968-036ba1b575da?q=80&w=600" class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500" alt="Box Cricket">
                        <div class="absolute inset-0 bg-gradient-to-t from-surface-850 to-transparent"></div>
                        <div class="absolute bottom-4 left-4">
                            <span class="px-2 py-1 bg-surface-900/80 backdrop-blur text-brand-400 text-[10px] font-bold uppercase rounded">Premium</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-display text-xl font-bold text-white mb-2 group-hover:text-brand-400 transition-colors">Box Cricket Arena</h3>
                        <p class="text-sm text-gray-400 mb-6">Fully enclosed professional turf designed for high-intensity, fast-paced matches.</p>
                        <div class="flex items-end justify-between border-t border-surface-700 pt-4">
                            <div>
                                <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">From</div>
                                <div class="text-xl font-bold text-white">₹600<span class="text-xs text-gray-500 font-normal">/hr</span></div>
                            </div>
                            <a href="#book" class="w-8 h-8 rounded-full bg-surface-700 flex items-center justify-center group-hover:bg-brand-500 group-hover:text-surface-950 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Practice Net 1 -->
                <div class="group relative bg-surface-850 border border-surface-700 rounded-2xl overflow-hidden hover:border-brand-500/50 transition-all duration-300 hover:shadow-glow">
                    <div class="h-48 bg-surface-950 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-surface-800 to-surface-900 flex items-center justify-center opacity-80 group-hover:scale-105 transition-transform duration-500">
                             <svg class="w-16 h-16 text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-surface-850 to-transparent"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-display text-xl font-bold text-white mb-2 group-hover:text-brand-400 transition-colors">Pro Net 1</h3>
                        <p class="text-sm text-gray-400 mb-6">Equipped for focused batting and bowling drills with accurate bounce.</p>
                        <div class="flex items-end justify-between border-t border-surface-700 pt-4">
                            <div>
                                <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">From</div>
                                <div class="text-xl font-bold text-white">₹200<span class="text-xs text-gray-500 font-normal">/hr</span></div>
                            </div>
                            <a href="#book" class="w-8 h-8 rounded-full bg-surface-700 flex items-center justify-center group-hover:bg-brand-500 group-hover:text-surface-950 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                 <!-- Practice Net 2 -->
                 <div class="group relative bg-surface-850 border border-surface-700 rounded-2xl overflow-hidden hover:border-brand-500/50 transition-all duration-300 hover:shadow-glow">
                    <div class="h-48 bg-surface-950 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-surface-800 to-surface-900 flex items-center justify-center opacity-80 group-hover:scale-105 transition-transform duration-500">
                             <svg class="w-16 h-16 text-surface-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-surface-850 to-transparent"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-display text-xl font-bold text-white mb-2 group-hover:text-brand-400 transition-colors">Pro Net 2</h3>
                        <p class="text-sm text-gray-400 mb-6">Independent lane ideal for coaching sessions or one-on-one practice.</p>
                        <div class="flex items-end justify-between border-t border-surface-700 pt-4">
                            <div>
                                <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">From</div>
                                <div class="text-xl font-bold text-white">₹200<span class="text-xs text-gray-500 font-normal">/hr</span></div>
                            </div>
                            <a href="#book" class="w-8 h-8 rounded-full bg-surface-700 flex items-center justify-center group-hover:bg-brand-500 group-hover:text-surface-950 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Open Pitch -->
                <div class="group relative bg-surface-850 border border-surface-700 rounded-2xl overflow-hidden hover:border-brand-500/50 transition-all duration-300 hover:shadow-glow">
                    <div class="h-48 bg-surface-950 relative overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?q=80&w=600" class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-500" alt="Open Pitch">
                        <div class="absolute inset-0 bg-gradient-to-t from-surface-850 to-transparent"></div>
                        <div class="absolute bottom-4 left-4">
                            <span class="px-2 py-1 bg-accent-gold/20 text-accent-gold border border-accent-gold/30 text-[10px] font-bold uppercase rounded">1 Acre Field</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-display text-xl font-bold text-white mb-2 group-hover:text-brand-400 transition-colors">Championship Pitch</h3>
                        <p class="text-sm text-gray-400 mb-6">Massive open field perfect for full team 11v11 competitive matches.</p>
                        <div class="flex items-end justify-between border-t border-surface-700 pt-4">
                            <div>
                                <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">From</div>
                                <div class="text-xl font-bold text-white">₹1400<span class="text-xs text-gray-500 font-normal">/2hrs</span></div>
                            </div>
                            <a href="#book" class="w-8 h-8 rounded-full bg-surface-700 flex items-center justify-center group-hover:bg-brand-500 group-hover:text-surface-950 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Booking Widget Area -->
    <section id="book" class="py-24 bg-surface-950 relative">
        <!-- Glow effects -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-brand-500/5 rounded-full blur-[100px] pointer-events-none"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-12">
                <span class="text-brand-500 font-bold tracking-wider uppercase text-sm mb-2 block">Instant Reservation</span>
                <h2 class="text-3xl md:text-5xl font-display font-bold text-white">Secure Your Slot</h2>
                <p class="text-gray-400 mt-4 max-w-2xl mx-auto">Select a facility, pick an available time, and lock it in. Real-time availability ensures you never miss a chance to play.</p>
            </div>

            <!-- Elevated Livewire Booking Component -->
            <div class="bg-surface-900 border border-surface-700 rounded-3xl p-6 md:p-10 shadow-2xl relative overflow-hidden">
                <!-- Inner decoration -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
                
                <div class="relative z-10">
                    @livewire('booking-widget')
                </div>
            </div>
        </div>
    </section>

    <!-- Memberships Section -->
    <section id="memberships" class="py-24 bg-surface-900 border-t border-surface-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-accent-gold font-bold tracking-wider uppercase text-sm mb-2 block">Premium Access</span>
                <h2 class="text-3xl md:text-5xl font-display font-bold text-white">Club Memberships</h2>
                <p class="text-gray-400 mt-4 max-w-2xl mx-auto">Join the elite club. Get priority bookings, discounted slots, and complimentary kit rentals for your entire squad.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Silver -->
                <div class="bg-surface-950 border border-surface-800 rounded-3xl p-8 flex flex-col justify-between transition-transform duration-300 hover:-translate-y-2">
                    <div>
                        <h3 class="font-display text-2xl font-bold text-white mb-2">Silver</h3>
                        <p class="text-sm text-gray-400 mb-6">Perfect for casual cricket enthusiasts starting out.</p>
                        <div class="text-3xl font-display font-bold text-white mb-8">₹2,499<span class="text-sm text-gray-500 font-normal">/mo</span></div>
                        
                        <ul class="space-y-4 text-sm text-gray-300 mb-8">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-brand-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>4 Included Hours</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-brand-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>10% discount on extras</span>
                            </li>
                        </ul>
                    </div>
                    <form action="{{ route('payment.checkout-membership') }}" method="POST">
                        @csrf
                        <input type="hidden" name="membership_id" value="1">
                        <button type="submit" class="w-full bg-surface-800 hover:bg-surface-700 text-white font-bold py-3 px-4 rounded-xl transition-colors">
                            Select Plan
                        </button>
                    </form>
                </div>

                <!-- Gold (Popular) -->
                <div class="bg-surface-850 border-2 border-accent-gold rounded-3xl p-8 flex flex-col justify-between transform lg:-translate-y-4 shadow-[0_0_40px_rgba(251,191,36,0.1)] relative">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-accent-gold text-surface-950 text-xs font-bold uppercase tracking-wider py-1 px-4 rounded-full">
                        Most Popular
                    </div>
                    <div>
                        <h3 class="font-display text-2xl font-bold text-white mb-2">Gold</h3>
                        <p class="text-sm text-gray-400 mb-6">For regular groups playing weekly training matches.</p>
                        <div class="text-4xl font-display font-bold text-accent-gold mb-8">₹4,999<span class="text-sm text-gray-500 font-normal">/mo</span></div>
                        
                        <ul class="space-y-4 text-sm text-gray-300 mb-8">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-accent-gold mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-medium text-white">8 Included Hours</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-accent-gold mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>15% discount on extras</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-accent-gold mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Priority Booking windows</span>
                            </li>
                        </ul>
                    </div>
                    <form action="{{ route('payment.checkout-membership') }}" method="POST">
                        @csrf
                        <input type="hidden" name="membership_id" value="2">
                        <button type="submit" class="w-full bg-accent-gold hover:bg-yellow-400 text-surface-950 font-bold py-3 px-4 rounded-xl shadow-lg shadow-accent-gold/20 transition-all hover:-translate-y-0.5">
                            Select Plan
                        </button>
                    </form>
                </div>

                <!-- Platinum -->
                <div class="bg-surface-950 border border-surface-800 rounded-3xl p-8 flex flex-col justify-between transition-transform duration-300 hover:-translate-y-2">
                    <div>
                        <h3 class="font-display text-2xl font-bold text-white mb-2">Platinum</h3>
                        <p class="text-sm text-gray-400 mb-6">For cricket academies and regular match organizers.</p>
                        <div class="text-3xl font-display font-bold text-white mb-8">₹9,499<span class="text-sm text-gray-500 font-normal">/mo</span></div>
                        
                        <ul class="space-y-4 text-sm text-gray-300 mb-8">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-brand-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>16 Included Hours</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-brand-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>20% discount & Free Kit</span>
                            </li>
                        </ul>
                    </div>
                    <form action="{{ route('payment.checkout-membership') }}" method="POST">
                        @csrf
                        <input type="hidden" name="membership_id" value="3">
                        <button type="submit" class="w-full bg-surface-800 hover:bg-surface-700 text-white font-bold py-3 px-4 rounded-xl transition-colors">
                            Select Plan
                        </button>
                    </form>
                </div>
                
                <!-- Diamond -->
                <div class="bg-surface-950 border border-surface-800 rounded-3xl p-8 flex flex-col justify-between transition-transform duration-300 hover:-translate-y-2">
                    <div>
                        <h3 class="font-display text-2xl font-bold text-white mb-2">Diamond</h3>
                        <p class="text-sm text-gray-400 mb-6">Ultimate plan for teams, leagues, and tournaments.</p>
                        <div class="text-3xl font-display font-bold text-white mb-8">₹14,999<span class="text-sm text-gray-500 font-normal">/mo</span></div>
                        
                        <ul class="space-y-4 text-sm text-gray-300 mb-8">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-brand-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>24 Included Hours</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-brand-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>25% Weekend discounts</span>
                            </li>
                        </ul>
                    </div>
                    <form action="{{ route('payment.checkout-membership') }}" method="POST">
                        @csrf
                        <input type="hidden" name="membership_id" value="4">
                        <button type="submit" class="w-full bg-surface-800 hover:bg-surface-700 text-white font-bold py-3 px-4 rounded-xl transition-colors">
                            Select Plan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Interest Lead Generation Widget -->
    <section class="bg-surface-950 py-24 relative overflow-hidden border-t border-surface-800">
        <!-- Background decorative elements -->
        <div class="absolute right-0 top-0 w-1/3 h-full bg-gradient-to-l from-brand-500/5 to-transparent pointer-events-none"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="bg-surface-900 border border-surface-700 rounded-3xl p-8 md:p-12 shadow-2xl">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-display font-bold text-white mb-3">Waitlist & Alerts</h2>
                    <p class="text-gray-400">Can't find an available slot? Register your interest below. Our staff will notify you immediately if a slot opens up due to cancellation.</p>
                </div>

                @if(session('success_interest'))
                    <div class="mb-8 p-4 bg-brand-500/10 border border-brand-500/30 rounded-xl flex items-center gap-3">
                        <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-brand-400 font-medium">{{ session('success_interest') }}</span>
                    </div>
                @endif

                <form action="{{ route('interest.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="int_name" class="text-sm font-semibold text-gray-300">Full Name</label>
                            <input type="text" id="int_name" name="name" required class="w-full bg-surface-950 border border-surface-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors">
                        </div>
                        <div class="space-y-2">
                            <label for="int_mobile" class="text-sm font-semibold text-gray-300">Mobile Number</label>
                            <input type="text" id="int_mobile" name="mobile" required class="w-full bg-surface-950 border border-surface-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label for="int_fac" class="text-sm font-semibold text-gray-300">Facility</label>
                            <select id="int_fac" name="facility_id" required class="w-full bg-surface-950 border border-surface-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors appearance-none">
                                <option value="1">Box Cricket</option>
                                <option value="2">Practice Net 1</option>
                                <option value="3">Practice Net 2</option>
                                <option value="4">Open Pitch</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label for="int_date" class="text-sm font-semibold text-gray-300">Date</label>
                            <input type="date" id="int_date" name="interested_date" min="{{ now()->format('Y-m-d') }}" required class="w-full bg-surface-950 border border-surface-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors [color-scheme:dark]">
                        </div>
                        <div class="space-y-2">
                            <label for="int_time" class="text-sm font-semibold text-gray-300">Time Slot</label>
                            <input type="text" id="int_time" name="interested_time" placeholder="e.g. 18:00" required class="w-full bg-surface-950 border border-surface-700 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors">
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col items-center">
                        <button type="submit" class="w-full md:w-auto min-w-[200px] bg-brand-500 hover:bg-brand-400 text-surface-950 font-bold py-4 px-8 rounded-xl shadow-glow hover:shadow-glow-lg transition-all hover:-translate-y-0.5 text-center">
                            Set Alert
                        </button>
                        <p class="text-xs text-gray-500 mt-4 text-center max-w-sm">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            This is an interest form, not a booking confirmation. You will be notified via SMS if a slot opens.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-surface-950 border-t border-surface-800 pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Brand -->
                <div class="space-y-6">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-brand-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-surface-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <span class="text-xl font-display font-black tracking-wide text-white">
                            THE<span class="text-brand-500">CRICKET</span>HUB
                        </span>
                    </a>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Vijayawada's premium 2-acre outdoor cricket turf and tournament destination. Play cricket, your way.
                    </p>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="font-display font-bold text-white mb-6 uppercase tracking-wider text-sm">Contact Us</h4>
                    <ul class="space-y-4 text-sm text-gray-400">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Ambapuram, Vijayawada,<br>Andhra Pradesh, India</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>+91 99999 88888</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-brand-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>hello@thecrickethub.com</span>
                        </li>
                    </ul>
                </div>

                <!-- Hours -->
                <div>
                    <h4 class="font-display font-bold text-white mb-6 uppercase tracking-wider text-sm">Operational Hours</h4>
                    <ul class="space-y-4 text-sm">
                        <li class="flex justify-between items-center border-b border-surface-800 pb-2">
                            <span class="text-gray-400">Mon – Thu</span>
                            <span class="font-semibold text-white">6:00 AM – 11:00 PM</span>
                        </li>
                        <li class="flex justify-between items-center border-b border-surface-800 pb-2">
                            <span class="text-gray-400">Fri – Sun</span>
                            <span class="font-semibold text-white">6:00 AM – 12:00 AM</span>
                        </li>
                    </ul>
                </div>

                <!-- SEO Tags -->
                <div>
                    <h4 class="font-display font-bold text-white mb-6 uppercase tracking-wider text-sm">Popular Searches</h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs bg-surface-900 border border-surface-800 text-gray-400 px-3 py-1.5 rounded-lg hover:text-brand-400 transition-colors cursor-default">Box Cricket Vijayawada</span>
                        <span class="text-xs bg-surface-900 border border-surface-800 text-gray-400 px-3 py-1.5 rounded-lg hover:text-brand-400 transition-colors cursor-default">Cricket Nets Booking</span>
                        <span class="text-xs bg-surface-900 border border-surface-800 text-gray-400 px-3 py-1.5 rounded-lg hover:text-brand-400 transition-colors cursor-default">Open Cricket Ground</span>
                        <span class="text-xs bg-surface-900 border border-surface-800 text-gray-400 px-3 py-1.5 rounded-lg hover:text-brand-400 transition-colors cursor-default">Turf Ambapuram</span>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-surface-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500">
                <p>&copy; 2026 The Cricket Hub. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>
