@extends('delivery.partials.layout')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 px-4 py-3 rounded-2xl text-xs font-semibold backdrop-blur-md">
            <i class="fas fa-circle-check text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 bg-rose-500/10 border border-rose-500/20 text-rose-600 px-4 py-3 rounded-2xl text-xs font-semibold backdrop-blur-md">
            <i class="fas fa-circle-exclamation text-base"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- SECTION 1: ON-GOING DELIVERIES (ACTIVE) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                </span>
                Active Pickup / On-Going Delivery
            </h3>
            <span class="text-[11px] font-bold px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full border border-amber-200">
                {{ count($activeOrders) }} Active
            </span>
        </div>

        @forelse($activeOrders as $order)
            @php
                // Store/Restaurant Details
                $storeName = $order->restaurant->name ?? $order->vendor->name ?? 'Partner Store';
                $storeAddress = $order->restaurant->address ?? $order->vendor->address ?? 'Outlet Location';
                $storePhone = $order->restaurant->phone ?? $order->vendor->phone ?? '';

                // Customer Details
                $custName = $order->customer_name ?? $order->user->name ?? 'Customer';
                $custPhone = $order->customer_phone ?? $order->user->phone ?? 'N/A';
                $custAddress = $order->delivery_address ?? 'Customer Location';

                // Navigation URLs for Google Maps
                $pickupMapUrl = ($order->restaurant->latitude ?? false) 
                    ? "https://www.google.com/maps/dir/?api=1&destination={$order->restaurant->latitude},{$order->restaurant->longitude}"
                    : "https://www.google.com/maps/search/?api=1&query=" . urlencode($storeAddress);

                $deliveryMapUrl = ($order->latitude ?? false) 
                    ? "https://www.google.com/maps/dir/?api=1&destination={$order->latitude},{$order->longitude}"
                    : "https://www.google.com/maps/search/?api=1&query=" . urlencode($custAddress);
            @endphp

            <div class="bg-gradient-to-br from-amber-500/5 via-white to-slate-50 border-2 border-amber-400/40 rounded-3xl p-5 shadow-lg relative overflow-hidden space-y-4">
                
                <!-- Status Header -->
                <div class="flex justify-between items-start pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] uppercase font-black px-2.5 py-1 {{ in_array($order->status, ['pickedup', 'picked_up', 'on_the_way']) ? 'bg-emerald-500' : 'bg-amber-500' }} text-white rounded-lg tracking-wider">
                            {{ in_array($order->status, ['pickedup', 'picked_up', 'on_the_way']) ? 'On The Way To Customer' : 'Ready For Pickup' }}
                        </span>
                        <h4 class="text-lg font-black text-slate-900 mt-2">Order #{{ $order->id }}</h4>
                        <p class="text-xs font-semibold text-slate-500"><i class="fas fa-store text-amber-500 mr-1"></i> {{ $storeName }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-black text-emerald-600">₹{{ number_format($order->delivery_fee ?? 45, 2) }}</span>
                        <span class="block text-[10px] text-slate-400 font-bold uppercase">Earning</span>
                    </div>
                </div>

                <!-- Pickup & Drop Locations with Live Navigation -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <!-- Pickup Location -->
                    <div class="bg-amber-50/50 p-3.5 rounded-2xl border border-amber-200/60 space-y-2">
                        <div class="flex justify-between items-start">
                            <span class="text-[10px] text-amber-800 font-black uppercase"><i class="fas fa-store mr-1"></i> 1. Pickup Store</span>
                            <a href="{{ $pickupMapUrl }}" target="_blank" class="px-2.5 py-1 bg-amber-500 text-white rounded-lg text-[10px] font-bold shadow-xs hover:bg-amber-600 flex items-center gap-1">
                                <i class="fas fa-location-arrow"></i> Map
                            </a>
                        </div>
                        <p class="font-bold text-slate-900 text-xs">{{ $storeName }}</p>
                        <p class="text-slate-600 line-clamp-2 text-[11px]">{{ $storeAddress }}</p>
                        @if($storePhone)
                            <a href="tel:{{ $storePhone }}" class="inline-flex items-center gap-1 text-amber-700 font-bold text-[11px]">
                                <i class="fas fa-phone-volume"></i> Call Store
                            </a>
                        @endif
                    </div>

                    <!-- Drop Location -->
                    <div class="bg-blue-50/50 p-3.5 rounded-2xl border border-blue-200/60 space-y-2">
                        <div class="flex justify-between items-start">
                            <span class="text-[10px] text-blue-800 font-black uppercase"><i class="fas fa-user-location mr-1"></i> 2. Delivery Drop</span>
                            <a href="{{ $deliveryMapUrl }}" target="_blank" class="px-2.5 py-1 bg-blue-600 text-white rounded-lg text-[10px] font-bold shadow-xs hover:bg-blue-700 flex items-center gap-1">
                                <i class="fas fa-directions"></i> Map
                            </a>
                        </div>
                        <p class="font-bold text-slate-900 text-xs">{{ $custName }}</p>
                        <p class="text-slate-600 line-clamp-2 text-[11px]">{{ $custAddress }}</p>
                        <a href="tel:{{ $custPhone }}" class="inline-flex items-center gap-1 text-blue-600 font-bold text-[11px]">
                            <i class="fas fa-phone-volume"></i> Call Customer ({{ $custPhone }})
                        </a>
                    </div>
                </div>

                <!-- WORKFLOW STEP 1: PICKUP VERIFICATION (Show OTP to Store) -->
                @if(in_array($order->status, ['accepted', 'out_for_delivery']))
                    <div class="bg-slate-900 text-white p-4 rounded-2xl space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <span class="text-xs font-bold text-amber-400"><i class="fas fa-key mr-1"></i> Step 1: Restaurant Pickup OTP</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-amber-500/20 text-amber-300 rounded border border-amber-500/30">Show Code To Outlet</span>
                        </div>
                        
                        <div class="bg-slate-800 border border-slate-700 p-3 rounded-xl text-center">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Show this OTP to Store Counter</span>
                            <span class="text-3xl font-black text-amber-400 tracking-widest font-mono">{{ $order->pickup_otp ?? rand(1000, 9999) }}</span>
                        </div>

                        <!-- Confirm Pickup Status Button -->
                        <form action="{{ route('delivery.orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="pickedup">
                            <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-md transition uppercase tracking-wider flex items-center justify-center gap-2">
                                <i class="fas fa-box-check"></i> Food Picked Up & Start Navigation To Customer
                            </button>
                        </form>
                    </div>

                <!-- WORKFLOW STEP 2: CUSTOMER DELIVERY & VERIFICATION (Get OTP from Customer) -->
                @elseif(in_array($order->status, ['pickedup', 'picked_up', 'on_the_way']))
                    <form action="{{ route('delivery.orders.complete', $order->id) }}" method="POST" class="bg-slate-900 text-white p-4 rounded-2xl space-y-4">
                        @csrf
                        
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <span class="text-xs font-bold text-emerald-400"><i class="fas fa-shield-check mr-1"></i> Step 2: Verify Customer OTP & Complete</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-500/20 text-emerald-300 rounded border border-emerald-500/30">Ask Customer For OTP</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 items-end">
                            <!-- Customer Delivery OTP -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 mb-1">Enter 4-Digit Customer OTP</label>
                                <input type="text" name="delivery_otp" maxlength="4" placeholder="••••" required 
                                       class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-center text-xl font-mono tracking-widest text-amber-400 focus:outline-none focus:border-amber-400">
                            </div>

                            <!-- Cash Collection Badge (If COD) -->
                            @if(strtolower($order->payment_method ?? 'cod') === 'cod')
                                <div class="bg-amber-500/10 border border-amber-500/30 p-2.5 rounded-xl flex items-center justify-between">
                                    <div>
                                        <span class="block text-[10px] font-bold text-amber-400 uppercase">Collect Cash</span>
                                        <span class="text-base font-black text-amber-300">₹{{ number_format($order->sub_total ?? $order->total_amount ?? 0, 2) }}</span>
                                    </div>
                                    <label class="flex items-center gap-2 cursor-pointer bg-amber-400 text-slate-950 px-3 py-1.5 rounded-lg text-xs font-bold shadow-xs">
                                        <input type="checkbox" name="cash_collected" value="1" required class="rounded text-slate-900 focus:ring-0">
                                        Cash Recd.
                                    </label>
                                </div>
                            @else
                                <div class="bg-emerald-500/10 border border-emerald-500/30 p-3 rounded-xl flex items-center justify-between text-emerald-400 text-xs font-bold">
                                    <span><i class="fas fa-circle-check mr-1"></i> Prepaid Order</span>
                                    <span>No Cash Collection</span>
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-extrabold rounded-xl shadow-lg shadow-emerald-500/20 transition-all text-sm uppercase tracking-wider flex items-center justify-center gap-2">
                            <i class="fas fa-circle-check text-base"></i> Verify OTP & Complete Delivery
                        </button>
                    </form>
                @endif

            </div>
        @empty
            <div class="bg-white rounded-3xl border border-slate-200/80 p-8 text-center shadow-xs">
                <div class="w-14 h-14 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">
                    <i class="fas fa-truck-fast"></i>
                </div>
                <h5 class="text-sm font-bold text-slate-700">No Active Deliveries Right Now</h5>
                <p class="text-xs text-slate-400 mt-0.5">Accept an order below to start your delivery process.</p>
            </div>
        @endforelse
    </div>

    <hr class="border-slate-200/60 my-6">

    <!-- SECTION 2: AVAILABLE ORDERS FEED -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-bolt text-amber-500"></i> Available Orders
                </h3>
                <p class="text-xs text-slate-500 font-medium">New orders ready for pickup near you</p>
            </div>
            <span class="text-xs font-extrabold px-3 py-1 bg-blue-50 text-blue-600 rounded-full border border-blue-100">
                {{ count($availableOrders) }} Live
            </span>
        </div>

        <div class="grid grid-cols-1 gap-4">
            @forelse($availableOrders as $order)
                @php
                    $businessType = strtolower($order->business_type ?? 'restaurant');
                    $badgeStyle = match($businessType) {
                        'grocery' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'emporium', 'retail' => 'bg-purple-50 text-purple-700 border-purple-200',
                        default => 'bg-orange-50 text-orange-700 border-orange-200',
                    };
                    $typeIcon = match($businessType) {
                        'grocery' => 'fa-basket-shopping',
                        'emporium', 'retail' => 'fa-store',
                        default => 'fa-utensils',
                    };

                    $availStoreName = $order->restaurant->name ?? $order->vendor->name ?? 'Partner Store';
                    $availStoreAddress = $order->restaurant->address ?? $order->vendor->address ?? 'Outlet Location';

                    $availPickupMapUrl = ($order->restaurant->latitude ?? false) 
                        ? "https://www.google.com/maps/dir/?api=1&destination={$order->restaurant->latitude},{$order->restaurant->longitude}"
                        : "https://www.google.com/maps/search/?api=1&query=" . urlencode($availStoreAddress);
                @endphp

                <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs hover:shadow-md hover:border-blue-400 transition-all group">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        
                        <!-- Order Info -->
                        <div class="space-y-2 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-lg border {{ $badgeStyle }} inline-flex items-center gap-1.5">
                                    <i class="fas {{ $typeIcon }}"></i> {{ ucfirst($businessType) }}
                                </span>
                                <span class="text-xs font-black text-slate-900">#{{ $order->id }}</span>
                                <span class="text-[10px] text-slate-400 font-bold">• {{ $order->created_at ? $order->created_at->diffForHumans() : 'Just Now' }}</span>
                            </div>

                            <!-- Pickup Store Info & Map -->
                            <div>
                                <h4 class="font-extrabold text-slate-900 text-base group-hover:text-blue-600 transition-colors">
                                    {{ $availStoreName }}
                                </h4>
                                <p class="text-xs text-slate-500 font-medium flex items-center gap-1 mt-0.5">
                                    <i class="fas fa-location-dot text-rose-500"></i>
                                    Pickup: <span class="text-slate-700">{{ $availStoreAddress }}</span>
                                    <a href="{{ $availPickupMapUrl }}" target="_blank" class="ml-2 text-blue-600 font-bold hover:underline flex items-center gap-1">
                                        <i class="fas fa-location-arrow text-[10px]"></i> Map
                                    </a>
                                </p>
                            </div>
                        </div>

                        <!-- Earnings & Action -->
                        <div class="flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100 gap-3">
                            <div class="text-left sm:text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase block">Delivery Pay</span>
                                <span class="text-xl font-black text-emerald-600 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-100 inline-block">
                                    +₹{{ number_format($order->delivery_fee ?? 45, 2) }}
                                </span>
                            </div>

                            <form action="{{ route('delivery.orders.accept', $order->id) }}" method="POST" class="w-auto">
                                @csrf
                                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-extrabold text-xs rounded-2xl shadow-md shadow-blue-500/20 transition-all flex items-center gap-2">
                                    <span>Accept Pickup</span>
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center shadow-xs">
                    <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h5 class="text-base font-extrabold text-slate-800">No Orders Available Right Now</h5>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">New order requests from restaurants and stores will show up here live automatically.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection