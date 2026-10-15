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
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">City</label>
                <input type="text" name="city" class="form-control form-control-lg fs-6" placeholder="City Name">
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Full Address</label>
                <textarea name="address" class="form-control fs-6" rows="2" placeholder="Full Address"></textarea>
            </div>
            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">Save Business</button>
        </form>
    </div>
</div>