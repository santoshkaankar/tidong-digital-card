<!-- Mobile Backdrop Overlay -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false" 
     class="fixed inset-0 bg-slate-900/50 z-40 md:hidden" 
     style="display: none;"></div>

<!-- Sidebar Container -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col shadow-xl transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:shadow-sm md:z-auto">
    
    <!-- Logo Header -->
    <div class="p-5 md:p-6 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-blue-200">
                <i class="fas fa-truck-fast"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 leading-tight">Delivery Hub</h2>
                <p class="text-xs font-semibold text-slate-400">Universal Dispatch</p>
            </div>
        </div>
        <!-- Close button on Mobile -->
        <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 p-1">
            <i class="fas fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <a href="{{ route('delivery.dashboard') }}" 
           class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('delivery.dashboard') ? 'text-blue-600 bg-blue-50/80 shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-chart-pie mr-3 w-5 text-center text-base"></i> Dashboard
        </a>

        <a href="{{ route('delivery.orders.index') }}" 
           class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('delivery.orders.*') ? 'text-blue-600 bg-blue-50/80 shadow-xs' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-box-open mr-3 w-5 text-center text-base"></i> Live Pickups
        </a>

        <div class="pt-4 pb-2 px-4 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Business Types</div>
        
        <a href="{{ route('delivery.orders.index', ['business_type' => 'restaurant']) }}" class="flex items-center px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition">
            <i class="fas fa-utensils mr-3 w-5 text-center text-orange-500"></i> Restaurant / Food
        </a>
        <a href="{{ route('delivery.orders.index', ['business_type' => 'grocery']) }}" class="flex items-center px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition">
            <i class="fas fa-basket-shopping mr-3 w-5 text-center text-emerald-500"></i> Grocery & Mart
        </a>
        <a href="{{ route('delivery.orders.index', ['business_type' => 'emporium']) }}" class="flex items-center px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-xl transition">
            <i class="fas fa-store mr-3 w-5 text-center text-purple-500"></i> Retail & Emporium
        </a>

        <div class="pt-4 pb-2 px-4 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Finance & Logs</div>

        <a href="{{ route('delivery.earnings.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('delivery.earnings.*') ? 'text-blue-600 bg-blue-50/80' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-wallet mr-3 w-5 text-center text-base"></i> Earnings & Payouts
        </a>
        <a href="{{ route('delivery.orders.history') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('delivery.orders.history') ? 'text-blue-600 bg-blue-50/80' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-clock-rotate-left mr-3 w-5 text-center text-base"></i> Delivery History
        </a>

        <div class="pt-4 pb-2 px-4 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Account Settings</div>

        <a href="{{ route('delivery.profile.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 hover:bg-slate-100 font-medium transition text-sm">
    <i class="fas fa-user text-slate-500"></i>
    <span>Profile</span>
</a>

        <form method="POST" action="{{ route('logout') }}" class="pt-3">
            @csrf
            <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition">
                <i class="fas fa-right-from-bracket mr-3 w-5 text-center"></i> Logout
            </button>
        </form>
    </nav>
</aside>