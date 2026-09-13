<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Receipt') }} - #{{ $order->order_number }}</title>
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
        <h3 style="margin: 0; font-size: 16px;">{{ __('KOT / RECEIPT') }}</h3>
        <p style="margin: 4px 0;">{{ __('Order #:') }} <strong>{{ $order->order_number }}</strong></p>
        <p style="margin: 0;">
            {{ __('Type:') }} <strong>{{ __(strtoupper(str_replace('_', ' ', $order->order_type))) }}</strong> | 
            {{ __('Table:') }} <strong>{{ $order->table->table_number ?? __('N/A') }}</strong>
        </p>
        <p style="margin: 4px 0;">{{ __('Date:') }} {{ $order->created_at->format('d-m-Y h:i A') }}</p>
    </div>

    <table class="border-bottom">
        <thead>
            <tr>
                <th>{{ __('Item') }}</th>
                <th class="text-center" style="width: 40px;">{{ __('Qty') }}</th>
                <th class="text-right" style="width: 70px;">{{ __('Price') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                @php
                    $name = $item->item_name 
                            ?? optional($item->item)->name 
                            ?? optional(optional($item->item)->globalItem)->item_name 
                            ?? __('Unknown Item');
                    $itemTotal = $item->price * $item->quantity;
                @endphp
                <tr>
                    <td>{{ $name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($itemTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- SUBTOTAL & DIVIDED TAX SECTION -->
    <div class="border-bottom">
        <table>
            <tr>
                <td>{{ __('Subtotal') }}</td>
                <td class="text-right">₹{{ number_format($order->sub_total, 2) }}</td>
            </tr>

            @if(isset($taxLines) && count($taxLines) > 0)
                @foreach($taxLines as $tax)
                <tr>
                    <td>{{ $tax['name'] }}</td>
                    <td class="text-right">+ ₹{{ $tax['amount'] }}</td>
                </tr>
                @endforeach
            @endif
        </table>
    </div>

    <div class="border-bottom">
        <table>
            <tr>
                <td><strong>{{ __('Grand Total:') }}</strong></td>
                <td class="text-right"><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <p class="text-center" style="margin-top: 12px; margin-bottom: 0;">*** {{ __('Thank You!') }} ***</p>

    <script>
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>