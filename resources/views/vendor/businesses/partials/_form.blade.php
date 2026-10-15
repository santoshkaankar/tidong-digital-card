<div class="card shadow-sm border-0 mb-4 rounded-4">
    <div class="card-header bg-primary text-white py-3 px-4">
        <h5 class="mb-0 text-white fs-6 fs-md-5 fw-bold"><i class="fas fa-plus-circle me-2"></i> Add New Business</h5>
    </div>
    <div class="card-body p-3 p-md-4">
        <form action="{{ route('businesses.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Business Name</label>
                <input type="text" name="business_name" class="form-control form-control-lg fs-6" required placeholder="e.g. Sharma Restaurant">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Business Type</label>
                <select name="business_type" class="form-select form-select-lg fs-6" required>
                    <option value="restaurant">Restaurant</option>
                    <option value="marriage_garden">Marriage Garden</option>
                    <option value="tiffin">Tiffin Service</option>
                    <option value="retail">Retail Store</option>
                    <option value="hotel">Hotel</option>
                    <option value="taxi">Taxi Service</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Phone Number</label>
                <input type="text" name="phone" class="form-control form-control-lg fs-6" placeholder="Mobile Number">
            </div>

            <!-- Smart Pincode / Area Search (Crash-proof AJAX) -->
            <div class="mb-3 position-relative">
    <label class="form-label fw-semibold text-secondary small">Search City, Area or Pincode</label>
    <input type="text" id="pincodeSearchInput" class="form-control form-control-lg fs-6" placeholder="Type City, Area or Pincode (e.g. Kota, Borkheda, 324001)..." autocomplete="off">
    <div id="pincodeResults" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
</div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold text-secondary small">City / District</label>
                    <input type="text" name="city" id="cityInput" class="form-control form-control-lg fs-6" required readonly placeholder="Auto-filled">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold text-secondary small">Pincode</label>
                    <input type="text" name="pincode" id="pincodeInput" class="form-control form-control-lg fs-6" required readonly placeholder="Auto-filled">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">State</label>
                <input type="text" name="state" id="stateInput" class="form-control form-control-lg fs-6" required readonly placeholder="Auto-filled">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Full Address</label>
                <textarea name="address" id="fullAddressInput" class="form-control fs-6" rows="2" required placeholder="Full Address (Shop No / Street)"></textarea>
            </div>

            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">Save Business</button>
        </form>
    </div>
</div>

<!-- AJAX Script for Auto-select -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('pincodeSearchInput');
    const resultsDiv = document.getElementById('pincodeResults');
    const cityInput = document.getElementById('cityInput');
    const pincodeInput = document.getElementById('pincodeInput');
    const stateInput = document.getElementById('stateInput');
    const fullAddressInput = document.getElementById('fullAddressInput');

    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        let query = this.value.trim();

        if (query.length < 2) {
            resultsDiv.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`{{ route('pincode.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            let btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'list-group-item list-group-item-action py-2 fs-6';
                            btn.innerHTML = `<strong>${item.office_name}</strong> (${item.pincode}) - ${item.district}, ${item.state}`;
                            
                            btn.addEventListener('click', function() {
                                cityInput.value = item.district || '';
                                pincodeInput.value = item.pincode || '';
                                stateInput.value = item.state || '';
                                searchInput.value = `${item.office_name} - ${item.pincode}`;
                                fullAddressInput.value = `${item.office_name}, `;
                                resultsDiv.innerHTML = '';
                            });

                            resultsDiv.appendChild(btn);
                        });
                    } else {
                        resultsDiv.innerHTML = '<div class="list-group-item text-muted py-2">No location found</div>';
                    }
                });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.innerHTML = '';
        }
    });
});
</script>