@extends('member.partials.layout')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .restaurant-header { background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%); border: 1px solid #e2e8f0; border-radius: 16px; }
    .menu-item-card { border: 1px solid #e2e8f0; border-radius: 12px; transition: all 0.2s ease; background: #ffffff; }
    .menu-item-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .badge-veg { border: 1px solid #16a34a; color: #16a34a; font-size: 0.75rem; }
    .badge-nonveg { border: 1px solid #dc2626; color: #dc2626; font-size: 0.75rem; }
    .badge-thali { background: #fef3c7; color: #d97706; font-weight: bold; font-size: 0.75rem; border: 1px solid #f59e0b; }
    .badge-tiffin { background: #e0e7ff; color: #4338ca; font-weight: bold; font-size: 0.75rem; border: 1px solid #6366f1; }
    .floating-order-bar { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 600px; background: #ffffff; border-radius: 50px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); z-index: 1050; padding: 12px 24px; }
</style>

<div class="container-fluid py-4 px-4 pb-5">

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ route('member.restaurant.index') }}" class="btn btn-sm btn-light border text-muted rounded-3">
            <i class="fas fa-arrow-left me-1"></i> Back to Restaurants
        </a>
        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#tiffinBookingModal">
            <i class="fas fa-calendar-alt me-1"></i> Pre-Book Tiffin (1D / 1W / 1M)
        </button>
    </div>

    <!-- Restaurant Info Header -->
    <div class="restaurant-header p-4 mb-4 shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 60px; height: 60px;">
                        {{ strtoupper(substr($restaurant->name ?? 'R', 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="fw-bold text-dark mb-1">{{ $restaurant->name ?? 'Restaurant Name' }}</h3>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            {{ $restaurant->city ?? 'Kota' }}, {{ $restaurant->state ?? 'Rajasthan' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                    <i class="fas fa-check-circle me-1"></i> Accepting Orders
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row">
        <!-- Categories Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border border-light-subtle rounded-3 p-3 shadow-sm bg-white">
                <h6 class="fw-bold text-dark mb-3">Categories</h6>
                <div class="nav flex-column nav-pills gap-1" id="v-pills-tab">
                    <button class="nav-link active text-start rounded-3 py-2" data-bs-toggle="pill" data-bs-target="#cat-all">
                        <i class="fas fa-utensils me-2"></i> All Items & Thalis
                    </button>
                    <button class="nav-link text-start rounded-3 py-2" data-bs-toggle="pill" data-bs-target="#cat-tiffin">
                        <i class="fas fa-box me-2"></i> Today's Tiffin ({{ $todayDay }})
                    </button>
                    @foreach($categories as $category)
                        <button class="nav-link text-start rounded-3 py-2" data-bs-toggle="pill" data-bs-target="#cat-{{ $category->id }}">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Menu Items List -->
        <div class="col-lg-9">
            <div class="card border border-light-subtle rounded-3 p-3 shadow-sm bg-white">
                
                <div class="tab-content">
                    
                    <!-- TAB 1: ALL ITEMS + THALI (CUSTOM ITEMS) -->
                    <div class="tab-pane fade show active" id="cat-all">
                        <h5 class="fw-bold text-dark mb-3">Menu & Special Thalis</h5>
                        <div class="row g-3">
                            
                            <!-- 1. Custom Items / Thalis -->
                            @foreach($customItems as $cItem)
                                <div class="col-md-6">
                                    <div class="menu-item-card p-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge badge-thali rounded-1 px-2 py-0.5"><i class="fas fa-concierge-bell me-1"></i> THALI / SPECIAL</span>
                                                <h6 class="fw-bold text-dark mb-0">{{ $cItem->name }}</h6>
                                            </div>
                                            <p class="text-muted small mb-2 text-truncate" style="max-width: 200px;">
                                                {{ $cItem->description ?? 'Special Restaurant Dish / Thali' }}
                                            </p>
                                            <span class="fw-bold text-dark">₹{{ number_format($cItem->price ?? 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="input-group input-group-sm rounded-pill border overflow-hidden" style="width: 100px;">
                                                <button class="btn btn-light text-danger fw-bold px-2 py-1" onclick="updateQty('custom_{{ $cItem->id }}', {{$cItem->price }}, -1)">-</button>
                                                <input type="text" id="qty-custom_{{ $cItem->id }}" class="form-control text-center border-0 fw-bold px-0 bg-white" value="0" readonly>
                                                <button class="btn btn-light text-success fw-bold px-2 py-1" onclick="updateQty('custom_{{ $cItem->id }}', {{$cItem->price }}, 1)">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- 2. Global Items -->
                            @foreach($globalItems as $gItem)
                                @php
                                    $itemName = $gItem->globalItem->item_name ?? $gItem->name ?? 'Food Item';
                                @endphp
                                <div class="col-md-6">
                                    <div class="menu-item-card p-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge badge-veg rounded-1 px-1.5 py-0.5"><i class="fas fa-circle" style="font-size: 8px;"></i> VEG</span>
                                                <h6 class="fw-bold text-dark mb-0">{{ $itemName }}</h6>
                                            </div>
                                            <p class="text-muted small mb-2 text-truncate" style="max-width: 200px;">Freshly prepared food item.</p>
                                            <span class="fw-bold text-dark">₹{{ number_format($gItem->price ?? 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="input-group input-group-sm rounded-pill border overflow-hidden" style="width: 100px;">
                                                <button class="btn btn-light text-danger fw-bold px-2 py-1" onclick="updateQty('global_{{ $gItem->id }}', {{$gItem->price }}, -1)">-</button>
                                                <input type="text" id="qty-global_{{ $gItem->id }}" class="form-control text-center border-0 fw-bold px-0 bg-white" value="0" readonly>
                                                <button class="btn btn-light text-success fw-bold px-2 py-1" onclick="updateQty('global_{{ $gItem->id }}', {{$gItem->price }}, 1)">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <!-- TAB 2: SAME-DAY TIFFIN AUTO RENDER -->
                    <div class="tab-pane fade" id="cat-tiffin">
                        <h5 class="fw-bold text-dark mb-3">Today's Tiffin Menu ({{ $todayDay }})</h5>
                        <div class="row g-3">
                            @forelse($todayTiffins as $tiffin)
                                @foreach($tiffin->items as $tItem)
                                    <div class="col-md-6">
                                        <div class="menu-item-card p-3 d-flex justify-content-between align-items-center border-primary-subtle">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge badge-tiffin rounded-1 px-2 py-0.5"><i class="fas fa-box me-1"></i> {{ strtoupper($tItem->meal_type) }}</span>
                                                    <h6 class="fw-bold text-dark mb-0">{{ $tiffin->title ?? 'Daily Tiffin' }}</h6>
                                                </div>
                                                <p class="text-muted small mb-1">{{ $tItem->item_name ?? 'Full Meals' }}</p>
                                                <span class="fw-bold text-success">₹{{ number_format($tItem->price ?? 100, 2) }}</span>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#tiffinBookingModal">Book Now</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @empty
                                <div class="col-12 py-5 text-center text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                                    <p class="mb-0">No tiffin schedule configured for today ({{ $todayDay }}).</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- TAB 3: DYNAMIC CATEGORIES -->
                    @foreach($categories as $category)
                        <div class="tab-pane fade" id="cat-{{ $category->id }}">
                            <h5 class="fw-bold text-dark mb-3">{{ $category->name }}</h5>
                            <div class="row g-3">
                                @php
                                    $catLower = strtolower(trim($category->name));
                                @endphp

                                @if(str_contains($catLower, 'thali'))
                                    {{-- Render Thalis (Custom Items) --}}
                                    @forelse($customItems as $cItem)
                                        <div class="col-md-6">
                                            <div class="menu-item-card p-3 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="badge badge-thali rounded-1 px-2 py-0.5"><i class="fas fa-concierge-bell me-1"></i> THALI / SPECIAL</span>
                                                        <h6 class="fw-bold text-dark mb-0">{{ $cItem->name }}</h6>
                                                    </div>
                                                    <p class="text-muted small mb-2 text-truncate" style="max-width: 200px;">
                                                        {{ $cItem->description ?? 'Special Restaurant Dish / Thali' }}
                                                    </p>
                                                    <span class="fw-bold text-dark">₹{{ number_format($cItem->price ?? 0, 2) }}</span>
                                                </div>
                                                <div>
                                                    <div class="input-group input-group-sm rounded-pill border overflow-hidden" style="width: 100px;">
                                                        <button class="btn btn-light text-danger fw-bold px-2 py-1" onclick="updateQty('custom_{{ $cItem->id }}', {{$cItem->price }}, -1)">-</button>
                                                        <input type="text" id="qty-custom_{{ $cItem->id }}" class="form-control text-center border-0 fw-bold px-0 bg-white" value="0" readonly>
                                                        <button class="btn btn-light text-success fw-bold px-2 py-1" onclick="updateQty('custom_{{ $cItem->id }}', {{$cItem->price }}, 1)">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 py-5 text-center text-muted">
                                            <i class="fas fa-concierge-bell fa-2x mb-2 opacity-50"></i>
                                            <p class="mb-0">Is category me abhi koi Thali available nahi hai.</p>
                                        </div>
                                    @endforelse

                                @elseif(str_contains($catLower, 'tiffin'))
                                    {{-- Render Tiffins --}}
                                    @forelse($todayTiffins as $tiffin)
                                        @foreach($tiffin->items as $tItem)
                                            <div class="col-md-6">
                                                <div class="menu-item-card p-3 d-flex justify-content-between align-items-center border-primary-subtle">
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 mb-1">
                                                            <span class="badge badge-tiffin rounded-1 px-2 py-0.5"><i class="fas fa-box me-1"></i> {{ strtoupper($tItem->meal_type) }}</span>
                                                            <h6 class="fw-bold text-dark mb-0">{{ $tiffin->title ?? 'Daily Tiffin' }}</h6>
                                                        </div>
                                                        <p class="text-muted small mb-1">{{ $tItem->item_name ?? 'Full Meals' }}</p>
                                                        <span class="fw-bold text-success">₹{{ number_format($tItem->price ?? 100, 2) }}</span>
                                                    </div>
                                                    <div>
                                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#tiffinBookingModal">Book Now</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @empty
                                        <div class="col-12 py-5 text-center text-muted">
                                            <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                                            <p class="mb-0">No tiffin schedule configured for today ({{ $todayDay }}).</p>
                                        </div>
                                    @endforelse

                                @else
                                    {{-- Standard Global Items --}}
                                    @php
                                        $catItems =$globalItems->filter(function($item) use ($category) {
                                            return ($item->category_id ==$category->id) || ($item->restaurant_category_id ==$category->id);
                                        });
                                    @endphp

                                    @forelse($catItems as $gItem)
                                        @php
                                            $itemName = $gItem->globalItem->item_name ?? $gItem->name ?? 'Food Item';
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="menu-item-card p-3 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="badge badge-veg rounded-1 px-1.5 py-0.5"><i class="fas fa-circle" style="font-size: 8px;"></i> VEG</span>
                                                        <h6 class="fw-bold text-dark mb-0">{{ $itemName }}</h6>
                                                    </div>
                                                    <p class="text-muted small mb-2 text-truncate" style="max-width: 200px;">Freshly prepared food item.</p>
                                                    <span class="fw-bold text-dark">₹{{ number_format($gItem->price ?? 0, 2) }}</span>
                                                </div>
                                                <div>
                                                    <div class="input-group input-group-sm rounded-pill border overflow-hidden" style="width: 100px;">
                                                        <button class="btn btn-light text-danger fw-bold px-2 py-1" onclick="updateQty('global_{{ $gItem->id }}', {{$gItem->price }}, -1)">-</button>
                                                        <input type="text" id="qty-global_{{ $gItem->id }}" class="form-control text-center border-0 fw-bold px-0 bg-white" value="0" readonly>
                                                        <button class="btn btn-light text-success fw-bold px-2 py-1" onclick="updateQty('global_{{ $gItem->id }}', {{$gItem->price }}, 1)">+</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 py-5 text-center text-muted">
                                            <i class="fas fa-utensils fa-2x mb-2 opacity-50"></i>
                                            <p class="mb-0">Is category me abhi koi item available nahi hai.</p>
                                        </div>
                                    @endforelse
                                @endif
                            </div>
                        </div>
                    @endforeach

                </div>

            </div>
        </div>
    </div>

</div>

<!-- Floating Bottom Order Bar -->
<div id="floatingOrderBar" class="floating-order-bar d-none">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-bold text-muted small d-block" id="selectedItemsCount">0 Items Selected</span>
            <span class="fw-bold text-success fs-5" id="selectedTotalAmount">₹0.00</span>
        </div>
        <button type="button" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm" onclick="submitLiveOrder()">
            Send Order <i class="fas fa-arrow-right ms-1"></i>
        </button>
    </div>
</div>

<!-- Modal: Tiffin Pre-Booking (1D, 1W, 1M, Custom Date) -->
<div class="modal fade" id="tiffinBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold modal-title"><i class="fas fa-calendar-alt text-primary me-2"></i> Book Tiffin Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="tiffinBookingForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Duration</label>
                        <select id="tiffinDuration" class="form-select rounded-3" onchange="toggleCustomDates(this.value)">
                            <option value="1d">1 Day (1D)</option>
                            <option value="1w">1 Week (1W)</option>
                            <option value="1m">1 Month (1M)</option>
                            <option value="custom">Custom Date Range</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Start Date</label>
                            <input type="date" id="tiffinFromDate" class="form-control rounded-3" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6" id="toDateContainer" style="display: none;">
                            <label class="form-label small fw-bold">End Date</label>
                            <input type="date" id="tiffinToDate" class="form-control rounded-3" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Select Meal Types</label>
                        <div class="d-flex gap-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="meal_types[]" value="breakfast" id="meal_breakfast" checked>
                                <label class="form-check-label small" for="meal_breakfast">Breakfast</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="meal_types[]" value="lunch" id="meal_lunch" checked>
                                <label class="form-check-label small" for="meal_lunch">Lunch</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="meal_types[]" value="snacks" id="meal_snacks" checked>
                                <label class="form-check-label small" for="meal_snacks">Snacks</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="meal_types[]" value="dinner" id="meal_dinner" checked>
                                <label class="form-check-label small" for="meal_dinner">Dinner</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Tiffin Package</label>
                        <select id="tiffinCatalogId" class="form-select rounded-3">
                            @foreach($todayTiffins as $tCat)
                                <option value="{{ $tCat->id }}">{{ $tCat->title }} (₹{{$tCat->price ?? 100 }}/meal)</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" class="btn btn-primary w-100 rounded-3 py-2 fw-bold" onclick="submitTiffinBooking()">
                        Confirm Tiffin Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let cartItems = {};

    function updateQty(itemId, price, change) {
        if (!cartItems[itemId]) {
            cartItems[itemId] = { id: itemId, price: price, quantity: 0 };
        }
        cartItems[itemId].quantity += change;

        if (cartItems[itemId].quantity <= 0) {
            delete cartItems[itemId];
            document.getElementById(`qty-${itemId}`).value = 0;
        } else {
            document.getElementById(`qty-${itemId}`).value = cartItems[itemId].quantity;
        }
        renderFloatingBar();
    }

    function renderFloatingBar() {
        let totalCount = 0;
        let totalAmount = 0;
        Object.values(cartItems).forEach(item => {
            totalCount += item.quantity;
            totalAmount += (item.price * item.quantity);
        });

        const bar = document.getElementById('floatingOrderBar');
        if (totalCount > 0) {
            document.getElementById('selectedItemsCount').innerText = `${totalCount} Items Selected`;
            document.getElementById('selectedTotalAmount').innerText = `₹${totalAmount.toFixed(2)}`;
            bar.classList.remove('d-none');
        } else {
            bar.classList.add('d-none');
        }
    }

    function toggleCustomDates(val) {
        document.getElementById('toDateContainer').style.display = (val === 'custom') ? 'block' : 'none';
    }

    function submitTiffinBooking() {
        let duration = document.getElementById('tiffinDuration').value;
        let fromDate = document.getElementById('tiffinFromDate').value;
        let toDate = document.getElementById('tiffinToDate').value;
        let catalogId = document.getElementById('tiffinCatalogId').value;

        let meals = [];
        document.querySelectorAll('input[name="meal_types[]"]:checked').forEach(cb => {
            meals.push(cb.value);
        });

        if (!catalogId) {
            alert("Kripya Tiffin Package choose karein!");
            return;
        }

        fetch("{{ route('hub.restaurant.bookTiffin', $restaurant->id) }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                duration: duration,
                from_date: fromDate,
                to_date: toDate,
                meal_types: meals,
                catalog_id: catalogId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        });
    }
</script>
@endpush