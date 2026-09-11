<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOT / Receipt - #{{ $order->order_number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 280px; 
            margin: 0 auto; 
            padding: 10px; 
            font-size: 12px;
            color: #000000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .border-bottom { 
            border-bottom: 1px dashed #000; 
            margin-bottom: 8px; 
            padding-bottom: 8px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            text-align: left; 
            padding: 4px 0; 
            vertical-align: top;
        }
        @media print {
            body { 
                width: 100%; 
                padding: 0; 
            }
            @page { 
                margin: 0; 
            }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="text-center border-bottom">
        <h3 style="margin: 0; font-size: 16px;">KOT / RECEIPT</h3>
        <p style="margin: 4px 0;">Order #: <strong>{{ $order->order_number }}</strong></p>
        <p style="margin: 0;">Type: <strong>{{ strtoupper(str_replace('_', ' ', $order->order_type)) }}</strong> | Table: <strong>{{ $order->table->table_number ?? 'N/A' }}</strong></p>
        <p style="margin: 4px 0;">Date: {{ $order->created_at->format('d-m-Y h:i A') }}</p>
    </div>

    <table class="border-bottom">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-center" style="width: 40px;">Qty</th>
                <th class="text-right" style="width: 70px;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                @php
                    $name = $item->item_name 
                            ?? $item->item->name 
                            ?? $item->item->globalItem->item_name 
                            ?? 'Item';
                @endphp
                <tr>
                    <td>{{ $name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->subtotal ?? ($item->price * $item->quantity), 2) }}</td>
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

    <p class="text-center" style="margin-top: 12px; margin-bottom: 0;">*** Thank You! ***</p>

    <script>
        // Redirect back to orders list after closing or completing the print window
        window.onafterprint = function() {
            window.location.href = "{{ route('vendor.restaurant.orders.index') }}";
        };
    </script>
</body>
</html>