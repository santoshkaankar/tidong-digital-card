@extends('layouts.vendor_restaurant')

@push('styles')
<style>
    .content-card {
        background: var(--card-bg, #ffffff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .item-card {
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 12px;
        transition: all 0.2s ease;
        background: #fff;
    }
    .item-card:hover {
        border-color: #4f46e5;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
    }
    .filter-btn.active {
        background-color: #4f46e5 !important;
        color: #fff !important;
        border-color: #4f46e5 !important;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">
            POINT OF SALE
        </span>
        <h2 class="fw-bold mt-2 mb-0">POS / Counter Billing</h2>
        <p class="text-muted small mb-0">Create quick bills, print KOT, and manage table orders.</p>
    </div>
    <a href="{{ route('vendor.restaurant.orders.index') }}" class="btn btn-outline-primary fw-semibold">
        <i class="bi bi-list-task me-1"></i> View All Orders
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Items Menu & Filters -->
    <div class="col-lg-7">
        <div class="content-card p-3 mb-4">
            <div class="d-flex flex-wrap gap-2" id="category-filters">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 filter-btn active" data-category="all">All Items</button>
                @foreach($categories as $category)
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 filter-btn" data-category="{{ $category->id }}">{{ $category->name }}</button>
                @endforeach
                @if(isset($customItems) && count($customItems) > 0)
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 filter-btn" data-category="custom">Custom Items</button>
                @endif
            </div>
        </div>

        <div class="row g-3" id="items-grid">
            @php $hasAnyItem = false; @endphp

            <!-- Regular Inventory Items -->
            @if(isset($items))
                @foreach($items as $item)
                    @php
                        $hasAnyItem = true;
                        $itemName = $item->globalItem->item_name ?? $item->globalItem->name ?? $item->name ?? 'Item #' . $item->id;
                    @endphp
                    <div class="col-md-4 col-sm-6 item-card-wrapper" data-category-id="{{ $item->restaurant_category_id }}">
                        <div class="item-card p-3 text-center h-100 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="fw-bold text-dark mb-2" style="min-height: 38px;">{{ $itemName }}</h6>
                                <p class="text-primary fw-bold fs-6 mb-3">₹{{ number_format($item->price, 2) }}</p>
                            </div>
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary w-100 rounded-3 fw-semibold add-to-cart-btn" 
                                    data-id="{{ $item->id }}" 
                                    data-is-custom="0"
                                    data-name="{{ addslashes($itemName) }}" 
                                    data-price="{{ $item->price }}">
                                <i class="bi bi-plus-lg me-1"></i> Add
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Custom Items -->
            @if(isset($customItems))
                @foreach($customItems as $custom)
                    @php
                        $hasAnyItem = true;
                        $customName = $custom->name ?? $custom->item_name ?? 'Custom Item';
                        $customPrice = $custom->price ?? 0;
                    @endphp
                    <div class="col-md-4 col-sm-6 item-card-wrapper" data-category-id="custom">
                        <div class="item-card p-3 text-center h-100 d-flex flex-column justify-content-between border-warning">
                            <div>
                                <div class="mb-1"><span class="badge bg-warning text-dark" style="font-size: 0.6rem;">Custom</span></div>
                                <h6 class="fw-bold text-dark mb-2" style="min-height: 38px;">{{ $customName }}</h6>
                                <p class="text-success fw-bold fs-6 mb-3">₹{{ number_format($customPrice, 2) }}</p>
                            </div>
                            <button type="button"
                                    class="btn btn-sm btn-outline-warning text-dark w-100 rounded-3 fw-semibold add-to-cart-btn" 
                                    data-id="{{ $custom->id }}" 
                                    data-is-custom="1"
                                    data-name="{{ addslashes($customName) }}" 
                                    data-price="{{ $customPrice }}">
                                <i class="bi bi-plus-lg me-1"></i> Add
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif

            @if(!$hasAnyItem)
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-inbox display-5 d-block mb-2 opacity-25"></i>
                    No food items available in the menu.
                </div>
            @endif
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
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted">Customer Name</label>
                    <input type="text" id="customer_name" class="form-control rounded-3" placeholder="Optional">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-muted">Mobile Number</label>
                    <input type="tel" id="customer_phone" class="form-control rounded-3" placeholder="10 Digit Number">
                </div>
            </div>

            <div class="table-responsive my-3" style="min-height: 180px; max-height: 320px; overflow-y: auto;">
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
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Externalized Script File Inclusion -->
@include('vendor.restaurant.pos.pos_script')
@endpush