<div class="card border border-light-subtle rounded-3 p-3 shadow-sm bg-white">
    <div class="tab-content">
        
        <!-- TAB 1: ALL ITEMS -->
        <div class="tab-pane fade show active" id="cat-all">
            <h5 class="fw-bold text-dark mb-3 fs-6 fs-md-5">Menu & Special Thalis</h5>
            <div class="row g-3">
                <!-- Custom Items / Thalis -->
                @foreach($customItems as $cItem)
                    <div class="col-12 col-md-6">
                        <div class="menu-item-card p-3 d-flex justify-content-between align-items-center gap-2">
                            <div class="item-details-container flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="badge badge-thali rounded-1 px-2 py-0.5"><i class="fas fa-concierge-bell me-1"></i> THALI / SPECIAL</span>
                                    <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $cItem->name }}</h6>
                                </div>
                                <p class="text-muted small mb-2 text-truncate" style="max-width: 220px;">{{ $cItem->description ?? 'Special Restaurant Dish / Thali' }}</p>
                                <span class="fw-bold text-dark">₹{{ number_format($cItem->price ?? 0, 2) }}</span>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="input-group input-group-sm rounded-pill border overflow-hidden" style="width: 95px;">
                                    <button class="btn btn-light text-danger fw-bold px-2 py-1" onclick="updateQty('custom_{{ $cItem->id }}', {{ $cItem->price }}, -1, '{{ addslashes($cItem->name) }}')">-</button>
                                    <input type="text" id="qty-custom_{{ $cItem->id }}" class="form-control text-center border-0 fw-bold px-0 bg-white" value="0" readonly>
                                    <button class="btn btn-light text-success fw-bold px-2 py-1" onclick="updateQty('custom_{{ $cItem->id }}', {{ $cItem->price }}, 1, '{{ addslashes($cItem->name) }}')">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Global Food Items -->
                @foreach($globalItems as $gItem)
                    @php $itemName = $gItem->globalItem->item_name ?? $gItem->name ?? 'Food Item'; @endphp
                    <div class="col-12 col-md-6">
                        <div class="menu-item-card p-3 d-flex justify-content-between align-items-center gap-2">
                            <div class="item-details-container flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="badge badge-veg rounded-1 px-1.5 py-0.5"><i class="fas fa-circle" style="font-size: 8px;"></i> VEG</span>
                                    <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $itemName }}</h6>
                                </div>
                                <p class="text-muted small mb-2 text-truncate" style="max-width: 220px;">Freshly prepared food item.</p>
                                <span class="fw-bold text-dark">₹{{ number_format($gItem->price ?? 0, 2) }}</span>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="input-group input-group-sm rounded-pill border overflow-hidden" style="width: 95px;">
                                    <button class="btn btn-light text-danger fw-bold px-2 py-1" onclick="updateQty('global_{{ $gItem->id }}', {{ $gItem->price }}, -1, '{{ addslashes($itemName) }}')">-</button>
                                    <input type="text" id="qty-global_{{ $gItem->id }}" class="form-control text-center border-0 fw-bold px-0 bg-white" value="0" readonly>
                                    <button class="btn btn-light text-success fw-bold px-2 py-1" onclick="updateQty('global_{{ $gItem->id }}', {{ $gItem->price }}, 1, '{{ addslashes($itemName) }}')">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- TAB 2: DYNAMIC CATEGORIES -->
        @foreach($categories as $category)
            <div class="tab-pane fade" id="cat-{{ $category->id }}">
                <h5 class="fw-bold text-dark mb-3 fs-6 fs-md-5">{{ $category->name }}</h5>
                <div class="row g-3">
                    @php
                        $catItems = $globalItems->filter(function($item) use ($category) {
                            return ($item->category_id == $category->id) || ($item->restaurant_category_id == $category->id);
                        });
                    @endphp

                    @forelse($catItems as $gItem)
                        @php $itemName = $gItem->globalItem->item_name ?? $gItem->name ?? 'Food Item'; @endphp
                        <div class="col-12 col-md-6">
                            <div class="menu-item-card p-3 d-flex justify-content-between align-items-center gap-2">
                                <div class="item-details-container flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge badge-veg rounded-1 px-1.5 py-0.5"><i class="fas fa-circle" style="font-size: 8px;"></i> VEG</span>
                                        <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $itemName }}</h6>
                                    </div>
                                    <span class="fw-bold text-dark">₹{{ number_format($gItem->price ?? 0, 2) }}</span>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="input-group input-group-sm rounded-pill border overflow-hidden" style="width: 95px;">
                                        <button class="btn btn-light text-danger fw-bold px-2 py-1" onclick="updateQty('global_{{ $gItem->id }}', {{ $gItem->price }}, -1, '{{ addslashes($itemName) }}')">-</button>
                                        <input type="text" id="qty-global_{{ $gItem->id }}" class="form-control text-center border-0 fw-bold px-0 bg-white" value="0" readonly>
                                        <button class="btn btn-light text-success fw-bold px-2 py-1" onclick="updateQty('global_{{ $gItem->id }}', {{ $gItem->price }}, 1, '{{ addslashes($itemName) }}')">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 py-5 text-center text-muted">
                            <i class="fas fa-utensils fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Is category me abhi koi items nahi hain.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach

        <!-- TAB 3: TODAY'S TIFFIN -->
        <div class="tab-pane fade" id="cat-tiffin">
            <h5 class="fw-bold text-dark mb-3 fs-6 fs-md-5">Today's Tiffin Schedule ({{ $todayDay }})</h5>
            <div class="row g-3">
                @forelse($todayTiffins as $tiffin)
                    @foreach($tiffin->items as $tItem)
                        <div class="col-12 col-md-6">
                            <div class="menu-item-card p-3 d-flex justify-content-between align-items-center border-primary-subtle gap-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge badge-tiffin rounded-1 px-2 py-0.5"><i class="fas fa-box me-1"></i> {{ strtoupper($tItem->meal_type) }}</span>
                                        <h6 class="fw-bold text-dark mb-0">{{ $tiffin->title ?? 'Daily Tiffin' }}</h6>
                                    </div>
                                    <p class="text-muted small mb-1">{{ $tItem->item_name ?? 'Full Meals' }}</p>
                                    <span class="fw-bold text-success">₹{{ number_format($tItem->price ?? 100, 2) }}</span>
                                </div>
                                <div class="flex-shrink-0">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#tiffinBookingModal">Book Now</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="col-12 py-5 text-center text-muted">
                        <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">Aaj ({{ $todayDay }}) ke liye koi tiffin schedule active nahi hai.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>