<!-- 1. Tiffin Pre-Booking Modal -->
<div class="modal fade" id="tiffinBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold modal-title fs-6 fs-md-5">
                    <i class="fas fa-calendar-alt text-primary me-2"></i> Book Tiffin Service
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            @if(isset($todayTiffins) && count($todayTiffins) > 0)
                                @foreach($todayTiffins as $tCat)
                                    <option value="{{ $tCat->id }}">{{ $tCat->title }} (₹{{ $tCat->price ?? 100 }}/meal)</option>
                                @endforeach
                            @else
                                <option value="">No active tiffin packages available</option>
                            @endif
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold modal-title"><i class="fas fa-truck text-danger me-2"></i> Delivery Address & Bill Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                        @php
                            $authUser = auth()->user();
                            $profileAddress = $authUser->address ?? '';
                            $profilePincode = $authUser->pincode ?? '';
                            $userAddresses = $savedAddresses ?? ($authUser && $authUser->addresses ? $authUser->addresses : []);
                        @endphp

                        <!-- 1. Saved Address Dropdown -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Select Saved Address / New</label>
                            <select id="savedAddressDropdown" class="form-select rounded-3" onchange="handleSavedAddressChange(this)">
                                @if(!empty($profileAddress))
                                    <option value="profile" data-address="{{ $profileAddress }}" data-pincode="{{ $profilePincode }}" selected>
                                        Profile Address: {{ Str::limit($profileAddress, 40) }} ({{ $profilePincode }})
                                    </option>
                                @endif

                                @if(is_iterable($userAddresses) && count($userAddresses) > 0)
                                    @foreach($userAddresses as $addr)
                                        @php
                                            $addText = $addr->address ?? $addr->address_line ?? '';
                                            $pinText = $addr->pincode ?? '';
                                        @endphp
                                        @if($addText !== $profileAddress)
                                            <option value="{{ $addr->id ?? $loop->index }}" data-address="{{ $addText }}" data-pincode="{{ $pinText }}">
                                                {{ $addr->title ?? 'Saved' }}: {{ Str::limit($addText, 40) }} ({{ $pinText }})
                                            </option>
                                        @endif
                                    @endforeach
                                @endif

                                <option value="new" data-address="" data-pincode="" {{ empty($profileAddress) ? 'selected' : '' }}>
                                    + Enter New Delivery Address
                                </option>
                            </select>
                        </div>

                        <div class="p-3 bg-light border rounded-3 mb-3">
                            <!-- 2. Smart Search Box (Typo Tolerant) -->
                            <div class="mb-3 position-relative">
                                <label class="form-label small fw-bold text-primary">
                                    <i class="fas fa-search-location me-1"></i> Search Area, City/District, or Pincode
                                </label>
                                <input type="text" id="addressSearchInput" class="form-control rounded-3" placeholder="Type Area, City, or Pincode (e.g. Khordha, Shakti Vihar, 752050)..." autocomplete="off">
                                
                                <!-- Search Suggestions Dropdown -->
                                <div id="pincodeSearchResults" class="list-group position-absolute w-100 shadow rounded-3 mt-1" style="max-height: 220px; overflow-y: auto; z-index: 1060; display: none;"></div>
                            </div>

                            <!-- 3. Auto-filled Address Fields (Area, City, State, Pincode) -->
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-1 text-muted">Area / Locality (Auto-filled)</label>
                                    <input type="text" id="deliveryAreaInput" class="form-control rounded-3 bg-white" placeholder="Auto-filled from search" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-1 text-muted">City / District (Auto-filled)</label>
                                    <input type="text" id="deliveryCityInput" class="form-control rounded-3 bg-white" placeholder="Auto-filled from search" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-1 text-muted">State (Auto-filled)</label>
                                    <input type="text" id="deliveryStateInput" class="form-control rounded-3 bg-white" placeholder="Auto-filled from search" value="Odisha" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold mb-1 text-muted">Pincode (Auto-filled)</label>
                                    <input type="text" id="deliveryPincodeInput" class="form-control rounded-3 bg-white" placeholder="Auto-filled from search" maxlength="6" value="{{ $profilePincode }}" readonly>
                                </div>
                            </div>

                            <!-- 4. Manual Street / House Address Field -->
                            <div>
                                <label class="form-label small fw-bold mb-1 text-dark">
                                    <i class="fas fa-home me-1"></i> House No. / Flat / Building / Street Address <span class="text-danger">*</span>
                                </label>
                                <textarea id="deliveryStreetInput" class="form-control rounded-3" rows="2" placeholder="e.g. House No. 9A, Near Krishna Temple, Shakti Vihar">{{ $profileAddress }}</textarea>
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