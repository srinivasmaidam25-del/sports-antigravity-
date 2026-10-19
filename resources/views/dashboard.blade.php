<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-xl text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-surface-900 border border-surface-800 overflow-hidden shadow-lg sm:rounded-2xl">
                <div class="p-8 text-gray-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-full bg-brand-500/20 border border-brand-500/50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-display font-bold text-white">Welcome Back!</h3>
                            <p class="text-sm text-gray-400">{{ __("You're successfully logged in.") }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
