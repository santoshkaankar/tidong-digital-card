<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
    <!-- Today's Completed -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-500">Today Delivered</span>
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-black text-slate-900">{{ $todayDeliveries ?? 0 }}</p>
    </div>

    <!-- Active Ongoing -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-500">Active Pickups</span>
            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                <i class="fas fa-truck-ramp-box"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-black text-slate-900">{{ $activeDeliveries ?? 0 }}</p>
    </div>

    <!-- Ready Pickups -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-500">Ready Pickups</span>
            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-black text-slate-900">{{ $availablePickups ?? 0 }}</p>
    </div>

    <!-- Total Earnings -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-slate-500">Total Earnings</span>
            <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-sm">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
        <p class="text-xl md:text-2xl font-black text-slate-900">₹{{ number_format($totalEarnings ?? 0, 2) }}</p>
    </div>
</div>