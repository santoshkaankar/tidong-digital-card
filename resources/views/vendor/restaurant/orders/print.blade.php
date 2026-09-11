<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KOT / Receipt - #{{ $order->order_number }}</title>
    <style>
        body { font-family: monospace; width: 280px; margin: 0 auto; padding: 10px; font-size: 12px; }
        .text-center { text-align: center; }
        .border-bottom { border-bottom: 1px dashed #000; margin-bottom: 8px; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 4px 0; }
        .text-right { text-align: right; }
    </style>
</head>
<body onload="window.print(); window.location.href = '{{ route('vendor.restaurant.orders.index') }}';">

    <div class="text-center border-bottom">
        <h3 style="margin: 0;">KOT / RECEIPT</h3>
        <p style="margin: 4px 0;">Order #: {{ $order->order_number }}</p>
        <p style="margin: 0;">Type: {{ strtoupper($order->order_type) }} | Table: {{ $order->table->table_number ?? 'N/A' }}</p>
        <p style="margin: 4px 0;">Date: {{ $order->created_at->format('d-m-Y h:i A') }}</p>
    </div>

    <table class="border-bottom">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-bottom">
        <table>
            <tr>
                <td><strong>Grand Total:</strong></td>
                <td class="text-right"><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <p class="text-center" style="margin-top: 10px;">Thank You!</p>

</body>
</html>