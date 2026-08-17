@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/card-materials.css') }}">
    <link rel="stylesheet" href="{{ asset('css/card-themes.css') }}">
    <style>
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
            transition: background 0.3s ease, color 0.3s ease;
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
                
                <!-- Theme Selector Dropdown (Expanded to 1000+ Dynamic Options) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="fa-solid fa-layer-group text-primary me-2"></i>Select Theme & Material Category</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="theme_style" class="form-label fw-bold text-dark small">Choose From 1000+ Dynamic Color Finishes</label>
                                <select name="theme_style" id="theme_style" class="form-select form-select-lg rounded-3 fs-6 border-secondary-subtle" onchange="changeThemePreview(this.value)">
                                    <optgroup label="Standard / Minimalist">
                                        <option value="default" selected>Modern Dark Minimal</option>
                                        <option value="classic-white">Classic Pure White</option>
                                        <option value="classic-dark">Deep Midnight Charcoal</option>
                                        <option value="classic-modern">Modern Gradient Blue</option>
                                    </optgroup>
                                    <optgroup label="Dynamic Vibrant Gradients (1 - 500)">
                                        @for ($i = 1; $i <= 500; $i++)
                                            <option value="dyn-vibrant-{{ $i }}">Vibrant Color Theme #{{ $i }}</option>
                                        @endfor
                                    </optgroup>
                                    <optgroup label="Dynamic Pastel & Royal Shades (501 - 1000)">
                                        @for ($i = 501; $i <= 1000; $i++)
                                            <option value="dyn-royal-{{ $i }}">Royal Shade Theme #{{ $i }}</option>
                                        @endfor
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
                        
                        @php
                            function renderToggleBox($label, $name, $checked = false, $disabled = false, $title = '') {
                                $checkedAttr = $checked ? 'checked' : '';
                                $disabledAttr = $disabled ? 'disabled' : '';
                                $titleAttr = $title ? 'title="'.$title.'"' : '';
                                $nameAttr = $name ? 'name="'.$name.'" value="1"' : '';
                                
                                return '<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px; margin-bottom: 8px;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="text-align: left; vertical-align: middle;">
                                                <span class="small fw-bold text-dark">'.$label.'</span>
                                            </td>
                                            <td style="text-align: right; vertical-align: middle; width: 50px;">
                                                <input type="checkbox" style="width: 2.2em; height: 1.2em; cursor: pointer; margin: 0;" class="form-check-input" '.$nameAttr.' '.$checkedAttr.' '.$disabledAttr.' '.$titleAttr.'>
                                            </td>
                                        </tr>
                                    </table>
                                </div>';
                            }
                        @endphp

                        <!-- 1. Profile Info -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Profile Details</span>
                            <div class="row g-0">
                                <div class="col-md-6 px-1">{!! renderToggleBox('Full Name (Locked)', '', true, true, 'Name is mandatory') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Profile Photo', 'toggles[show_photo]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Business Name', 'toggles[show_business_name]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Designation', 'toggles[show_designation]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Tagline / Motto', 'toggles[show_tagline]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Nickname', 'toggles[show_nickname]') !!}</div>
                            </div>
                        </div>

                        <!-- 2. Phone & Messaging -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Phone & Messaging</span>
                            <div class="row g-0">
                                <div class="col-md-6 px-1">{!! renderToggleBox('Primary Phone', 'toggles[show_phone]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Alternate Phone', 'toggles[show_alt_phone]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('WhatsApp Number', 'toggles[show_whatsapp]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Telegram', 'toggles[show_telegram]') !!}</div>
                            </div>
                        </div>

                        <!-- 3. Emails & Web Links -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Emails & Websites</span>
                            <div class="row g-0">
                                <div class="col-md-6 px-1">{!! renderToggleBox('Gmail / Primary Email', 'toggles[show_gmail]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Yahoo Email', 'toggles[show_yahoo_email]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Other Email', 'toggles[show_other_email]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Website URL', 'toggles[show_website]') !!}</div>
                            </div>
                        </div>

                        <!-- 4. Social Media Links -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Social Media Links</span>
                            <div class="row g-0">
                                <div class="col-md-6 px-1">{!! renderToggleBox('Facebook', 'toggles[show_facebook]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Instagram', 'toggles[show_instagram]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('LinkedIn', 'toggles[show_linkedin]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('YouTube Channel', 'toggles[show_youtube]') !!}</div>
                            </div>
                        </div>

                        <!-- 5. Payments & QR -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Payments & QR Code</span>
                            <div class="row g-0">
                                <div class="col-md-6 px-1">{!! renderToggleBox('UPI ID', 'toggles[show_upi_id]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Google Pay', 'toggles[show_gpay]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Paytm', 'toggles[show_paytm]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('QR Code Image', 'toggles[show_qr_code]') !!}</div>
                            </div>
                        </div>

                        <!-- 6. Address & Location -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Address & Location</span>
                        <div class="row g-0">
                                <div class="col-md-6 px-1">{!! renderToggleBox('Street Address', 'toggles[show_address]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Area / Colony', 'toggles[show_area]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('City / District', 'toggles[show_city]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('State', 'toggles[show_state]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Pincode', 'toggles[show_pincode]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Google Maps Link', 'toggles[show_location_url]') !!}</div>
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
// Unique color generator for 1000+ themes
function getThemeColors(id, type) {
    let hash1 = (id * 37) % 360;
    let hash2 = (id * 83) % 360;
    
    if(type === 'vibrant') {
        return `linear-gradient(135deg, hsl(${hash1}, 85%, 50%), hsl(${hash2}, 90%, 35%))`;
    } else {
        return `linear-gradient(135deg, hsl(${hash1}, 65% , 88%), hsl(${hash2}, 75%, 70%))`;
    }
}

function changeThemePreview(selectedTheme) {
    let wrapper = document.querySelector('#live-card-container .card-material-wrapper');
    if (!wrapper) return;

    let bgStyle = '';
    let textColor = '#ffffff'; // Default white text

    if (selectedTheme === 'default' || selectedTheme === 'classic-dark') {
        bgStyle = '#111827';
        textColor = '#ffffff';
    } else if (selectedTheme === 'classic-white') {
        bgStyle = '#ffffff';
        textColor = '#111827'; // Dark text for white background
    } else if (selectedTheme === 'classic-modern') {
        bgStyle = 'linear-gradient(135deg, #1e3a8a, #3b82f6)';
        textColor = '#ffffff';
    } else if (selectedTheme.startsWith('dyn-vibrant-')) {
        let id = parseInt(selectedTheme.replace('dyn-vibrant-', ''));
        bgStyle = getThemeColors(id, 'vibrant');
        textColor = '#ffffff';
    } else if (selectedTheme.startsWith('dyn-royal-')) {
        let id = parseInt(selectedTheme.replace('dyn-royal-', ''));
        bgStyle = getThemeColors(id, 'royal');
        textColor = '#0f172a'; // Dark text for pastel/royal light shades
    }

    // Apply with !important to override any conflicting external CSS
    wrapper.style.setProperty('background', bgStyle, 'important');
    wrapper.style.setProperty('color', textColor, 'important');
    
    // Force all child elements inside the card to follow the theme color
    wrapper.querySelectorAll('*').forEach(el => {
        el.style.setProperty('color', textColor, 'important');
    });
}

function updateCardToggles() {
    document.querySelectorAll('input[type="checkbox"][name^="toggles"]').forEach(checkbox => {
        let nameAttr = checkbox.getAttribute('name');
        if(!nameAttr) return;
        let match = nameAttr.match(/\[(.*?)\]/);
        if (!match) return;
        
        let fieldKey = match[1];
        
        document.querySelectorAll('#live-card-container .' + fieldKey).forEach(el => {
            if (checkbox.checked) {
                el.style.display = '';
            } else {
                el.style.display = 'none';
            }
        });
    });
}

document.querySelectorAll('input[type="checkbox"][name^="toggles"]').forEach(checkbox => {
    checkbox.addEventListener('change', updateCardToggles);
});

document.addEventListener("DOMContentLoaded", function() {
    updateCardToggles();
    let themeSelect = document.getElementById('theme_style');
    if(themeSelect) {
        changeThemePreview(themeSelect.value);
    }
});
</script>
@endpush
@endsection