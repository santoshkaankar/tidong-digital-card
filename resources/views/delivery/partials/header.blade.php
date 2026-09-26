<header class="bg-white border-b border-slate-200 px-4 md:px-6 py-3 md:py-4 shadow-xs sticky top-0 z-10 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <!-- Mobile Menu Toggle Button -->
        <button @click="sidebarOpen = true" class="md:hidden p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg focus:outline-none">
            <i class="fas fa-bars text-lg"></i>
        </button>
        
        <div>
            <h1 class="text-base md:text-xl font-bold text-slate-900 tracking-tight leading-tight">Universal Delivery Hub</h1>
            <p class="text-[11px] md:text-xs text-slate-500 font-medium hidden sm:block">Manage order fulfillment across all partner outlets.</p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <form action="{{ route('delivery.profile.toggle-duty') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="px-3 md:px-4 py-1.5 md:py-2 text-[11px] md:text-xs font-bold rounded-full transition-all flex items-center gap-1.5 md:gap-2 border shadow-xs {{ auth()->user()->is_online ?? true ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}">
                <span class="w-2 h-2 md:w-2.5 md:h-2.5 rounded-full animate-pulse {{ auth()->user()->is_online ?? true ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                <span class="hidden sm:inline">Duty Status:</span> {{ auth()->user()->is_online ?? true ? 'Online' : 'Offline' }}
            </button>
        </form>
    </div>
</header>