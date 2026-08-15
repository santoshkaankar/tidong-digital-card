@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/card-materials.css') }}">
    <style>
        .toggle-card-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: all 0.2s;
        }
        .toggle-card-box:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }
        .form-check-input {
            cursor: pointer;
            width: 2.6em;
            height: 1.3em;
        }
        .section-title-badge {
            background: #e0f2fe;
            color: #0369a1;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        /* Strict Credit Card Size & Styling for Preview */
        #live-card-container .card-material-wrapper {
            width: 340px !important;
            height: 215px !important;
            max-width: 340px !important;
            min-width: 340px !important;
            margin: 0 auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
            box-sizing: border-box;
            overflow: hidden;
            position: relative;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4 px-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0 text-dark">Create Custom Card Variant</h3>
            <p class="text-muted small mb-0">Select material categories, dropdown styles, and custom field toggles.</p>
        </div>
        <a href="{{ route('member.cards.index') }}" class="btn btn-outline-secondary rounded-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Cards
        </a>
    </div>

    <form action="{{ route('member.card.view.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            
            <!-- Left Column: Dropdown & Toggles -->
            <div class="col-lg-7 col-xl-7">
                
                <!-- Theme Selector Dropdown -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="fa-solid fa-layer-group text-primary me-2"></i>Select Theme & Material Category</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="theme_style" class="form-label fw-bold text-dark small">Choose Material Finish</label>
                                <select name="theme_style" id="theme_style" class="form-select form-select-lg rounded-3 fs-6 border-secondary-subtle" onchange="changeThemePreview(this.value)">
                                    <optgroup label="Category A: Standard / Modern Minimalist">
                                        <option value="default" selected>Modern Dark Minimal</option>
                                        <option value="classic-white">Classic Pure White</option>
                                        <option value="classic-dark">Deep Midnight Charcoal</option>
                                        <option value="classic-modern">Modern Gradient Blue</option>
                                    </optgroup>
                                    <optgroup label="Category B: Fabric & Cloth Textures">
                                        <option value="fabric-cotton">Cotton Weave Fabric</option>
                                        <option value="fabric-denim">Rough Blue Denim</option>
                                        <option value="fabric-silk">Royal Smooth Silk</option>
                                        <option value="fabric-canvas">Textured Art Canvas</option>
                                        <option value="fabric-velvet">Rich Velvet Texture</option>
                                    </optgroup>
                                    <optgroup label="Category C: Stone & Marble Textures">
                                        <option value="stone-marble">Italian White Marble</option>
                                        <option value="stone-granite">Dark Granite Stone</option>
                                        <option value="stone-slate">Black Slate Texture</option>
                                    </optgroup>
                                    <optgroup label="Category D: Natural Wood Grain">
                                        <option value="wood-oak">Classic Oak Wood</option>
                                        <option value="wood-walnut">Deep Walnut Timber</option>
                                        <option value="wood-teak">Natural Teak Finish</option>
                                    </optgroup>
                                    <optgroup label="Category E: Premium Metallic Finishes">
                                        <option value="metal-gold">Luxury Gold Foil</option>
                                        <option value="metal-silver">Brushed Metal Silver</option>
                                        <option value="metal-bronze">Antique Bronze</option>
                                    </optgroup>
                                    <optgroup label="Category F: Leather & Vintage Paper">
                                        <option value="leather-black">Black Grain Leather</option>
                                        <option value="vintage-paper">Classic Vintage Paper</option>
                                        <option value="paper-parchment">Aged Parchment</option>
                                    </optgroup>
                                    <optgroup label="Category G: Glass & Special Finishes">
                                        <option value="crystal-glass">Frosted Crystal Glass</option>
                                        <option value="dark-obsidian">Obsidian Reflective</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Field Toggles -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0"><i class="fa-solid fa-sliders text-primary me-2"></i>Visible Field Toggles</h5>
                        <span class="badge bg-light text-dark border">Toggle fields to show/hide live</span>
                    </div>
                    <div class="card-body px-4">
                        
                        <!-- 1. Profile Info -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Profile Details</span>
                            <div class="row g-2">
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Full Name (Locked)</label><input type="checkbox" class="form-check-input" checked disabled title="Name is mandatory"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Profile Photo</label><input type="checkbox" class="form-check-input" name="toggles[show_photo]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Business Name</label><input type="checkbox" class="form-check-input" name="toggles[show_business_name]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Designation</label><input type="checkbox" class="form-check-input" name="toggles[show_designation]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Tagline / Motto</label><input type="checkbox" class="form-check-input" name="toggles[show_tagline]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Nickname</label><input type="checkbox" class="form-check-input" name="toggles[show_nickname]" value="1"></div></div>
                            </div>
                        </div>

                        <!-- 2. Phone & Messaging -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Phone & Messaging</span>
                            <div class="row g-2">
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Primary Phone</label><input type="checkbox" class="form-check-input" name="toggles[show_phone]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Alternate Phone</label><input type="checkbox" class="form-check-input" name="toggles[show_alt_phone]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">WhatsApp Number</label><input type="checkbox" class="form-check-input" name="toggles[show_whatsapp]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Telegram</label><input type="checkbox" class="form-check-input" name="toggles[show_telegram]" value="1"></div></div>
                            </div>
                        </div>

                        <!-- 3. Emails & Web Links -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Emails & Websites</span>
                            <div class="row g-2">
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Gmail / Primary Email</label><input type="checkbox" class="form-check-input" name="toggles[show_gmail]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Yahoo Email</label><input type="checkbox" class="form-check-input" name="toggles[show_yahoo_email]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Other Email</label><input type="checkbox" class="form-check-input" name="toggles[show_other_email]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Website URL</label><input type="checkbox" class="form-check-input" name="toggles[show_website]" value="1"></div></div>
                            </div>
                        </div>

                        <!-- 4. Social Media Links -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Social Media Links</span>
                            <div class="row g-2">
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Facebook</label><input type="checkbox" class="form-check-input" name="toggles[show_facebook]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Instagram</label><input type="checkbox" class="form-check-input" name="toggles[show_instagram]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">LinkedIn</label><input type="checkbox" class="form-check-input" name="toggles[show_linkedin]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">YouTube Channel</label><input type="checkbox" class="form-check-input" name="toggles[show_youtube]" value="1"></div></div>
                            </div>
                        </div>

                        <!-- 5. Payments & QR -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Payments & QR Code</span>
                            <div class="row g-2">
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">UPI ID</label><input type="checkbox" class="form-check-input" name="toggles[show_upi_id]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Google Pay</label><input type="checkbox" class="form-check-input" name="toggles[show_gpay]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Paytm</label><input type="checkbox" class="form-check-input" name="toggles[show_paytm]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">QR Code Image</label><input type="checkbox" class="form-check-input" name="toggles[show_qr_code]" value="1"></div></div>
                            </div>
                        </div>

                        <!-- 6. Address & Location -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Address & Location</span>
                            <div class="row g-2">
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Street Address</label><input type="checkbox" class="form-check-input" name="toggles[show_address]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Area / Colony</label><input type="checkbox" class="form-check-input" name="toggles[show_area]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">City / District</label><input type="checkbox" class="form-check-input" name="toggles[show_city]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">State</label><input type="checkbox" class="form-check-input" name="toggles[show_state]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Pincode</label><input type="checkbox" class="form-check-input" name="toggles[show_pincode]" value="1"></div></div>
                                <div class="col-md-6"><div class="toggle-card-box"><label class="form-check-label small fw-bold text-dark">Google Maps Link</label><input type="checkbox" class="form-check-input" name="toggles[show_location_url]" value="1"></div></div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg rounded-3 shadow-sm py-3 fw-bold">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Card Variant
                    </button>
                </div>

            </div>

            <!-- Right Column: Live Card Preview -->
            <div class="col-lg-5 col-xl-5">
                <div class="sticky-top" style="top: 20px;">
                    <div class="card border-0 shadow-sm rounded-4 bg-light p-3 p-md-4 text-center">
                        <h6 class="fw-bold text-muted text-uppercase tracking-wider mb-3">Live Interactive Card Preview</h6>
                        
                        <div id="live-card-container" class="d-flex justify-content-center">
                            @include('member.card.render_engine', [
                                'masterCard'   => $masterCard,
                                'themeStyle'   => 'default',
                                'fullCardNo'   => $masterCard->card_no . '-A1',
                                'fieldToggles' => []
                            ])
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
function changeThemePreview(selectedTheme) {
    let wrapper = document.querySelector('#live-card-container .card-material-wrapper');
    if(wrapper) {
        wrapper.className = wrapper.className.replace(/theme-[a-zA-Z0-9-]+/g, '').trim();
        wrapper.classList.add('theme-' + selectedTheme);
    }
}

// Function to update preview elements based on toggles
function updateCardToggles() {
    document.querySelectorAll('input[type="checkbox"][name^="toggles"]').forEach(checkbox => {
        let nameAttr = checkbox.getAttribute('name');
        let match = nameAttr.match(/\[(.*?)\]/);
        if (!match) return;
        
        let fieldKey = match[1];
        
        // Find matching elements inside the live card preview container
        document.querySelectorAll('#live-card-container .' + fieldKey).forEach(el => {
            if (checkbox.checked) {
                el.classList.remove('d-none');
                el.style.display = ''; 
            } else {
                el.classList.add('d-none');
                el.style.display = 'none';
            }
        });
    });
}

// Attach change listener to all toggles
document.querySelectorAll('input[type="checkbox"][name^="toggles"]').forEach(checkbox => {
    checkbox.addEventListener('change', updateCardToggles);
});

// Run on initial page load to sync initial checked states
document.addEventListener("DOMContentLoaded", function() {
    updateCardToggles();
});
</script>

@endpush
@endsection