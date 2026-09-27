<!-- 1. Tiffin Pre-Booking Modal -->
<div class="modal fade" id="tiffinBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold modal-title fs-6 fs-md-5"><i class="fas fa-calendar-alt text-primary me-2"></i> Book Tiffin Service</h5>
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
                        <div class="d-flex flex-wrap gap-2 gap-sm-3 mb-3">
                            <div class="form-check me-2">
                                <input class="form-check-input" type="checkbox" name="meal_types[]" value="breakfast" id="meal_breakfast" checked>
                                <label class="form-check-label small" for="meal_breakfast">Breakfast</label>
                            </div>
                            <div class="form-check me-2">
                                <input class="form-check-input" type="checkbox" name="meal_types[]" value="lunch" id="meal_lunch" checked>
                                <label class="form-check-label small" for="meal_lunch">Lunch</label>
                            </div>
                            <div class="form-check me-2">
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
                                <option value="{{ $tCat->id }}">{{ $tCat->title }} (₹{{ $tCat->price ?? 100 }}/meal)</option>
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

<!-- 2. Delivery Address & Confirmation Modal -->
<div class="modal fade" id="deliveryAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold modal-title"><i class="fas fa-truck text-danger me-2"></i> Delivery Address & Bill Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="deliveryOrderForm">
                    <!-- Order Type Selection -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Order Type</label>
                        <select id="orderTypeSelect" class="form-select rounded-3" onchange="toggleOrderTypeView()">
                            <option value="delivery" selected>Home Delivery</option>
                            <option value="dine_in">Dine-In / Eat at Restaurant</option>
                            <option value="takeaway">Takeaway / Pick Up</option>
                        </select>
                    </div>

                    <!-- Address Section -->
                    <div id="addressGroup">
                        <label class="form-label small fw-bold">Select Delivery Address</label>
                        
                        <!-- Saved Profile Addresses -->
                        <div class="saved-addresses-list mb-3">
                            @if(auth()->check() && auth()->user()->addresses && auth()->user()->addresses->count() > 0)
                                @foreach(auth()->user()->addresses as $index => $addr)
                                    <div class="card mb-2 border rounded-3 p-2 border-opacity-75">
                                        <div class="form-check">
                                            <input class="form-check-input address-radio" type="radio" name="selected_address" id="addr_{{ $addr->id }}" value="{{ $addr->address_line }}" data-pincode="{{ $addr->pincode }}" {{ $index == 0 ? 'checked' : '' }} onchange="toggleAddressInput(false)">
                                            <label class="form-check-label w-100 cursor-pointer ms-1 small" for="addr_{{ $addr->id }}">
                                                <span class="fw-bold text-dark d-block">{{ $addr->title ?? 'Saved Address' }}</span>
                                                <span class="text-muted d-block">{{ $addr->address_line }} - {{ $addr->pincode }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Option to Add New Address -->
                            <div class="card border rounded-3 p-2 border-dashed">
                                <div class="form-check">
                                    <input class="form-check-input address-radio" type="radio" name="selected_address" id="addr_new" value="new" {{ (!auth()->check() || !auth()->user()->addresses || auth()->user()->addresses->count() == 0) ? 'checked' : '' }} onchange="toggleAddressInput(true)">
                                    <label class="form-check-label fw-bold text-primary cursor-pointer ms-1 small" for="addr_new">
                                        <i class="fas fa-plus-circle me-1"></i> + Add New Address
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- New Address Form Input -->
                        <div id="newAddressBox" class="p-2 bg-light rounded-3 mb-3" style="display: {{ (!auth()->check() || !auth()->user()->addresses || auth()->user()->addresses->count() == 0) ? 'block' : 'none' }};">
                            <div class="mb-2">
                                <label class="form-label small fw-bold mb-1">New Delivery Address</label>
                                <textarea id="deliveryAddressInput" class="form-control rounded-3" rows="2" placeholder="Enter house no., landmark, locality..."></textarea>
                            </div>
                            <div>
                                <label class="form-label small fw-bold mb-1">Pincode</label>
                                <input type="text" id="deliveryPincodeInput" class="form-control rounded-3" placeholder="e.g. 324005">
                            </div>
                        </div>
                    </div>

                    <!-- Bill Breakdown Card -->
                    <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                        <h6 class="fw-bold mb-2 text-dark small text-uppercase">Bill Details</h6>
                        <div class="d-flex justify-content-between align-items-center mb-1 small text-muted">
                            <span>Item Subtotal</span>
                            <span class="fw-semibold text-dark" id="modalSubtotal">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1 small text-muted" id="deliveryFeeRow">
                            <span>Delivery Partner Fee</span>
                            <span class="fw-bold text-success" id="modalDeliveryCharge">+ ₹30.00</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center fw-bold text-dark">
                            <span>Total Payable</span>
                            <span class="text-danger fs-6" id="modalTotalPayable">₹0.00</span>
                        </div>
                    </div>

                    <button type="button" class="btn btn-danger w-100 rounded-3 py-2 fw-bold" onclick="processFinalOrder()">
                        Confirm & Place Order (<span id="btnTotalText">₹0.00</span>)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>