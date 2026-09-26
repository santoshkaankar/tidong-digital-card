@extends('delivery.partials.layout')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Delivery History</h2>
            <p class="text-xs text-slate-500">View all completed and past fulfillment records.</p>
        </div>
    </div>

    <!-- History Table or Empty State -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6">
        @if(isset($orders) && count($orders) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-100">
                        <tr>
                            <th class="p-3">Order ID</th>
                            <th class="p-3">Customer</th>
                            <th class="p-3">Date</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Fee</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-3 font-bold text-slate-900">#{{ $order->id }}</td>
                                <td class="p-3 text-slate-700">{{ $order->customer_name ?? 'N/A' }}</td>
                                <td class="p-3 text-slate-500">{{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">Delivered</span>
                                </td>
                                <td class="p-3 text-right font-bold text-emerald-600">₹{{ number_format($order->delivery_fee ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700 mb-1">No Delivery History Available</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">Your past completed orders and delivery activity logs will automatically be listed here.</p>
            </div>
        @endif
    </div>
</div>
@endsection