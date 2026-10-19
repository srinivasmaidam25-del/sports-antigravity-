<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'The Cricket Hub') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
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
                                500: '#22c55e',
                                600: '#16a34a',
                            },
                            surface: {
                                800: '#1e293b',
                                850: '#151e2e',
                                900: '#0f172a',
                                950: '#020617',
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
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            h1, h2, h3, h4, h5, h6, .font-display { font-family: 'Outfit', sans-serif; }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-200 bg-surface-950 selection:bg-brand-500 selection:text-white">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-surface-900 border-b border-surface-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
