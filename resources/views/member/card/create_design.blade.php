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
        <option value="metal-gold">Metal Gold Theme</option>
        <option value="fabric-denim">Fabric Denim Theme</option>
    </optgroup>
    
    <optgroup label="Creative & Artistic Textures (New)">
        <option value="texture-fish-stones">Fish Stones Theme</option>
        <option value="texture-torn-paper">Torn Paper Theme</option>
        <option value="texture-old-wood">Old Wood Theme</option>
        <option value="texture-ripped-jeans">Ripped Jeans Theme</option>
        <option value="texture-spider-web">Spider Web Theme</option>
        <option value="texture-dusty-sand">Dusty Sand Theme</option>
        <option value="texture-rusty-metal">Rusty Metal Theme</option>
        
        <!-- नए जोड़े गए शानदार डिज़ाइन थीम्स -->
        <option value="texture-neon-glow">Neon Glow Theme</option>
        <option value="texture-carbon-fiber">Carbon Fiber Theme</option>
        <option value="texture-marble-white">Luxury Marble White</option>
        <option value="texture-stained-glass">Stained Glass Theme</option>
        <option value="texture-holographic">Holographic Effect</option>
        <option value="texture-rose-gold">Rose Gold Luxury</option>
        <option value="texture-midnight-velvet">Midnight Velvet</option>
        <option value="texture-emerald-silk">Emerald Silk Theme</option>
        <option value="texture-cyberpunk-grid">Cyberpunk Grid</option>
        <option value="texture-vintage-leather">Vintage Leather</option>
        <option value="texture-sunset-orange">Sunset Orange Glow</option>
        <option value="texture-deep-ocean">Deep Ocean Wave</option>
        <option value="texture-royal-amethyst">Royal Amethyst Purple</option>
        <option value="texture-frost-glass">Frosted Glass Effect</option>
        <option value="texture-matte-obsidian">Matte Obsidian Black</option>
        <option value="texture-liquid-chrome">Liquid Chrome Silver</option>
        <option value="texture-plasma-energy">Plasma Energy Glow</option>
    </optgroup>

    <optgroup label="Dynamic Vibrant Gradients (1 - 2500)">
        @for ($i = 1; $i <= 2500; $i++)
            <option value="dyn-vibrant-{{ $i }}">Vibrant Color Theme #{{ $i }}</option>
        @endfor
    </optgroup>
    
    <optgroup label="Dynamic Pastel & Royal Shades (2501 - 5000)">
        @for ($i = 2501; $i <= 5000; $i++)
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
// Unique color generator for 5000+ themes
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
    let bgSize = 'cover';
    let textColor = '#ffffff'; // Default white text

    if (selectedTheme === 'default' || selectedTheme === 'classic-dark') {
        bgStyle = '#111827';
        textColor = '#ffffff';
    } else if (selectedTheme === 'classic-white') {
        bgStyle = '#ffffff';
        textColor = '#111827';
    } else if (selectedTheme === 'classic-modern') {
        bgStyle = 'linear-gradient(135deg, #1e3a8a, #3b82f6)';
        textColor = '#ffffff';
    } else if (selectedTheme === 'metal-gold') {
        bgStyle = 'linear-gradient(135deg, #bf953f, #fcf6ba, #aa771c)';
        textColor = '#3d2c04';
    } else if (selectedTheme === 'fabric-denim') {
        bgStyle = 'linear-gradient(135deg, #1e3c72, #2a5298)';
        textColor = '#ffffff';
    } 
    // --- Real & Artistic Multi-layered Textures & New Themes ---
    else if (selectedTheme === 'texture-fish-stones') {
        bgStyle = 'radial-gradient(circle at 20% 30%, #ff5722 0%, transparent 25%), radial-gradient(circle at 80% 70%, #00bcd4 0%, transparent 30%), linear-gradient(135deg, #37474f, #263238)';
        textColor = '#ffffff';
    } else if (selectedTheme === 'texture-torn-paper') {
        bgStyle = 'linear-gradient(135deg, #f9f9f9 0%, #eceff1 100%), repeating-linear-gradient(45deg, rgba(0,0,0,0.04) 0px, rgba(0,0,0,0.04) 2px, transparent 2px, transparent 4px)';
        textColor = '#111827';
    } else if (selectedTheme === 'texture-old-wood') {
        bgStyle = 'url("data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M0 20h40M20 0v40\' stroke=\'%23000000\' stroke-opacity=\'0.2\' stroke-width=\'1\' fill=\'none\'/%3E%3C/svg%3E"), linear-gradient(90deg, #3e2723 0%, #4e342e 50%, #3e2723 100%)';
        bgSize = 'auto, cover';
        textColor = '#d7ccc8';
    } else if (selectedTheme === 'texture-ripped-jeans') {
        bgStyle = 'url("data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.15\' fill-rule=\'evenodd\'%3E%3Cpath d=\'M0 3h20v1H0V3zm0 4h20v1H0V7zm0 4h20v1H0v-1zm0 4h20v1H0v-1z\'/%3E%3C/g%3E%3C/svg%3E"), linear-gradient(135deg, #1a237e 0%, #3949ab 100%)';
        bgSize = 'auto, cover';
        textColor = '#ffffff';
    } else if (selectedTheme === 'texture-spider-web') {
        bgStyle = 'radial-gradient(circle at center, #263238 0%, #0b1013 100%), repeating-radial-gradient(circle at center, transparent 0px, transparent 15px, rgba(255,255,255,0.08) 15px, rgba(255,255,255,0.08) 17px)';
        textColor = '#e0f7fa';
    } else if (selectedTheme === 'texture-dusty-sand') {
        bgStyle = 'url("data:image/svg+xml,%3Csvg width=\'12\' height=\'12\' viewBox=\'0 0 12 12\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'1.5\' fill=\'%23000000\' fill-opacity=\'0.15\'/%3E%3C/svg%3E"), linear-gradient(135deg, #d7ccc8 0%, #a1887f 100%)';
        bgSize = 'auto, cover';
        textColor = '#3e2723';
    } else if (selectedTheme === 'texture-rusty-metal') {
        bgStyle = 'linear-gradient(135deg, #bf360c 0%, #4e342e 100%), repeating-linear-gradient(-45deg, rgba(0,0,0,0.25) 0px, rgba(0,0,0,0.25) 4px, transparent 4px, transparent 8px)';
        textColor = '#ffccbc';
    } else if (selectedTheme === 'texture-neon-glow') {
        bgStyle = 'linear-gradient(135deg, #000428, #004e92)';
        textColor = '#00ffcc';
    } else if (selectedTheme === 'texture-carbon-fiber') {
        bgStyle = 'url("data:image/svg+xml,%3Csvg width=\'10\' height=\'10\' viewBox=\'0 0 10 10\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M0 0h5v5H0zM5 5h5v5H5z\' fill=\'%23ffffff\' fill-opacity=\'0.08\'/%3E%3C/svg%3E"), linear-gradient(135deg, #111111 0%, #222222 100%)';
        bgSize = 'auto, cover';
        textColor = '#ffffff';
    } else if (selectedTheme === 'texture-marble-white') {
        bgStyle = 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)';
        textColor = '#2c3e50';
    } else if (selectedTheme === 'texture-stained-glass') {
        bgStyle = 'linear-gradient(45deg, #ff9a9e, #fad0c4, #a18cd1)';
        textColor = '#222222';
    } else if (selectedTheme === 'texture-holographic') {
        bgStyle = 'linear-gradient(to right, #ff00ff, #00ffff, #ffff00)';
        textColor = '#ffffff';
    } else if (selectedTheme === 'texture-rose-gold') {
        bgStyle = 'linear-gradient(135deg, #b76e79, #e8b4b8, #d4af37)';
        textColor = '#ffffff';
    } else if (selectedTheme === 'texture-midnight-velvet') {
        bgStyle = 'linear-gradient(135deg, #0f0c29, #302b63, #24243e)';
        textColor = '#e2e8f0';
    } else if (selectedTheme === 'texture-emerald-silk') {
        bgStyle = 'linear-gradient(135deg, #0575e6, #00f2fe)';
        textColor = '#ffffff';
    } else if (selectedTheme === 'texture-cyberpunk-grid') {
        bgStyle = 'linear-gradient(135deg, #f72585, #7209b7, #3a0ca3)';
        textColor = '#4cc9f0';
    } else if (selectedTheme === 'texture-vintage-leather') {
        bgStyle = 'linear-gradient(135deg, #3e2723, #4e342e, #211512)';
        textColor = '#d7ccc8';
    } else if (selectedTheme === 'texture-sunset-orange') {
        bgStyle = 'linear-gradient(135deg, #ff4e50, #f9d423)';
        textColor = '#222222';
    } else if (selectedTheme === 'texture-deep-ocean') {
        bgStyle = 'linear-gradient(135deg, #2b5876, #4e4376)';
        textColor = '#ffffff';
    } else if (selectedTheme === 'texture-royal-amethyst') {
        bgStyle = 'linear-gradient(135deg, #9d50bb, #6e48aa)';
        textColor = '#ffffff';
    } else if (selectedTheme === 'texture-frost-glass') {
        bgStyle = 'linear-gradient(135deg, rgba(255,255,255,0.4), rgba(255,255,255,0.1))';
        textColor = '#111827';
    } else if (selectedTheme === 'texture-matte-obsidian') {
        bgStyle = '#1a1a1a';
        textColor = '#a3a3a3';
    } else if (selectedTheme === 'texture-liquid-chrome') {
        bgStyle = 'linear-gradient(135deg, #bdc3c7, #2c3e50)';
        textColor = '#ffffff';
    } else if (selectedTheme === 'texture-plasma-energy') {
        bgStyle = 'linear-gradient(135deg, #ff0844, #ffb199)';
        textColor = '#ffffff';
    }
    // --- Dynamic Support for up to 5000 Themes ---
    else if (selectedTheme.startsWith('dyn-vibrant-')) {
        let id = parseInt(selectedTheme.replace('dyn-vibrant-', ''));
        bgStyle = getThemeColors(id, 'vibrant');
        textColor = '#ffffff';
    } else if (selectedTheme.startsWith('dyn-royal-')) {
        let id = parseInt(selectedTheme.replace('dyn-royal-', ''));
        bgStyle = getThemeColors(id, 'royal');
        textColor = '#0f172a';
    }

    // Apply background, size, and text color correctly
    wrapper.style.setProperty('background-image', bgStyle, 'important');
    wrapper.style.setProperty('background-size', bgSize, 'important');
    wrapper.style.setProperty('color', textColor, 'important');
    
    const textElements = wrapper.querySelectorAll('h1, h2, h3, h4, h5, h6, p, span, strong, small, .card-text, .name-title, .company-title');
    textElements.forEach(el => {
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
        // Add event listener programmatically to ensure it catches all changes smoothly
        themeSelect.addEventListener('change', function() {
            changeThemePreview(this.value);
        });
    }
});
</script>
@endpush
@endsection