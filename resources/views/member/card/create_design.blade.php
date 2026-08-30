@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/card-materials.css') }}">
    <link rel="stylesheet" href="{{ asset('css/card-themes.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Inter:wght@400;600&family=Montserrat:wght@400;600;700&family=Playfair+Display:ital,wght@0,600;1,400&family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
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
            <p class="text-muted small mb-0">Select material categories, typography styles, icon colors, and custom field toggles.</p>
        </div>
        <a href="{{ route('member.card.index') }}" class="btn btn-outline-secondary rounded-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Cards
        </a>
    </div>

    <form action="{{ route('member.card.view.store') }}" method="POST" id="customCardDesignForm">
        @csrf
        
        <!-- Hidden BG Field mapping for DB -->
        <input type="hidden" name="custom_bg_color" id="custom_bg_color" value="#111827">

        <div class="row g-4">
            
            <!-- Left Column: Dropdowns, Styling & Toggles -->
            <div class="col-lg-7 col-xl-7">
                
                <!-- 1. Theme Selector Dropdown -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="fa-solid fa-layer-group text-primary me-2"></i>Select Theme & Material Category</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="theme_style" class="form-label fw-bold text-dark small">Choose From 5000+ Dynamic Color Finishes</label>
                                
                                <select name="theme_style" id="theme_style" class="form-select form-select-lg rounded-3 fs-6 border-secondary-subtle">
                                    <optgroup label="Standard / Minimalist">
                                        <option value="default" selected>Modern Dark Minimal</option>
                                        <option value="classic-white">Classic Pure White</option>
                                        <option value="classic-dark">Deep Midnight Charcoal</option>
                                        <option value="classic-modern">Modern Gradient Blue</option>
                                        <option value="metal-gold">Metal Gold Theme</option>
                                        <option value="fabric-denim">Fabric Denim Theme</option>
                                    </optgroup>
                                    
                                    <optgroup label="Creative & Artistic Textures">
                                        <option value="texture-fish-stones">Fish Stones Theme</option>
                                        <option value="texture-torn-paper">Torn Paper Theme</option>
                                        <option value="texture-old-wood">Old Wood Theme</option>
                                        <option value="texture-ripped-jeans">Ripped Jeans Theme</option>
                                        <option value="texture-spider-web">Spider Web Theme</option>
                                        <option value="texture-dusty-sand">Dusty Sand Theme</option>
                                        <option value="texture-rusty-metal">Rusty Metal Theme</option>
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

                <!-- 2. Typography & Icon Customization Section -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="fa-solid fa-pen-nib text-primary me-2"></i>Text & Icon Customization</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="row g-3">
                            <!-- Icon Display Mode -->
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold text-dark small">Icon Display Mode</label>
                                <select name="icon_display_mode" id="icon_display_mode" class="form-select rounded-3 border-secondary-subtle">
                                    <option value="icon_only" selected>Only Icons (Clean)</option>
                                    <option value="icon_text">Icon + Text / Number</option>
                                </select>
                            </div>

                            <!-- Font Family -->
                            <div class="col-md-6 mb-2">
                                <label for="font_family" class="form-label fw-bold text-dark small">Font Family</label>
                                <select name="font_family" id="font_family" class="form-select rounded-3 border-secondary-subtle">
                                    <option value="'Poppins', sans-serif" selected>Poppins (Modern Clean)</option>
                                    <option value="'Montserrat', sans-serif">Montserrat (Geometric Pro)</option>
                                    <option value="'Roboto', sans-serif">Roboto (Classic Tech)</option>
                                    <option value="'Playfair Display', serif">Playfair Display (Luxury Serif)</option>
                                    <option value="'Cinzel', serif">Cinzel (Royal Elegant)</option>
                                    <option value="'Inter', sans-serif">Inter (Minimalist)</option>
                                </select>
                            </div>

                            <!-- Icon Style -->
                            <div class="col-md-6">
                                <label for="icon_style" class="form-label fw-bold text-dark small">Icon Style</label>
                                <select name="icon_style" id="icon_style" class="form-select rounded-3 border-secondary-subtle">
                                    <option value="solid" selected>Solid (Filled)</option>
                                    <option value="regular">Outline (Regular)</option>
                                    <option value="badge">Circle Badge</option>
                                    <option value="square">Square Badge</option>
                                </select>
                            </div>

                            <!-- Custom Text Color (Mapped name to custom_text_color) -->
                            <div class="col-md-6">
                                <label for="custom_text_color" class="form-label fw-bold text-dark small">Custom Text Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color border-secondary-subtle rounded-start" id="text_color_picker" value="#ffffff">
                                    <input type="text" name="custom_text_color" id="text_color" class="form-control border-secondary-subtle rounded-end" value="#ffffff" placeholder="#ffffff">
                                </div>
                            </div>

                            <!-- Custom Icon Color (Mapped name to custom_icon_color) -->
                            <div class="col-md-6">
                                <label for="custom_icon_color" class="form-label fw-bold text-dark small">Custom Icon Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color border-secondary-subtle rounded-start" id="icon_color_picker" value="#3b82f6">
                                    <input type="text" name="custom_icon_color" id="icon_color" class="form-control border-secondary-subtle rounded-end" value="#3b82f6" placeholder="#3b82f6">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Field Toggles -->
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

                        <!-- Profile Details -->
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

                        <!-- Phone & Messaging -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Phone & Messaging</span>
                            <div class="row g-0">
                                <div class="col-md-6 px-1">{!! renderToggleBox('Primary Phone', 'toggles[show_phone]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Alternate Phone', 'toggles[show_alt_phone]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('WhatsApp Number', 'toggles[show_whatsapp]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Telegram', 'toggles[show_telegram]') !!}</div>
                            </div>
                        </div>

                        <!-- Emails & Web Links -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Emails & Websites</span>
                            <div class="row g-0">
                                <div class="col-md-6 px-1">{!! renderToggleBox('Gmail / Primary Email', 'toggles[show_gmail]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Yahoo Email', 'toggles[show_yahoo_email]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Other Email', 'toggles[show_other_email]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Website URL', 'toggles[show_website]') !!}</div>
                            </div>
                        </div>

                        <!-- Social Media Links -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Social Media Links</span>
                            <div class="row g-0">
                                <div class="col-md-6 px-1">{!! renderToggleBox('Facebook', 'toggles[show_facebook]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Instagram', 'toggles[show_instagram]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('LinkedIn', 'toggles[show_linkedin]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('YouTube Channel', 'toggles[show_youtube]') !!}</div>
                            </div>
                        </div>

                        <!-- Payments & QR -->
                        <div class="mb-3">
                            <span class="section-title-badge mb-2 d-inline-block">Payments & QR Code</span>
                            <div class="row g-0">
                                <div class="col-md-6 px-1">{!! renderToggleBox('UPI ID', 'toggles[show_upi_id]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Google Pay', 'toggles[show_gpay]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('Paytm', 'toggles[show_paytm]') !!}</div>
                                <div class="col-md-6 px-1">{!! renderToggleBox('QR Code Image', 'toggles[show_qr_code]') !!}</div>
                            </div>
                        </div>

                        <!-- Address & Location -->
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
function getThemeColors(id, type) {
    let hash1 = (id * 37) % 360;
    let hash2 = (id * 83) % 360;
    return type === 'vibrant'
        ? `linear-gradient(135deg, hsl(${hash1}, 85%, 50%), hsl(${hash2}, 90%, 35%))`
        : `linear-gradient(135deg, hsl(${hash1}, 65% , 88%), hsl(${hash2}, 75%, 70%))`;
}

function sanitizeHex(color, fallback) {
    if (!color) return fallback;
    color = color.trim();
    if (color.length === 4 && color.startsWith('#')) {
        color = '#' + color[1] + color[1] + color[2] + color[2] + color[3] + color[3];
    }
    return /^#[0-9A-F]{6}$/i.test(color) ? color : fallback;
}

// Live CSS Injection for Font, Colors & Icons
function applyDynamicStyles() {
    let fontFamily = document.getElementById('font_family')?.value || "'Poppins', sans-serif";
    let iconStyle  = document.getElementById('icon_style')?.value || "solid";
    
    let textColor  = sanitizeHex(document.getElementById('text_color_picker')?.value, "#ffffff");
    let iconColor  = sanitizeHex(document.getElementById('icon_color_picker')?.value, "#3b82f6");

    let styleTag = document.getElementById('card-live-preview-override-style');
    if (!styleTag) {
        styleTag = document.createElement('style');
        styleTag.id = 'card-live-preview-override-style';
        document.head.appendChild(styleTag);
    }

    let iconStyleCSS = '';
    if (iconStyle === 'badge') {
        iconStyleCSS = `
            #live-card-container .card-material-wrapper i,
            #live-card-container .card-material-wrapper svg,
            #live-card-container .card-material-wrapper [class*="fa-"] {
                background-color: #ffffff !important;
                border-radius: 50% !important;
                padding: 6px !important;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
            }
        `;
    } else if (iconStyle === 'square') {
        iconStyleCSS = `
            #live-card-container .card-material-wrapper i,
            #live-card-container .card-material-wrapper svg,
            #live-card-container .card-material-wrapper [class*="fa-"] {
                background-color: #ffffff !important;
                border-radius: 6px !important;
                padding: 6px !important;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
            }
        `;
    } else if (iconStyle === 'regular') {
        iconStyleCSS = `
            #live-card-container .card-material-wrapper i,
            #live-card-container .card-material-wrapper svg,
            #live-card-container .card-material-wrapper [class*="fa-"] {
                background-color: transparent !important;
                border: 1px solid ${iconColor} !important;
                border-radius: 50% !important;
                padding: 4px !important;
            }
        `;
    }

    styleTag.innerHTML = `
        #live-card-container .card-material-wrapper,
        #live-card-container .card-material-wrapper * {
            font-family: ${fontFamily} !important;
        }

        #live-card-container .card-material-wrapper,
        #live-card-container .card-material-wrapper p,
        #live-card-container .card-material-wrapper span,
        #live-card-container .card-material-wrapper div,
        #live-card-container .card-material-wrapper h1,
        #live-card-container .card-material-wrapper h2,
        #live-card-container .card-material-wrapper h3,
        #live-card-container .card-material-wrapper h4,
        #live-card-container .card-material-wrapper h5,
        #live-card-container .card-material-wrapper h6 {
            color: ${textColor} !important;
        }

        #live-card-container .card-material-wrapper i,
        #live-card-container .card-material-wrapper svg,
        #live-card-container .card-material-wrapper [class*="fa-"] {
            color: ${iconColor} !important;
        }

        ${iconStyleCSS}
    `;
}

function changeThemePreview(selectedTheme, isUserAction = false) {
    let wrapper = document.querySelector('#live-card-container .card-material-wrapper');
    if (!wrapper) return;

    let bgStyle = '#111827';
    let bgSize = 'cover';

    if (selectedTheme === 'classic-white') {
        bgStyle = '#ffffff';
    } else if (selectedTheme === 'classic-modern') {
        bgStyle = 'linear-gradient(135deg, #1e3a8a, #3b82f6)';
    } else if (selectedTheme === 'metal-gold') {
        bgStyle = 'linear-gradient(135deg, #bf953f, #fcf6ba, #aa771c)';
    } else if (selectedTheme.startsWith('dyn-vibrant-')) {
        let id = parseInt(selectedTheme.replace('dyn-vibrant-', ''));
        bgStyle = getThemeColors(id, 'vibrant');
    } else if (selectedTheme.startsWith('dyn-royal-')) {
        let id = parseInt(selectedTheme.replace('dyn-royal-', ''));
        bgStyle = getThemeColors(id, 'royal');
    }

    wrapper.style.setProperty('background', bgStyle, 'important');
    wrapper.style.setProperty('background-size', bgSize, 'important');

    applyDynamicStyles();
}

function updateCardToggles() {
    document.querySelectorAll('input[type="checkbox"][name^="toggles"]').forEach(checkbox => {
        let nameAttr = checkbox.getAttribute('name');
        if(!nameAttr) return;
        let match = nameAttr.match(/\[(.*?)\]/);
        if (!match) return;
        
        let fieldKey = match[1];
        document.querySelectorAll('#live-card-container .' + fieldKey).forEach(el => {
            el.style.display = checkbox.checked ? '' : 'none';
        });
    });
}

document.addEventListener("DOMContentLoaded", function() {
    let fontSelect   = document.getElementById('font_family');
    let iconSelect   = document.getElementById('icon_style');
    let themeSelect  = document.getElementById('theme_style');
    let textColorTxt = document.getElementById('text_color');
    let textColorClr = document.getElementById('text_color_picker');
    let iconColorTxt = document.getElementById('icon_color');
    let iconColorClr = document.getElementById('icon_color_picker');
    let formElement  = document.getElementById('customCardDesignForm');

    function bindColorPair(picker, text) {
        if (!picker || !text) return;
        text.value = picker.value;

        ['input', 'change'].forEach(evt => {
            picker.addEventListener(evt, function() {
                text.value = this.value;
                applyDynamicStyles();
            });
            text.addEventListener(evt, function() {
                let sanitized = sanitizeHex(this.value, null);
                if (sanitized) {
                    picker.value = sanitized;
                }
                applyDynamicStyles();
            });
        });
    }

    bindColorPair(textColorClr, textColorTxt);
    bindColorPair(iconColorClr, iconColorTxt);

    if (fontSelect) fontSelect.addEventListener('change', applyDynamicStyles);
    if (iconSelect) iconSelect.addEventListener('change', applyDynamicStyles);
    if (themeSelect) {
        themeSelect.addEventListener('change', function() {
            changeThemePreview(this.value, true);
        });
    }

    document.querySelectorAll('input[type="checkbox"][name^="toggles"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateCardToggles);
    });

    if (formElement) {
        formElement.addEventListener('submit', function() {
            let wrapper = document.querySelector('#live-card-container .card-material-wrapper');
            let activeBg = wrapper ? wrapper.style.background : '#111827';
            document.getElementById('custom_bg_color').value = activeBg || '#111827';
        });
    }

    updateCardToggles();
    if (themeSelect) changeThemePreview(themeSelect.value, false);
    applyDynamicStyles();
});
</script>
@endpush
@endsection