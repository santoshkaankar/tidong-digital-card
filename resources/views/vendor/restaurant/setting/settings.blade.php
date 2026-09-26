@extends('layouts.vendor_restaurant')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Restaurant Profile & Settings</h4>
        </div>
        <div class="card-body">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('vendor.restaurant.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- 1. BASIC INFORMATION -->
                <h5 class="text-primary border-bottom pb-2 mb-3">1. Basic Information</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Restaurant Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $restaurant->name) }}" required>
                        <small class="text-muted">Registration name auto-fetched (Editable).</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Contact / Mobile Number *</label>
                        <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $restaurant->mobile) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Food Category *</label>
                        <select name="food_type" class="form-select form-control" required>
                            <option value="pure_veg" {{ old('food_type', $restaurant->food_type) == 'pure_veg' ? 'selected' : '' }}>Pure Veg</option>
                            <option value="non_veg" {{ old('food_type', $restaurant->food_type) == 'non_veg' ? 'selected' : '' }}>Non-Veg</option>
                            <option value="both" {{ old('food_type', $restaurant->food_type) == 'both' ? 'selected' : '' }}>Both (Veg & Non-Veg)</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">Restaurant Photo</label>
                        <input type="file" name="profile_photo" class="form-control" id="photo_input" accept="image/*">
                        @if($restaurant->profile_photo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $restaurant->profile_photo) }}" id="photo_preview" alt="Restaurant Photo" height="80" class="rounded border">
                            </div>
                        @else
                            <div class="mt-2">
                                <img id="photo_preview" src="#" alt="Preview" height="80" class="rounded border d-none">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. LOCATION & ADDRESS AUTO-FILL -->
                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">2. Location & Address Settings</h5>
                
                <div class="row mb-3">
                    <div class="col-md-12 position-relative">
                        <label class="form-label font-weight-bold text-success">Search Location (Type Pincode, Area or City)</label>
                        <input type="text" id="location_search_box" class="form-control form-control-lg border-success" placeholder="Type Pincode, Area or City and select for address" autocomplete="off">
                        <div id="location_suggestions" class="list-group position-absolute w-100 shadow-lg" style="z-index: 1050; display:none;"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Pincode *</label>
                        <input type="text" id="pincode_field" name="pincode" class="form-control" value="{{ old('pincode', $restaurant->pincode) }}" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Area / Location</label>
                        <input type="text" id="area_field" name="area" class="form-control" value="{{ old('area', $restaurant->area) }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">City / District *</label>
                        <input type="text" id="city_field" name="city" class="form-control" value="{{ old('city', $restaurant->city) }}" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">State *</label>
                        <input type="text" id="state_field" name="state" class="form-control" value="{{ old('state', $restaurant->state) }}" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Full Street Address *</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Building No, Street Name, Landmark..." required>{{ old('address', $restaurant->address) }}</textarea>
                    </div>
                </div>

                <!-- 3. GST & BANK DETAILS -->
                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">3. GST & Bank Details</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">GSTIN / GST Number <span class="badge bg-secondary">Optional</span></label>
                        <input type="text" name="gstin" class="form-control" placeholder="e.g. 08ABCDE1234F1ZH" value="{{ old('gstin', $restaurant->gstin) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">GST Certificate Upload</label>
                        <input type="file" name="gst_certificate" class="form-control" accept="image/*,.pdf">
                        @if($restaurant->gst_certificate)
                            <small class="text-success d-block mt-1">
                                <a href="{{ asset('storage/' . $restaurant->gst_certificate) }}" target="_blank">📄 Uploaded GST Certificate Dekhein</a>
                            </small>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">MCA Certificate Upload</label>
                        <input type="file" name="mca_certificate" class="form-control" accept="image/*,.pdf">
                        @if($restaurant->mca_certificate)
                            <small class="text-success d-block mt-1">
                                <a href="{{ asset('storage/' . $restaurant->mca_certificate) }}" target="_blank">📄 Uploaded MCA Certificate Dekhein</a>
                            </small>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Account Holder Name</label>
                        <input type="text" name="account_holder_name" class="form-control" value="{{ old('account_holder_name', $restaurant->account_holder_name) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $restaurant->bank_name) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $restaurant->account_number) }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">IFSC Code</label>
                        <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $restaurant->ifsc_code) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">UPI ID</label>
                        <input type="text" name="upi_id" class="form-control" placeholder="example@upi" value="{{ old('upi_id', $restaurant->upi_id) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment UPI QR Code Image</label>
                        <input type="file" name="upi_qr_code" class="form-control" accept="image/*">
                        @if($restaurant->upi_qr_code)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $restaurant->upi_qr_code) }}" alt="UPI QR" width="100" class="img-thumbnail rounded">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 4. FSSAI & BUSINESS VERIFICATION -->
                <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">4. Business Documents & FSSAI Details</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">FSSAI License Number</label>
                        <input type="text" name="fssai_number" class="form-control" value="{{ old('fssai_number', $restaurant->fssai_number) }}" placeholder="14-digit FSSAI Number">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">FSSAI Certificate Image/PDF</label>
                        <input type="file" name="fssai_certificate" class="form-control" accept="image/*,.pdf">
                        @if($restaurant->fssai_certificate)
                            <small class="text-success d-block mt-1">
                                <a href="{{ asset('storage/' . $restaurant->fssai_certificate) }}" target="_blank">📄 Uploaded FSSAI Certificate Dekhein</a>
                            </small>
                        @endif
                    </div>
                </div>

                <!-- SAVE BUTTON (Inside Form) -->
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-5">Save Settings</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- JQUERY SCRIPT FOR DYNAMIC LOCATION SEARCH & PHOTO PREVIEW -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    // 1. Photo Preview
    $('#photo_input').change(function() {
        let reader = new FileReader();
        reader.onload = (e) => {
            $('#photo_preview').attr('src', e.target.result).removeClass('d-none');
        }
        if (this.files[0]) {
            reader.readAsDataURL(this.files[0]);
        }
    });

    // 2. Dynamic Location Search
    $('#location_search_box').on('keyup', function() {
        let term = $(this).val();
        if (term.length >= 2) {
            $.ajax({
                url: "{{ route('vendor.restaurant.location.search') }}",
                type: "GET",
                data: { term: term },
                success: function(data) {
                    let suggestions = $('#location_suggestions');
                    suggestions.empty().show();

                    if (data.length > 0) {
                        $.each(data, function(index, item) {
                            suggestions.append(`
                                <a href="javascript:void(0)" class="list-group-item list-group-item-action location-item" 
                                   data-pincode="${item.pincode}" 
                                   data-area="${item.office_name}" 
                                   data-city="${item.district}" 
                                   data-state="${item.state_name}">
                                    <strong>${item.office_name}</strong> - ${item.district}, ${item.state_name} <span class="badge bg-primary float-end">${item.pincode}</span>
                                </a>
                            `);
                        });
                    } else {
                        suggestions.append('<div class="list-group-item text-muted">No location found</div>');
                    }
                }
            });
        } else {
            $('#location_suggestions').hide();
        }
    });

    // 3. Selection Auto-fill
    $(document).on('click', '.location-item', function() {
        let pincode = $(this).data('pincode');
        let area = $(this).data('area');
        let city = $(this).data('city');
        let state = $(this).data('state');

        $('#pincode_field').val(pincode);
        $('#area_field').val(area);
        $('#city_field').val(city);
        $('#state_field').val(state);

        $('#location_search_box').val(area + ' - ' + city + ' (' + pincode + ')');
        $('#location_suggestions').hide();
    });

    // Hide dropdown on outside click
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#location_search_box, #location_suggestions').length) {
            $('#location_suggestions').hide();
        }
    });
});
</script>
@endsection