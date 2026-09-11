<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Counter Billing — Restaurant Hub</title>
    
    <!-- Google Fonts & Bootstrap 5 -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #4f46e5;
            --bg-canvas: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-main);
            overflow-x: hidden;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .main-viewport {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .content-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .item-card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            transition: all 0.2s ease;
            background: #fff;
        }

        .item-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        }

        .filter-btn.active {
            background-color: var(--primary-color) !important;
            color: #fff !important;
            border-color: var(--primary-color) !important;
        }
    </style>
    <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill">POINT OF SALE</span>
        <h2 class="fw-bold mt-2 mb-0">POS / Counter Billing</h2>
        <p class="text-muted small mb-0">Create quick bills, print KOT, and manage table orders.</p>
    </div>
    
    <!-- Direct View Orders Button -->
    <a href="{{ route('vendor.restaurant.orders.index') }}" class="btn btn-outline-primary fw-semibold">
        <i class="bi bi-list-task me-1"></i> View All Orders
    </a>
</div>
</head>
<body>

<div class="dashboard-wrapper">
    @include('vendor.restaurant.partials.sidebar')

    <div class="main-viewport p-4 p-lg-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                    POINT OF SALE
                </span>
                <h2 class="fw-bold mt-2 mb-0">POS / Counter Billing</h2>
                <p class="text-muted small mb-0">Create quick bills, print KOT, and manage table orders.</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Items Menu & Filters -->
            <div class="col-lg-7">
                <div class="content-card p-3 mb-4">
                    <div class="d-flex flex-wrap gap-2" id="category-filters">
                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 filter-btn active" data-category="all">All Items</button>
                        @foreach($categories as $category)
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 filter-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="row g-3" id="items-grid">
                    @forelse($items as $item)
                        @php
                            // Fetch exact item_name from global_items table migration relation
                            $itemName = $item->globalItem->item_name 
                                        ?? $item->globalItem->name 
                                        ?? $item->name 
                                        ?? 'Item #' . $item->id;
                        @endphp
                        <div class="col-md-4 col-sm-6 item-card-wrapper" data-category-id="{{ $item->restaurant_category_id }}">
                            <div class="item-card p-3 text-center h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-2" style="min-height: 38px;">{{ $itemName }}</h6>
                                    <p class="text-primary fw-bold fs-6 mb-3">₹{{ number_format($item->price, 2) }}</p>
                                </div>
                                <button class="btn btn-sm btn-outline-primary w-100 rounded-3 fw-semibold add-to-cart" 
                                        data-id="{{ $item->id }}" 
                                        data-name="{{ addslashes($itemName) }}" 
                                        data-price="{{ $item->price }}">
                                    <i class="bi bi-plus-lg me-1"></i> Add
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="bi bi-inbox display-5 d-block mb-2 opacity-25"></i>
                            No food items available in the menu.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Cart & Billing Area -->
            <div class="col-lg-5">
                <div class="content-card p-4 sticky-top" style="top: 20px;">
                    <h5 class="fw-bold mb-3"><i class="bi bi-cart3 text-primary me-2"></i> Current Order</h5>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Order Type</label>
                            <select id="order_type" class="form-select rounded-3">
                                <option value="dine_in">Dine In</option>
                                <option value="takeaway">Takeaway</option>
                                <option value="delivery">Delivery</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="table-wrapper">
                            <label class="form-label small fw-semibold text-muted">Table Number</label>
                            <select id="table_id" class="form-select rounded-3">
                                <option value="">Select Table</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}">{{ $table->table_number }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive my-3" style="min-height: 200px; max-height: 350px; overflow-y: auto;">
                        <table class="table align-middle text-center mb-0" id="cart-table">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-start border-0">Item</th>
                                    <th class="border-0">Price</th>
                                    <th class="border-0" style="width: 80px;">Qty</th>
                                    <th class="border-0">Total</th>
                                    <th class="border-0"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-body">
                                <tr>
                                    <td colspan="5" class="text-muted py-4">No items added to order</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-6 fw-semibold text-muted">Grand Total</span>
                            <span class="fs-4 fw-bold text-success">₹<span id="grand-total">0.00</span></span>
                        </div>

                        <button class="btn btn-primary w-100 py-2.5 fw-bold rounded-3 shadow-sm" id="place-order-btn">
                            <i class="bi bi-printer me-2"></i> Place Order & Print KOT
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('vendor.restaurant.pos.pos_script')

</body>
</html>