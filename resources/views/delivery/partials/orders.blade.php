<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
            <i class="fas fa-list-check text-blue-600"></i> Available Pickup Requests
        </h3>
        <span class="text-xs font-semibold px-3 py-1 bg-slate-100 text-slate-600 rounded-full">Live Feed</span>
    </div>
    
    <div class="space-y-4">
        @forelse($availableOrders ?? [] as $order)
            @php
                $badgeClasses = match($order->business_type ?? 'restaurant') {
                    'grocery' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                    'emporium', 'retail' => 'text-purple-700 bg-purple-50 border-purple-200',
                    default => 'text-orange-700 bg-orange-50 border-orange-200',
                };
            @endphp
            <div class="border border-slate-200 rounded-xl p-5 hover:border-blue-300 hover:shadow-md transition-all bg-slate-50/30">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider border {{ $badgeClasses }}">
                            {{ ucfirst($order->business_type ?? 'Restaurant') }} Order #{{ $order->order_number ?? $order->id }}
                        </span>
                        <h4 class="font-bold text-slate-900 text-base mt-2">{{ $order->vendor->name ?? 'Store Partner' }}</h4>
                        <p class="text-xs text-slate-500 font-medium mt-0.5"><i class="fas fa-location-dot text-rose-500 me-1"></i> Pickup: {{ $order->vendor->address ?? 'Market Location' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-extrabold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-100 inline-block">
                            +₹{{ number_format($order->delivery_fee ?? 45, 2) }}
                        </span>
                    </div>
                </div>
                
                <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-xs">
                    <span class="text-slate-600">
                        Customer: <strong class="text-slate-900 font-semibold">{{ $order->customer_name ?? 'Customer' }}</strong> 
                        <span class="text-slate-400 mx-1">•</span> 
                        Drop: <span class="text-slate-700">{{ $order->delivery_address ?? 'Customer Address' }}</span>
                    </span>
                    
                    <form action="{{ route('delivery.orders.accept', $order->id) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                            Accept Pickup Request
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-slate-400">
                <i class="fas fa-box-open text-4xl mb-3 text-slate-300"></i>
                <h6 class="text-sm font-bold text-slate-600 mb-1">No Active Pickup Requests</h6>
                <p class="text-xs text-slate-400">New delivery orders will appear here automatically when available.</p>
            </div>
        @endforelse
    </div>
</div>