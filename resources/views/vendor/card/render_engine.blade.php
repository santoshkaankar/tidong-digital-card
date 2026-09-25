@php
    // Card Data Resolution (Vendor Scope)
    $cardObj = $cardView ?? $card ?? $item ?? $vendorCard ?? null;

    $instanceId       = $instanceId ?? ($cardObj->id ?? rand(10000, 99999));
    $wrapperId        = "vendorCardRender_" . $instanceId;

    $themeStyle       = $cardObj->theme_style ?? $cardObj->theme ?? 'default';
    $fullCardNo       =$fullCardNo ?? ($cardObj->full_card_no ?? ($masterCard->card_no ?? '12091-080000001-V1'));
    
    $customTextColor   = $cardObj->custom_text_color ?? $cardObj->text_color ?? null;
    $customIconColor   = $cardObj->custom_icon_color ?? $cardObj->icon_color ?? null;
    $customIconStyle   = $cardObj->icon_style ?? $cardObj->custom_icon_style ?? 'solid';
    $customFont        = $cardObj->font_family ?? $cardObj->font ?? "'Poppins', sans-serif";
    $iconDisplayMode   = $cardObj->icon_display_mode ?? $cardObj->display_mode ?? 'icon_text';

    // Field Visibility Parsing
    $fieldToggles = $cardObj->field_toggles ?? $cardObj->field_visibility ?? [];
    if (is_string($fieldToggles)) {
        $fieldToggles = json_decode($fieldToggles, true) ?? [];
    }

    $isFieldActive = function($key) use ($fieldToggles) {
        if (empty($fieldToggles)) return true;

        $aliases = [
            'nickname'       => ['show_nickname', 'nickname'],
            'business_name'  => ['show_business_name', 'business_name'],
            'designation'    => ['show_designation', 'designation'],
            'tagline'        => ['show_tagline', 'tagline'],
            'qr_code'        => ['show_qr_code', 'qr_code'],
            'photo'          => ['show_photo', 'photo'],
            'phone'          => ['show_phone', 'phone'],
            'alt_phone'      => ['show_alt_phone', 'alt_phone'],
            'whatsapp'       => ['show_whatsapp', 'whatsapp'],
            'telegram'       => ['show_telegram', 'telegram'],
            'email'          => ['show_email', 'show_gmail', 'email'],
            'website'        => ['show_website', 'website'],
            'facebook'       => ['show_facebook', 'facebook'],
            'instagram'      => ['show_instagram', 'instagram'],
            'linkedin'       => ['show_linkedin', 'linkedin'],
            'youtube'        => ['show_youtube', 'youtube'],
            'upi'            => ['show_upi_id', 'upi'],
            'street_address' => ['show_address', 'street_address'],
            'area'           => ['show_area', 'area'],
            'city'           => ['show_city', 'city'],
            'state'          => ['show_state', 'state'],
            'pincode'        => ['show_pincode', 'pincode'],
            'google_maps'    => ['show_location_url', 'google_maps']
        ];

        $keysToTest = $aliases[$key] ?? [$key, 'show_' .$key];
        foreach ($keysToTest as$k) {
            if (array_key_exists($k,$fieldToggles)) {
                return filter_var($fieldToggles[$k], FILTER_VALIDATE_BOOLEAN);
            }
        }
        return false;
    };

    $rawWa = $masterCard->whatsapp ?? $masterCard->phone ?? '9876543210';
    $cleanWaNumber = preg_replace('/[^0-9]/', '',$rawWa);

    $formatUrl = function($url) {
        if (empty($url) \vert{}\vert{} trim($url) === '#') return '#';
        $url = trim($url);
        return \Illuminate\Support\Str::startsWith($url, ['http://', 'https://']) ? $url : 'https://' .$url;
    };
@endphp

