@extends('vendor.card.layout')

@section('title', 'Configure Digital Card')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Form Section -->
        <div class="col-lg-7">
            <!-- Tabs Navigation -->
            <ul class="nav nav-pills mb-3" id="cardTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold" id="master-tab" data-bs-toggle="tab" data-bs-target="#master-pane">1. Master Profile Config</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="design-tab" data-bs-toggle="tab" data-bs-target="#design-pane">2. Card View Designer</button>
                </li>
            </ul>

            <div class="tab-content" id="cardTabContent">
                <!-- TAB 1: Master Profile Form -->
                <div class="tab-pane fade show active bg-white p-4 rounded shadow-sm" id="master-pane">
                    <h5 class="mb-3 text-primary"><i class="fa-solid fa-address-card me-2"></i>Master Business Profile</h5>
                    <form action="{{ route('vendor.card.master.save') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $masterCard->name ?? '' }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nickname</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" name="show_nickname" class="form-check-input mt-0" {{ ($masterCard->show_nickname ?? 1) ? 'checked' : '' }}>
                                    </div>
                                    <input type="text" name="nickname" class="form-control" value="{{ $masterCard->nickname ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Business Name</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" name="show_business_name" class="form-check-input mt-0" {{ ($masterCard->show_business_name ?? 1) ? 'checked' : '' }}>
                                    </div>
                                    <input type="text" name="business_name" class="form-control" value="{{ $masterCard->business_name ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Designation</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" name="show_designation" class="form-check-input mt-0" {{ ($masterCard->show_designation ?? 1) ? 'checked' : '' }}>
                                    </div>
                                    <input type="text" name="designation" class="form-control" value="{{ $masterCard->designation ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Tagline / Motto</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" name="show_tagline" class="form-check-input mt-0" {{ ($masterCard->show_tagline ?? 1) ? 'checked' : '' }}>
                                    </div>
                                    <input type="text" name="tagline" class="form-control" value="{{ $masterCard->tagline ?? '' }}">
                                </div>
                            </div>

                            <!-- Phone / Whatsapp -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Primary Phone</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" name="show_phone" class="form-check-input mt-0" {{ ($masterCard->show_phone ?? 1) ? 'checked' : '' }}>
                                    </div>
                                    <input type="text" name="phone" class="form-control" value="{{ $masterCard->phone ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">WhatsApp Number</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" name="show_whatsapp" class="form-check-input mt-0" {{ ($masterCard->show_whatsapp ?? 1) ? 'checked' : '' }}>
                                    </div>
                                    <input type="text" name="whatsapp" class="form-control" value="{{ $masterCard->whatsapp ?? '' }}">
                                </div>
                            </div>

                            <!-- Address & Pincode Lookup Section -->
                            <hr class="my-3">
                            <h6 class="text-secondary fw-bold"><i class="fa-solid fa-map-location-dot me-2"></i>Address & Location</h6>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Pincode</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" name="show_pincode" class="form-check-input mt-0" {{ ($masterCard->show_pincode ?? 1) ? 'checked' : '' }}>
                                    </div>
                                    <input type="text" id="pincodeInput" name="pincode" class="form-control" value="{{ $masterCard->pincode ?? '' }}" placeholder="Enter 6-digit Pincode">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Area / Post Office</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" name="show_area" class="form-check-input mt-0" {{ ($masterCard->show_area ?? 1) ? 'checked' : '' }}>
                                    </div>
                                    <select id="areaSelect" name="area" class="form-select">
                                        <option value="{{ $masterCard->area ?? '' }}">{{ $masterCard->area ?? 'Select Area' }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">City</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" name="show_city" class="form-check-input mt-0" {{ ($masterCard->show_city ?? 1) ? 'checked' : '' }}>
                                    </div>
                                    <input type="text" id="cityInput" name="city" class="form-control" value="{{ $masterCard->city ?? '' }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">State</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" name="show_state" class="form-check-input mt-0" {{ ($masterCard->show_state ?? 1) ? 'checked' : '' }}>
                                    </div>
                                    <input type="text" id="stateInput" name="state" class="form-control" value="{{ $masterCard->state ?? '' }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Street Address</label>
                                <div class="input-group">
                                    <div class="input-group-text">
                                        <input type="checkbox" name="show_address" class="form-check-input mt-0" {{ ($masterCard->show_address ?? 1) ? 'checked' : '' }}>
                                    </div>
                                    <input type="text" name="address" class="form-control" value="{{ $masterCard->address ?? '' }}">
                                </div>
                            </div>

                            <div class="col-md-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i>Save Master Config</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: Card View Design Engine Form -->
                <div class="tab-pane fade bg-white p-4 rounded shadow-sm" id="design-pane">
                    <h5 class="mb-3 text-success"><i class="fa-solid fa-palette me-2"></i>Create Custom Card Design</h5>
                    <form id="cardViewDesignForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Theme Preset / Pattern</label>
                                <select name="theme_style" id="themeStyleSelect" class="form-select">
                                    <option value="default">Default Dark Slate</option>
                                    <option value="classic-white">Classic Clean White</option>
                                    <option value="classic-dark">Classic Obsidian</option>
                                    <option value="metal-gold">Royal Metallic Gold</option>
                                    <option value="cyber-neon">Cyber Neon Indigo</option>
                                    <option value="emerald-mesh">Emerald Deep Gradient</option>
                                    <option value="seed-1024">Custom Mesh Seed #1024</option>
                                    <option value="seed-5088">Custom Mesh Seed #5088</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Font Family</label>
                                <select name="font_family" id="fontFamilySelect" class="form-select">
                                    <option value="'Poppins', sans-serif">Poppins</option>
                                    <option value="'Inter', sans-serif">Inter</option>
                                    <option value="'Montserrat', sans-serif">Montserrat</option>
                                    <option value="'Playfair Display', serif">Playfair Display</option>
                                    <option value="'Cinzel', serif">Cinzel Luxury</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Icon Style</label>
                                <select name="icon_style" id="iconStyleSelect" class="form-select">
                                    <option value="solid">Transparent / Solid</option>
                                    <option value="regular">Bordered Outline</option>
                                    <option value="square">White Square Box</option>
                                    <option value="circle">White Circle Badge</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Icon Display Mode</label>
                                <select name="icon_display_mode" id="iconModeSelect" class="form-select">
                                    <option value="icon_text">Icon + Text Label</option>
                                    <option value="only_icons">Only Action Icons</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Text Color Override</label>
                                <input type="color" name="custom_text_color" id="textColorPicker" class="form-control form-control-color w-100" value="#ffffff">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Icon Color Override</label>
                                <input type="color" name="custom_icon_color" id="iconColorPicker" class="form-control form-control-color w-100" value="#f59e0b">
                            </div>

                            <div class="col-md-12 text-end mt-4">
                                <button type="button" id="saveCardViewBtn" class="btn btn-success px-4 fw-bold"><i class="fa-solid fa-circle-check me-2"></i>Generate & Save View</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Live Preview Panel -->
        <div class="col-lg-5">
            <div class="sticky-top" style="top: 20px;">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-eye me-2"></i>Live Render Preview</span>
                        <span class="badge bg-warning text-dark">Realtime</span>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center bg-light p-3">
                        @include('vendor.card.render_engine', ['masterCard' => $masterCard ?? (object)[]])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dedicated JavaScript Script (PINCODE + LIVE PREVIEW SYNC) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. PINCODE AUTO-FETCH SCRIPT (Querying pincodes table)
    const pincodeInput = document.getElementById('pincodeInput');
    const areaSelect    = document.getElementById('areaSelect');
    const cityInput    = document.getElementById('cityInput');
    const stateInput   = document.getElementById('stateInput');

    if (pincodeInput) {
        pincodeInput.addEventListener('input', function() {
            let pin = this.value.trim();
            if (pin.length === 6) {
                fetch(`{{ route('vendor.card.pincode.lookup') }}?pincode=${pin}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            cityInput.value = data.city || '';
                            stateInput.value = data.state || '';
                            
                            areaSelect.innerHTML = '<option value="">Select Area</option>';
                            data.areas.forEach(a => {
                                areaSelect.innerHTML += `<option value="${a}">${a}</option>`;
                            });
                        }
                    })
                    .catch(err => console.error("Pincode Fetch Error:", err));
            }
        });
    }

    // 2. LIVE CARD VIEW PREVIEW SYNC
    const wrapper = document.querySelector('.vendor-card-box');
    
    document.getElementById('themeStyleSelect')?.addEventListener('change', function() {
        if (wrapper) {
            wrapper.dataset.theme = this.value;
            if (typeof syncVendorCard === 'function') syncVendorCard();
        }
    });

    document.getElementById('fontFamilySelect')?.addEventListener('change', function() {
        if (wrapper) wrapper.style.fontFamily = this.value;
    });

    document.getElementById('textColorPicker')?.addEventListener('input', function() {
        if (wrapper) wrapper.style.color = this.value;
    });

    // 3. AJAX SAVE CARD VIEW
    document.getElementById('saveCardViewBtn')?.addEventListener('click', function() {
        let formData = new FormData(document.getElementById('cardViewDesignForm'));

        fetch("{{ route('vendor.card.view.save') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = "{{ route('vendor.card.index') }}";
            } else {
                alert(data.message || "Error occurred");
            }
        })
        .catch(err => alert("Server error occurred!"));
    });
});
</script>
@endsection