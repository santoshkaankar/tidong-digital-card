<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $table->table_number }} - Digital Menu</title>
    
    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding-bottom: 90px; }
        .header-card { background: linear-gradient(135deg, #ff416c, #ff4b2b); color: white; border-radius: 0 0 20px 20px; padding: 20px 15px; }
        .category-scroll { overflow-x: auto; white-space: nowrap; padding: 10px 0; scrollbar-width: none; }
        .category-scroll::-webkit-scrollbar { display: none; }
        .category-badge { display: inline-block; padding: 8px 16px; margin-right: 8px; border-radius: 20px; background: #fff; color: #333; font-weight: 600; border: 1px solid #ddd; text-decoration: none; cursor: pointer; }
        .category-badge.active { background: #ff4b2b; color: #fff; border-color: #ff4b2b; }
        .food-card { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 12px; }
        .food-type-icon { width: 14px; height: 14px; border-radius: 3px; display: inline-block; border: 1px solid; margin-right: 5px; }
        .type-veg { border-color: green; } .type-veg::after { content: ''; width: 6px; height: 6px; background: green; border-radius: 50%; display: block; margin: 3px auto; }
        .type-nonveg { border-color: red; } .type-nonveg::after { content: ''; width: 6px; height: 6px; background: red; border-radius: 50%; display: block; margin: 3px auto; }
        .qty-btn-group { border: 1px solid #ff4b2b; border-radius: 6px; overflow: hidden; display: flex; align-items: center; }
        .qty-btn-group button { background: #fff; border: none; color: #ff4b2b; width: 28px; height: 28px; font-weight: bold; }
        .qty-btn-group span { width: 25px; text-align: center; font-weight: 600; font-size: 14px; }
        .sticky-cart-bar { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #eee; padding: 12px 15px; box-shadow: 0 -4px 12px rgba(0,0,0,0.1); z-index: 1000; }
        .status-badge { font-size: 12px; padding: 4px 10px; border-radius: 12px; font-weight: 600; }
    </style>
</head>
<body>

    @include('customer.restaurant.partials.header')
    @include('customer.restaurant.partials.active_order')
    @include('customer.restaurant.partials.category_nav')
    @include('customer.restaurant.partials.menu_items')
    @include('customer.restaurant.partials.cart_bar')
    @include('customer.restaurant.partials.modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @include('customer.restaurant.scripts.menu_script')
</body>
</html>