<!-- External Fonts & Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Inter:wght@400;600&family=Montserrat:wght@500;700&family=Playfair+Display:wght@600;700&family=Poppins:wght@400;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    /* Scoped Field Visibility Engine */
    #{{ $wrapperId }} .v-field { display: none !important; }
    #{{ $wrapperId }} .v-field.v-active { display: inline-flex !important; }
    #{{ $wrapperId }} div.v-field.v-active,
    #{{ $wrapperId }} p.v-field.v-active,
    #{{ $wrapperId }} span.v-field.v-active { display: block !important; }

    /* Card Layout Dimensions */
    .vendor-card-box {
        width: 100%;
        max-width: 420px;
        min-height: 245px;
        aspect-ratio: 1.58 / 1;
        box-sizing: border-box;
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        transition: background 0.4s ease, border 0.3s ease, color 0.3s ease;
    }

    /* Pattern Overlay Layer */
    .vendor-card-box .v-pattern-layer {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 1;
        opacity: 0.18;
    }

    /* Content Layer */
    .vendor-card-box .v-content-layer {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Action Chips */
    .vendor-card-box .v-chip {
        font-size: 0.72rem;
        line-height: 1.2;
        color: inherit !important;
        text-decoration: none !important;
        background: rgba(255, 255, 255, 0.16);
        padding: 4px 9px 4px 6px;
        border-radius: 30px;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.22);
        max-width: 100%;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .vendor-card-box .v-chip:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
    }

    .vendor-card-box .v-icon-box {
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.22);
    }

    .vendor-card-box .v-icon-box i { font-size: 0.75rem; color: inherit; }

    /* Display Modes */
    .vendor-card-box.v-mode-only_icons .v-chip {
        background: transparent !important;
        border: none !important;
        backdrop-filter: none !important;
        padding: 0 !important;
    }
    .vendor-card-box.v-mode-only_icons .v-chip-label { display: none !important; }

    /* Icon Badge Styles */
    .vendor-card-box.v-style-square .v-icon-box { background: #ffffff !important; border-radius: 6px !important; color: #0f172a !important; }
    .vendor-card-box.v-style-circle .v-icon-box { background: #ffffff !important; border-radius: 50% !important; color: #0f172a !important; }
    .vendor-card-box.v-style-regular .v-icon-box { background: transparent !important; border: 1.5px solid currentColor !important; border-radius: 50% !important; }
    .vendor-card-box.v-style-solid .v-icon-box { background: transparent !important; border: none !important; }
</style>

<div id="{{ $wrapperId }}" 
     class="vendor-card-box v-mode-{{ str_replace(' ', '_', strtolower($iconDisplayMode)) }} v-style-{{$customIconStyle }} p-3 shadow-lg"
     data-theme="{{ $themeStyle }}"
     style="font-family: {{ $customFont }}; @if($customTextColor) color: {{$customTextColor }} !important; @endif">

    <!-- Theme Overlays -->
    <div class="v-pattern-layer" id="vPatternLayer_{{ $instanceId }}"></div>

    <div class="v-content-layer">
        <!-- Top Section -->
        <div class="d-flex justify-content-between align-items-start w-100">
            <div>
                <div class="d-flex align-items-baseline gap-1 mb-1">
                    <span class="fw-bold text-truncate" style="font-size: 1.08rem; line-height: 1.2;">
                        {{ $masterCard->name ?? 'Santosh Sharma' }}
                    </span>
                    <span class="v-field field-nickname fst-italic {{ $isFieldActive('nickname') ? 'v-active' : '' }}" style="font-size: 0.75rem; opacity: 0.85;">
                        ({{ $masterCard->nickname ?? 'Santosh' }})
                    </span>
                </div>

                <p class="v-field field-business_name mb-0 fw-semibold {{ $isFieldActive('business_name') ? 'v-active' : '' }}" style="font-size: 0.78rem; line-height: 1.15;">
                    {{ $masterCard->business_name ?? 'Tidong Marketing Pvt. Ltd.' }}
                </p>

                <p class="v-field field-designation mb-0 opacity-75 {{ $isFieldActive('designation') ? 'v-active' : '' }}" style="font-size: 0.7rem; line-height: 1.1;">
                    {{ $masterCard->designation ?? 'Director' }}
                </p>

                <p class="v-field field-tagline mb-0 opacity-60 {{ $isFieldActive('tagline') ? 'v-active' : '' }}" style="font-size: 0.65rem; line-height: 1.1;">
                    {{ $masterCard->tagline ?? $masterCard->motto ?? 'Hindustan ka apna shopping App' }}
                </p>
            </div>

            <!-- Profile Media / QR Code -->
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <div class="v-field field-qr_code {{ $isFieldActive('qr_code') ? 'v-active' : '' }}">
                    @if(!empty($masterCard->qr_code))
                        <img src="{{ asset($masterCard->qr_code) }}" alt="QR" style="width: 42px; height: 42px; object-fit: cover;" class="rounded-3 bg-white p-1 shadow-sm">
                    @else
                        <div class="bg-white rounded-3 p-1 text-dark d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                            <i class="fa-solid fa-qrcode" style="font-size: 22px; color: #0f172a;"></i>
                        </div>
                    @endif
                </div>

                <div class="v-field field-photo {{ $isFieldActive('photo') ? 'v-active' : '' }}">
                    @if(!empty($masterCard->photo))
                        <img src="{{ asset($masterCard->photo) }}" alt="Photo" class="rounded-circle border border-2 border-white shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-dark d-flex align-items-center justify-content-center text-white fw-bold border border-2 border-white shadow-sm" style="width: 42px; height: 42px; font-size: 0.95rem;">
                            {{ strtoupper(substr($masterCard->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Interactive Contact Chips -->
        <div class="d-flex flex-wrap gap-1.5 align-items-center my-1" style="max-height: 98px; overflow: hidden;">
            <div class="v-field field-phone {{ $isFieldActive('phone') ? 'v-active' : '' }}">
                <a href="tel:{{ $masterCard->phone ?? '9876543210' }}" class="v-chip d-inline-flex align-items-center gap-1">
                    <span class="v-icon-box" style="@if($customIconColor) color: {{$customIconColor }} !important; @endif"><i class="fa-solid fa-phone"></i></span>
                    <span class="v-chip-label">{{ $masterCard->phone ?? '9876543210' }}</span>
                </a>
            </div>

            <div class="v-field field-alt_phone {{ $isFieldActive('alt_phone') ? 'v-active' : '' }}">
                <a href="tel:{{ $masterCard->alt_phone ?? '9876543211' }}" class="v-chip d-inline-flex align-items-center gap-1">
                    <span class="v-icon-box" style="@if($customIconColor) color: {{$customIconColor }} !important; @endif"><i class="fa-solid fa-mobile-screen"></i></span>
                    <span class="v-chip-label">{{ $masterCard->alt_phone ?? '9876543211' }}</span>
                </a>
            </div>

            <div class="v-field field-whatsapp {{ $isFieldActive('whatsapp') ? 'v-active' : '' }}">
                <a href="https://wa.me/{{ $cleanWaNumber }}" target="_blank" class="v-chip d-inline-flex align-items-center gap-1">
                    <span class="v-icon-box" style="@if($customIconColor) color: {{$customIconColor }} !important; @endif"><i class="fa-brands fa-whatsapp"></i></span>
                    <span class="v-chip-label">{{ $masterCard->whatsapp ?? 'WhatsApp' }}</span>
                </a>
            </div>

            <div class="v-field field-telegram {{ $isFieldActive('telegram') ? 'v-active' : '' }}">
                <a href="https://t.me/{{ $masterCard->telegram ?? '#' }}" target="_blank" class="v-chip d-inline-flex align-items-center gap-1">
                    <span class="v-icon-box" style="@if($customIconColor) color: {{$customIconColor }} !important; @endif"><i class="fa-brands fa-telegram"></i></span>
                    <span class="v-chip-label">Telegram</span>
                </a>
            </div>

            <div class="v-field field-email {{ $isFieldActive('email') ? 'v-active' : '' }}">
                <a href="mailto:{{ $masterCard->gmail ?? 'info@tidong.in' }}" class="v-chip d-inline-flex align-items-center gap-1">
                    <span class="v-icon-box" style="@if($customIconColor) color: {{$customIconColor }} !important; @endif"><i class="fa-solid fa-envelope"></i></span>
                    <span class="v-chip-label">{{ $masterCard->gmail ?? 'Email' }}</span>
                </a>
            </div>

            <div class="v-field field-website {{ $isFieldActive('website') ? 'v-active' : '' }}">
                <a href="{{ $formatUrl($masterCard->website ?? '#') }}" target="_blank" class="v-chip d-inline-flex align-items-center gap-1">
                    <span class="v-icon-box" style="@if($customIconColor) color: {{$customIconColor }} !important; @endif"><i class="fa-solid fa-globe"></i></span>
                    <span class="v-chip-label">Website</span>
                </a>
            </div>

            <div class="v-field field-facebook {{ $isFieldActive('facebook') ? 'v-active' : '' }}">
                <a href="{{ $formatUrl($masterCard->facebook ?? '#') }}" target="_blank" class="v-chip d-inline-flex align-items-center gap-1">
                    <span class="v-icon-box" style="@if($customIconColor) color: {{$customIconColor }} !important; @endif"><i class="fa-brands fa-facebook-f"></i></span>
                    <span class="v-chip-label">Facebook</span>
                </a>
            </div>

            <div class="v-field field-instagram {{ $isFieldActive('instagram') ? 'v-active' : '' }}">
                <a href="{{ $formatUrl($masterCard->instagram ?? '#') }}" target="_blank" class="v-chip d-inline-flex align-items-center gap-1">
                    <span class="v-icon-box" style="@if($customIconColor) color: {{$customIconColor }} !important; @endif"><i class="fa-brands fa-instagram"></i></span>
                    <span class="v-chip-label">Instagram</span>
                </a>
            </div>

            <div class="v-field field-linkedin {{ $isFieldActive('linkedin') ? 'v-active' : '' }}">
                <a href="{{ $formatUrl($masterCard->linkedin ?? '#') }}" target="_blank" class="v-chip d-inline-flex align-items-center gap-1">
                    <span class="v-icon-box" style="@if($customIconColor) color: {{$customIconColor }} !important; @endif"><i class="fa-brands fa-linkedin-in"></i></span>
                    <span class="v-chip-label">LinkedIn</span>
                </a>
            </div>

            <div class="v-field field-youtube {{ $isFieldActive('youtube') ? 'v-active' : '' }}">
                <a href="{{ $formatUrl($masterCard->youtube ?? '#') }}" target="_blank" class="v-chip d-inline-flex align-items-center gap-1">
                    <span class="v-icon-box" style="@if($customIconColor) color: {{$customIconColor }} !important; @endif"><i class="fa-brands fa-youtube"></i></span>
                    <span class="v-chip-label">YouTube</span>
                </a>
            </div>

            <div class="v-field field-upi {{ $isFieldActive('upi') ? 'v-active' : '' }}">
                <a href="#" class="v-chip d-inline-flex align-items-center gap-1">
                    <span class="v-icon-box" style="@if($customIconColor) color: {{$customIconColor }} !important; @endif"><i class="fa-solid fa-wallet"></i></span>
                    <span class="v-chip-label">UPI</span>
                </a>
            </div>
        </div>

        <!-- Footer Section -->
        <div>
            <div class="opacity-80 mb-1 d-flex align-items-start" style="font-size: 0.65rem; line-height: 1.25;">
                <div class="v-field field-google_maps {{ $isFieldActive('google_maps') ? 'v-active' : '' }} me-1">
                    <a href="{{ $formatUrl($masterCard->location_url ?? '#') }}" target="_blank" class="text-warning text-decoration-none">
                        <i class="fa-solid fa-location-dot" style="font-size: 0.75rem;"></i>
                    </a>
                </div>
                <div>
                    <span class="v-field field-street_address {{ $isFieldActive('street_address') ? 'v-active' : '' }}">{{ $masterCard->address ?? '9A, Shakti Vihar' }}</span>
                    <span class="v-field field-area {{ $isFieldActive('area') ? 'v-active' : '' }}">{{ !empty($masterCard->area) ? ', ' .$masterCard->area : '' }}</span>
                    <span class="v-field field-city {{ $isFieldActive('city') ? 'v-active' : '' }}">{{ !empty($masterCard->city) ? ', ' .$masterCard->city : '' }}</span>
                    <span class="v-field field-state {{ $isFieldActive('state') ? 'v-active' : '' }}">{{ !empty($masterCard->state) ? ', ' .$masterCard->state : '' }}</span>
                    <span class="v-field field-pincode {{ $isFieldActive('pincode') ? 'v-active' : '' }}">{{ !empty($masterCard->pincode) ? ' - ' .$masterCard->pincode : '' }}</span>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-end border-top pt-1" style="border-color: rgba(255,255,255,0.2) !important;">
                <span class="font-monospace" style="font-size: 0.72rem; opacity: 0.85;">{{ $fullCardNo }}</span>
                <span class="fst-italic fw-semibold" style="font-size: 0.72rem; opacity: 0.9;">Powered by Tidong®</span>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const wrapperId = "{{ $wrapperId }}";
    const instanceId = "{{ $instanceId }}";

    // Presets Table
    const vendorStaticThemes = {
        'default': 'radial-gradient(circle at 10% 20%, #0f172a 0%, #1e293b 100%)',
        'classic-white': 'linear-gradient(135deg, #ffffff 0%, #f8fafc 100%)',
        'classic-dark': 'linear-gradient(135deg, #090d16 0%, #1a2332 100%)',
        'metal-gold': 'linear-gradient(135deg, #111827 0%, #1f2937 50%), linear-gradient(135deg, #bf953f, #fcf6ba, #aa771c)',
        'cyber-neon': 'radial-gradient(circle at 20% 20%, #4f46e5 0%, #0f172a 60%, #000000 100%)',
        'emerald-mesh': 'linear-gradient(135deg, #064e3b 0%, #022c22 50%, #0f172a 100%)'
    };

    // Dynamic Seed Pattern Engine
    function generateVendorPattern(seed) {
        let num = parseInt(seed) || 1;
        let h1 = (num * 137) % 360;
        let h2 = (h1 + 50) % 360;
        let h3 = (h1 + 120) % 360;
        let type = num % 4;

        if (type === 0) {
            return `radial-gradient(at 0% 0%, hsl(${h1}, 80%, 28%) 0px, transparent 50%), 
                    radial-gradient(at 100% 100%, hsl(${h2}, 75%, 22%) 0px, transparent 50%), #0f172a`;
        } else if (type === 1) {
            return `conic-gradient(from 180deg at 50% 50%, hsl(${h1}, 70%, 25%) 0deg, hsl(${h2}, 80%, 35%) 180deg, hsl(${h1}, 70%, 25%) 360deg)`;
        } else if (type === 2) {
            return `linear-gradient(135deg, hsl(${h1}, 75%, 25%) 0%, hsl(${h2}, 70%, 35%) 50%, hsl(${h3}, 80%, 20%) 100%)`;
        } else {
            return `radial-gradient(circle at 50% 0%, hsl(${h1}, 85%, 35%) 0%, hsl(${h2}, 70%, 15%) 70%, #0f172a 100%)`;
        }
    }

    function syncVendorCard() {
        const wrapper = document.getElementById(wrapperId);
        const patternLayer = document.getElementById("vPatternLayer_" + instanceId);
        if (!wrapper) return;

        // Theme Background
        let currentTheme = (wrapper.dataset.theme || 'default').toLowerCase();
        let bgStyle = vendorStaticThemes[currentTheme];

        if (!bgStyle) {
            let numMatch = currentTheme.match(/\d+/);
            let seed = numMatch ? numMatch[0] : 1;
            bgStyle = generateVendorPattern(seed);
        }

        wrapper.style.setProperty('background', bgStyle, 'important');
        if (currentTheme.includes('white')) {
            wrapper.style.setProperty('border', '1px solid #cbd5e1', 'important');
        } else {
            wrapper.style.setProperty('border', '1px solid rgba(255, 255, 255, 0.15)', 'important');
        }

        // Overlay Patterns
        if (patternLayer) {
            patternLayer.style.backgroundImage = 'radial-gradient(circle at 100% 100%, rgba(255,255,255,0.12) 0%, transparent 60%)';
        }

        // Checkbox Field Visibility Binds
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => {
            let key = (cb.name || cb.id || '').toLowerCase().replace('show_', '').replace('chk_', '');
            if (!key) return;

            let targetField = wrapper.querySelector('.field-' + key);
            if (targetField) {
                if (cb.checked) {
                    targetField.classList.add('v-active');
                } else {
                    targetField.classList.remove('v-active');
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', syncVendorCard);
    document.body.addEventListener('input', syncVendorCard);
    document.body.addEventListener('change', syncVendorCard);
    syncVendorCard();
})();
</script>