<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Receipt') }} - #{{ $order->order_number ?? '000' }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        @page {
            size: 80mm auto;
            margin: 3mm; /* Yahan margin badha diya hai taaki upar jagah rahe */
        }
        body { 
            font-family: 'Courier New', Courier, monospace; 
            width: 72mm; 
            margin: 0 auto; 
            padding: 4mm 2mm; /* Upar-neeche thodi padding aur de di hai */
            font-size: 12px;
            color: #000000;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .border-bottom { 
            border-bottom: 1px dashed #000; 
            margin-bottom: 6px; 
            padding-bottom: 6px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            text-align: left; 
            padding: 3px 0; 
            vertical-align: top;
        }
        @media print {
            body { 
                width: 72mm; 
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>

    <div class="text-center border-bottom">
        <h3 style="margin: 0 0 5px 0; font-size: 15px;">{{ __('KOT / RECEIPT') }}</h3>
        <p style="margin: 3px 0;">{{ __('Order #:') }} <strong>{{ $order->order_number ?? '-' }}</strong></p>
        <p style="margin: 0;">
            {{ __('Type:') }} <strong>{{ isset($order->order_type) ? __(strtoupper(str_replace('_', ' ', $order->order_type))) : '-' }}</strong> | 
            {{ __('Table:') }} <strong>{{ $order->table->table_number ?? __('N/A') }}</strong>
        </p>
        <p style="margin: 3px 0;">{{ __('Date:') }} {{ isset($order->created_at) ? $order->created_at->format('d-m-Y h:i A') : date('d-m-Y h:i A') }}</p>
    </div>

    <table class="border-bottom">
        <thead>
            <tr>
                <th>{{ __('Item') }}</th>
                <th class="text-center" style="width: 35px;">{{ __('Qty') }}</th>
                <th class="text-right" style="width: 65px;">{{ __('Price') }}</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($order->items) && count($order->items) > 0)
                @foreach($order->items as $item)
                    @php
                        $name = $item->item_name 
                            ?? optional($item->item)->name 
                            ?? optional($item->globalItem)->item_name
                            ?? optional(optional($item->item)->globalItem)->item_name
                            ?? optional(optional($item)->customItem)->name
                            ?? __('Food Item');
                            
                        $price = $item->price ?? 0;
                        $qty = $item->quantity ?? 1;
                        $itemTotal = $price * $qty;
                    @endphp
                    <tr>
                        <td>{{ $name }}</td>
                        <td class="text-center">{{ $qty }}</td>
                        <td class="text-right">₹{{ number_format($itemTotal, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center">{{ __('No items found') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="border-bottom">
        <table>
            <tr>
                <td>{{ __('Subtotal') }}</td>
                <td class="text-right">₹{{ number_format($order->sub_total ?? 0, 2) }}</td>
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
                <td class="text-right"><strong>₹{{ number_format($order->total_amount ?? 0, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <p class="text-center" style="margin-top: 8px; margin-bottom: 0;">*** {{ __('Thank You!') }} ***</p>

    <script>
        window.onload = function() {
            window.print();
        };

        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>