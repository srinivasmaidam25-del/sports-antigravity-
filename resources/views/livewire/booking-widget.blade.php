<div class="space-y-6">
    
    <!-- 1. Select Facility -->
    <div class="space-y-3">
        <label class="text-sm font-semibold uppercase tracking-wider text-emerald-400">Select Facility</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($facilities as $facility)
                <label class="relative flex flex-col p-4 bg-gray-900 border border-gray-800 rounded-xl cursor-pointer select-none transition-all duration-200 hover:border-emerald-500/50 hover:scale-[1.07] active:scale-[0.98] @if($selectedFacilityId == $facility->id) border-emerald-500 bg-emerald-950/10 shadow-lg shadow-emerald-500/10 @endif">
                    <input type="radio" wire:model.live="selectedFacilityId" value="{{ $facility->id }}" class="sr-only">
                    <span class="font-bold text-white text-base">{{ $facility->name }}</span>
                    <span class="text-xs text-gray-400 mt-1">{{ $facility->dimensions }} • {{ $facility->duration_minutes }} Mins</span>
                    <span class="text-sm font-bold text-emerald-400 mt-3">₹{{ number_format($facility->base_price_weekday, 0) }}+</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Date, Slots, and Options -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Date Picker -->
            <div class="bg-gray-900 border border-gray-800 p-5 rounded-xl space-y-3">
                <label for="booking_date" class="text-sm font-semibold uppercase tracking-wider text-emerald-400">Select Date</label>
                <input type="date" id="booking_date" wire:model.live="selectedDate" min="{{ now()->format('Y-m-d') }}" class="w-full bg-gray-950 border border-gray-800 text-white rounded-lg p-3 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
            </div>

            <!-- Available Slots Grid -->
            <div class="bg-gray-900 border border-gray-800 p-5 rounded-xl space-y-4">
                <label class="text-sm font-semibold uppercase tracking-wider text-emerald-400 block">Available Slots</label>
                
                @if(empty($availableSlots))
                    <p class="text-gray-500 text-sm">No available slots for the selected date.</p>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($availableSlots as $slot)
                            <button type="button" 
                                    @if($slot['available']) wire:click="selectSlot('{{ $slot['start'] }}', '{{ $slot['end'] }}')" @endif
                                    class="p-3 rounded-lg border text-sm transition-all duration-200 text-center flex flex-col items-center justify-center font-medium
                                    @if(!$slot['available']) 
                                        bg-gray-950/40 border-gray-900 text-gray-600 cursor-not-allowed
                                    @elseif($selectedStart === $slot['start'])
                                        bg-emerald-500 text-gray-950 border-emerald-400 font-bold scale-[1.08] shadow-md shadow-emerald-500/20
                                    @else
                                        bg-gray-950 border-gray-850 hover:border-emerald-500/50 hover:bg-gray-900 text-gray-200 hover:scale-[1.08] active:scale-[0.95]
                                    @endif">
                                <span>{{ Carbon\Carbon::parse($slot['start'])->format('h:i A') }}</span>
                                @if(!$slot['available'])
                                    <span class="text-[10px] uppercase font-bold text-rose-500/60 mt-1">Occupied</span>
                                @else
                                    <span class="text-[10px] uppercase font-bold text-emerald-400 mt-1">Available</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Payment Type Select -->
            <div class="bg-gray-900 border border-gray-800 p-5 rounded-xl space-y-4">
                <label class="text-sm font-semibold uppercase tracking-wider text-emerald-400 block">Payment Options</label>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <label class="relative flex flex-col p-4 bg-gray-950 border border-gray-850 rounded-lg cursor-pointer transition-all duration-200 hover:border-emerald-500/40 hover:scale-[1.06] active:scale-[0.98] @if($paymentType === 'FULL') border-emerald-500 bg-emerald-950/15 @endif">
                        <input type="radio" wire:model.live="paymentType" value="FULL" class="sr-only">
                        <span class="font-bold text-white text-sm">Full Payment</span>
                        <span class="text-xs text-gray-400 mt-1">100% Online Checkout</span>
                    </label>

                    <label class="relative flex flex-col p-4 bg-gray-950 border border-gray-850 rounded-lg cursor-pointer transition-all duration-200 hover:border-emerald-500/40 hover:scale-[1.06] active:scale-[0.98] @if($paymentType === 'ADVANCE') border-emerald-500 bg-emerald-950/15 @endif">
                        <input type="radio" wire:model.live="paymentType" value="ADVANCE" class="sr-only">
                        <span class="font-bold text-white text-sm">Advance Deposit</span>
                        <span class="text-xs text-gray-400 mt-1">Pay hold fee, rest at venue</span>
                    </label>

                    @if(!empty($userMemberships))
                        <label class="relative flex flex-col p-4 bg-gray-950 border border-gray-850 rounded-lg cursor-pointer transition-all duration-200 hover:border-emerald-500/40 hover:scale-[1.06] active:scale-[0.98] @if($paymentType === 'MEMBERSHIP') border-emerald-500 bg-emerald-950/15 @endif">
                            <input type="radio" wire:model.live="paymentType" value="MEMBERSHIP" class="sr-only">
                            <span class="font-bold text-white text-sm flex items-center">
                                Membership
                                <span class="ml-1.5 w-2 h-2 bg-emerald-400 rounded-full animate-ping"></span>
                            </span>
                            <span class="text-xs text-gray-400 mt-1">Use remaining package hours</span>
                        </label>
                    @endif
                </div>

                <!-- Membership Sub-Selector -->
                @if($paymentType === 'MEMBERSHIP' && !empty($userMemberships))
                    <div class="bg-gray-950 p-4 rounded-lg border border-gray-850 space-y-2 mt-2">
                        <label for="membership_id" class="text-xs text-gray-400">Select Active Membership Package</label>
                        <select id="membership_id" wire:model.live="selectedUserMembershipId" class="w-full bg-gray-900 border border-gray-800 text-white rounded p-2 text-sm focus:border-emerald-500 focus:outline-none">
                            @foreach($userMemberships as $userMemb)
                                <option value="{{ $userMemb->id }}">{{ $userMemb->membership->name }} Tier ({{ $userMemb->hours_remaining }} hrs remaining)</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right Col: Summary & Checkout Forms -->
        <div class="space-y-6">
            
            <!-- Price Summary & Checkout Action -->
            <div class="bg-gray-900 border border-gray-800 p-5 rounded-xl space-y-6">
                <h3 class="font-bold text-white border-b border-gray-800 pb-3">Booking Summary</h3>

                @if(!$selectedStart)
                    <div class="text-center py-6 text-gray-500 text-sm">
                        Please select a date and slot to review pricing summary.
                    </div>
                @else
                    <div class="space-y-4 text-sm">
                        <!-- Summary values -->
                        <div class="space-y-2 border-b border-gray-800 pb-4">
                            <div class="flex justify-between text-gray-400">
                                <span>Subtotal</span>
                                <span>₹{{ number_format($originalPrice, 2) }}</span>
                            </div>
                            @if($discountAmount > 0)
                                <div class="flex justify-between text-emerald-400">
                                    <span>Discount / Applied Hour credit</span>
                                    <span>-₹{{ number_format($discountAmount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-white font-semibold text-base pt-2">
                                <span>Total Price</span>
                                <span>₹{{ number_format($finalPrice, 2) }}</span>
                            </div>
                        </div>

                        <!-- Coupon Form -->
                        @if($paymentType !== 'MEMBERSHIP')
                            <div class="space-y-2">
                                <label class="text-xs text-gray-400">Apply Promo Code</label>
                                <div class="flex space-x-2">
                                    <input type="text" wire:model.live.debounce.300ms="couponCode" placeholder="ENTER CODE" class="w-full bg-gray-950 border border-gray-800 text-white rounded px-3 py-2 text-sm uppercase outline-none focus:border-emerald-500">
                                </div>
                                @if($couponError)
                                    <p class="text-xs text-rose-500">{{ $couponError }}</p>
                                @endif
                                @if($couponSuccess)
                                    <p class="text-xs text-emerald-500">{{ $couponSuccess }}</p>
                                @endif
                            </div>
                        @endif

                        <!-- Notes Field -->
                        <div class="space-y-1 pt-2">
                            <label class="text-xs text-gray-400">Special Notes (Optional)</label>
                            <textarea wire:model.live="notes" rows="2" placeholder="Any specific requirements?" class="w-full bg-gray-950 border border-gray-800 text-white rounded p-2 text-sm outline-none focus:border-emerald-500"></textarea>
                        </div>

                        <!-- Final Checkout Form Trigger -->
                        <div class="pt-4">
                            @auth
                                <form action="{{ route('payment.checkout') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="facility_id" value="{{ $selectedFacilityId }}">
                                    <input type="hidden" name="booking_date" value="{{ $selectedDate }}">
                                    <input type="hidden" name="start_time" value="{{ $selectedStart }}">
                                    <input type="hidden" name="end_time" value="{{ $selectedEnd }}">
                                    <input type="hidden" name="payment_type" value="{{ $paymentType }}">
                                    <input type="hidden" name="coupon_code" value="{{ $couponSuccess ? $couponCode : '' }}">
                                    <input type="hidden" name="user_membership_id" value="{{ $paymentType === 'MEMBERSHIP' ? $selectedUserMembershipId : '' }}">
                                    <input type="hidden" name="notes" value="{{ $notes }}">

                                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 hover:scale-[1.07] active:scale-[0.98] text-gray-950 font-bold py-3.5 px-4 rounded-xl transition-all duration-200 shadow-lg shadow-emerald-500/25 text-center block">
                                        Proceed to Checkout
                                    </button>
                                </form>
                            @else
                                <div class="space-y-3">
                                    <p class="text-xs text-center text-gray-500">Please sign in to complete your booking reservation.</p>
                                    <a href="{{ route('login') }}" class="w-full bg-gray-800 hover:bg-gray-750 hover:scale-[1.07] active:scale-[0.98] text-white font-bold py-3.5 px-4 rounded-xl transition-all duration-200 text-center block">
                                        Sign In / Register
                                    </a>
                                </div>
                            @endauth
                        </div>

                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
