<aside class="w-64 bg-white border-r border-gray-200 hidden md:flex flex-col shadow-sm">
    <!-- Brand Header -->
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-xl font-bold text-blue-600">Delivery Hub</h2>
        <p class="text-xs text-gray-500 mt-0.5">Universal Multi-Business</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
        <!-- Dashboard -->
        <a href="{{ route('delivery.dashboard') }}" class="flex items-center px-4 py-2.5 text-sm font-semibold text-blue-600 bg-blue-50 rounded-lg transition">
            <i class="fas fa-home mr-3 w-5 text-center"></i> Dashboard
        </a>

        <!-- Live Orders & Marketplace Requests -->
        <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition">
            <i class="fas fa-shopping-bag mr-3 w-5 text-center"></i> Live Orders / Pickups
        </a>

        <!-- Category Wise Deliveries -->
        <div class="pt-2 pb-1 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Business Types</div>
        
        <a href="#" class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition">
            <i class="fas fa-utensils mr-3 w-5 text-center text-orange-500"></i> Restaurant / Food
        </a>
        <a href="#" class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition">
            <i class="fas fa-carrot mr-3 w-5 text-center text-green-500"></i> Grocery & Mart
        </a>
        <a href="#" class="flex items-center px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition">
            <i class="fas fa-store mr-3 w-5 text-center text-purple-500"></i> Retail Stores
        </a>

        <!-- Management & Earnings -->
        <div class="pt-2 pb-1 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Finance & Logs</div>

        <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition">
            <i class="fas fa-wallet mr-3 w-5 text-center"></i> Earnings & Payouts
        </a>
        <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition">
            <i class="fas fa-history mr-3 w-5 text-center"></i> Delivery History
        </a>

        <!-- Account Settings -->
        <div class="pt-2 pb-1 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Settings</div>

        <a href="#" class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg transition">
            <i class="fas fa-user-cog mr-3 w-5 text-center"></i> Profile & Documents
        </a>

        <!-- Logout Option -->
        <form method="POST" action="{{ route('logout') }}" class="pt-4">
            @csrf
            <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition text-left">
                <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i> Logout
            </button>
        </form>
    </nav>
</aside>