@php
    $cardObj = $cardView ?? $card ?? $item ?? null;

    // Instance Unique ID for independent rendering on list page
    $instanceId       = $instanceId ?? ($cardObj->id ?? rand(1000, 9999));
    $wrapperId        = "liveCardRenderWrapper_" . $instanceId;

    $themeStyle       = $cardObj->theme_style ?? $cardObj->theme ?? $themeStyle ?? 'default';
    $fullCardNo       = $fullCardNo ?? ($cardObj->full_card_no ?? ($masterCard->card_no ?? '12091-080000001-A1'));
    
    $customTextColor   = $cardObj->custom_text_color ?? $cardObj->text_color ?? $customTextColor ?? null;
    $customIconColor   = $cardObj->custom_icon_color ?? $cardObj->icon_color ?? $customIconColor ?? null;
    $customIconStyle   = $cardObj->icon_style ?? $cardObj->custom_icon_style ?? $cardObj->icon_badge_style ?? $customIconStyle ?? 'solid';
    $customFont        = $cardObj->font_family ?? $cardObj->font ?? $customFont ?? "'Poppins', sans-serif";
    $iconDisplayMode   = $cardObj->icon_display_mode ?? $cardObj->display_mode ?? $cardObj->icon_mode ?? $iconDisplayMode ?? 'icon_text';

    // Parse field toggles for saved variants from DB
    $fieldToggles = $cardObj->field_toggles ?? $cardObj->field_visibility ?? [];
    if (is_string($fieldToggles)) {
        $fieldToggles = json_decode($fieldToggles, true) ?? [];
    }

    // Fixed toggle active checker supporting both boolean DB saved keys and field aliases
    $isFieldActive = function($key) use ($fieldToggles) {
        if (empty($fieldToggles)) return true;

        $aliases = [
            'nickname'       => ['show_nickname', 'show_nick_name', 'show_nick', 'nickname'],
            'business_name'  => ['show_business_name', 'show_business', 'business_name'],
            'designation'    => ['show_designation', 'designation'],
            'tagline'        => ['show_tagline', 'show_motto', 'tagline'],
            'qr_code'        => ['show_qr_code', 'show_qr', 'show_qr_code_image', 'qr_code'],
            'photo'          => ['show_photo', 'show_profile_photo', 'show_profile', 'photo'],
            'phone'          => ['show_phone', 'show_primary_phone', 'phone'],
            'alt_phone'      => ['show_alt_phone', 'show_alternate_phone', 'alt_phone'],
            'whatsapp'       => ['show_whatsapp', 'show_whatsapp_number', 'whatsapp'],
            'telegram'       => ['show_telegram', 'telegram'],
            'email'          => ['show_gmail', 'show_gmail_primary_email', 'show_email', 'gmail', 'email'],
            'yahoo_email'    => ['show_yahoo_email', 'yahoo_email'],
            'other_email'    => ['show_other_email', 'other_email'],
            'website'        => ['show_website', 'show_website_url', 'website'],
            'facebook'       => ['show_facebook', 'facebook'],
            'instagram'      => ['show_instagram', 'instagram'],
            'linkedin'       => ['show_linkedin', 'linkedin'],
            'youtube'        => ['show_youtube', 'show_youtube_channel', 'youtube'],
            'upi'            => ['show_upi_id', 'upi'],
            'gpay'           => ['show_gpay', 'show_google_pay', 'gpay'],
            'paytm'          => ['show_paytm', 'paytm'],
            'street_address' => ['show_address', 'show_street_address', 'address', 'street_address'],
            'area'           => ['show_area', 'show_area_colony', 'area'],
            'city'           => ['show_city', 'show_city_district', 'city'],
            'state'          => ['show_state', 'state'],
            'pincode'        => ['show_pincode', 'pincode'],
            'google_maps'    => ['show_location_url', 'show_google_maps_link', 'show_maps', 'google_maps']
        ];

        $keysToTest = $aliases[$key] ?? [$key, 'show_' . $key];

        foreach ($keysToTest as $k) {
            if (array_key_exists($k, $fieldToggles)) {
                return filter_var($fieldToggles[$k], FILTER_VALIDATE_BOOLEAN);
            }
        }

        return false;
    };

    $rawWa = $masterCard->whatsapp ?? $masterCard->phone ?? '9876543210';
    $cleanWaNumber = preg_replace('/[^0-9]/', '', $rawWa);

    $formatUrl = function($url) {
        if (empty($url) || trim($url) === '#') return '#';
        $url = trim($url);
        if (!\Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])) return 'https://' . $url;
        return $url;
    };

    $facebookUrl  = $formatUrl($masterCard->facebook_link ?? $masterCard->facebook_url ?? $masterCard->facebook ?? '#');
    $instagramUrl = $formatUrl($masterCard->instagram_link ?? $masterCard->instagram ?? '#');
    $linkedinUrl  = $formatUrl($masterCard->linkedin_link ?? $masterCard->linkedin ?? '#');
    $youtubeUrl   = $formatUrl($masterCard->youtube_link ?? $masterCard->youtube ?? '#');
    $websiteUrl   = $formatUrl($masterCard->website_link ?? $masterCard->website ?? '#');
    $locationUrl  = $formatUrl($masterCard->map_location_link ?? $masterCard->location_url ?? '#');
@endphp

<!-- FontAwesome Icons & Google Fonts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Montserrat:wght@400;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700&family=Poppins:wght@300;400;600;700&family=Roboto:wght@400;500;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
    /* Strict Toggle Engine Rules */
    .card-material-wrapper .f-item { display: none !important; }
    .card-material-wrapper .f-item.active-field { display: inline-flex !important; }
    .card-material-wrapper div.f-item.active-field,
    .card-material-wrapper span.f-item.active-field { display: inline-block !important; }
    .card-material-wrapper div.f-item.active-field.block-elem { display: block !important; }

    .auto-fit-card { width: 100%; max-width: 410px; min-height: 240px; aspect-ratio: 1.6 / 1; }
    
    .info-chip-item {
        font-size: 0.72rem; line-height: 1.2; text-decoration: none !important; color: inherit !important;
        background: rgba(255, 255, 255, 0.18); padding: 3px 8px 3px 5px; border-radius: 20px;
        backdrop-filter: blur(4px); max-width: 100%; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;
    }

    .action-icon {
        width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;
        flex-shrink: 0; border-radius: 6px; background: rgba(255, 255, 255, 0.2); transition: all 0.2s ease;
    }

    .action-icon i { font-size: 0.8rem; color: inherit; }

    /* Display Mode Handling (Icon Only vs Icon + Text) */
    .card-material-wrapper.mode-only-icons .info-chip-item,
    .card-material-wrapper.mode-only_icons .info-chip-item,
    .card-material-wrapper.mode-clean .info-chip-item,
    .card-material-wrapper.mode-icon_only .info-chip-item {
        background: transparent !important; backdrop-filter: none !important; padding: 0 !important;
    }
    
    .card-material-wrapper.mode-only-icons .chip-text,
    .card-material-wrapper.mode-only_icons .chip-text,
    .card-material-wrapper.mode-clean .chip-text,
    .card-material-wrapper.mode-icon_only .chip-text { display: none !important; }
    
    .card-material-wrapper.mode-only-text .action-icon,
    .card-material-wrapper.mode-only_text .action-icon { display: none !important; }
    .card-material-wrapper.mode-only-text .chip-text,
    .card-material-wrapper.mode-only_text .chip-text { display: inline !important; }

    /* Icon Style Badges */
    .card-material-wrapper.style-square .action-icon { background: #ffffff !important; border-radius: 6px !important; color: #1e293b !important; }
    .card-material-wrapper.style-badge .action-icon, 
    .card-material-wrapper.style-circle .action-icon { background: #ffffff !important; border-radius: 50% !important; color: #1e293b !important; }
    .card-material-wrapper.style-regular .action-icon { background: transparent !important; border: 1.5px solid currentColor !important; border-radius: 50% !important; }
    .card-material-wrapper.style-solid .action-icon { background: transparent !important; border: none !important; }
</style>

<div id="{{ $wrapperId }}" class="card-material-wrapper auto-fit-card mode-{{ str_replace(' ', '_', strtolower($iconDisplayMode)) }} style-{{ $customIconStyle }} position-relative rounded-4 p-3 d-flex flex-column justify-content-between shadow-lg" 
     data-theme="{{ $themeStyle }}"
     style="overflow: hidden; font-family: {{ $customFont }}; @if($customTextColor) color: {{ $customTextColor }} !important; @endif">

    <!-- Top Header Section -->
    <div class="d-flex justify-content-between align-items-start w-100">
       <div>
           <div style="margin-bottom: 2px;">
               <span class="fw-bold text-truncate card-main-text" style="font-size: 1.05rem; line-height: 1.2; display: inline-block; color: inherit;">
                   {{ $masterCard->name ?? 'Meenu Sharma' }}
               </span>
               <span class="fst-italic show_nickname show_nick_name show_nick f-item {{ $isFieldActive('nickname') ? 'active-field' : '' }}" style="font-size: 0.75rem; opacity: 0.85;">
                   ({{ $masterCard->nickname ?? 'Minni' }})
               </span>
           </div>
           
           <div class="show_business_name show_business f-item block-elem {{ $isFieldActive('business_name') ? 'active-field' : '' }}">
               <p class="mb-0 fw-semibold text-start card-sub-text" style="font-size: 0.78rem; line-height: 1.1; opacity: 0.9; color: inherit;">
                   {{ $masterCard->business_name ?? 'Tidong Marketing Pvt. Ltd.' }}
               </p>
           </div>
           
           <div class="show_designation f-item block-elem {{ $isFieldActive('designation') ? 'active-field' : '' }}">
               <p class="mb-0 opacity-75 text-start card-sub-text" style="font-size: 0.7rem; line-height: 1.1; color: inherit;">
                   {{ $masterCard->designation ?? 'Director' }}
               </p>
           </div>

           <div class="show_tagline show_motto f-item block-elem {{ $isFieldActive('tagline') ? 'active-field' : '' }}">
               <p class="mb-0 opacity-50 text-start card-sub-text" style="font-size: 0.65rem; line-height: 1.1; color: inherit;">
                   {{ $masterCard->tagline ?? $masterCard->motto ?? 'Hindustan ka apna shopping App' }}
               </p>
           </div>
       </div>

        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <div class="show_qr_code show_qr show_qr_code_image f-item {{ $isFieldActive('qr_code') ? 'active-field' : '' }}">
                @if(!empty($masterCard->qr_code))
                    <img src="{{ asset($masterCard->qr_code) }}" alt="QR" style="width: 40px; height: 40px; object-fit: cover;" class="rounded bg-white p-0.5 shadow-sm">
                @else
                    <div class="bg-white rounded p-1 text-dark d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-qrcode" style="font-size: 24px;"></i>
                    </div>
                @endif
            </div>

            <div class="show_photo show_profile_photo show_profile f-item {{ $isFieldActive('photo') ? 'active-field' : '' }}">
                @if(!empty($masterCard->photo))
                    <img src="{{ asset($masterCard->photo) }}" alt="Photo" class="rounded-circle border border-2 border-light shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold border border-2 border-light" style="width: 40px; height: 40px; font-size: 0.9rem;">
                        {{ strtoupper(substr($masterCard->name ?? 'T', 0, 1)) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Contact, Social, Messaging & Payments Chips Container -->
    <div class="d-flex flex-wrap gap-2 align-items-center my-1" style="max-height: 95px; overflow: hidden;">
        <!-- Primary Phone -->
        <div class="show_primary_phone show_phone f-item {{ $isFieldActive('phone') ? 'active-field' : '' }}">
            <a href="tel:{{ $masterCard->phone ?? '6395392537' }}" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-solid fa-phone-alt"></i></span>
                <span class="chip-text">{{ $masterCard->phone ?? '6395392537' }}</span>
            </a>
        </div>

        <!-- Alternate Phone -->
        <div class="show_alternate_phone show_alt_phone f-item {{ $isFieldActive('alt_phone') ? 'active-field' : '' }}">
            <a href="tel:{{ $masterCard->alt_phone ?? '9634759912' }}" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-solid fa-mobile-screen-button"></i></span>
                <span class="chip-text">{{ $masterCard->alt_phone ?? '9634759912' }}</span>
            </a>
        </div>

        <!-- WhatsApp -->
        <div class="show_whatsapp_number show_whatsapp f-item {{ $isFieldActive('whatsapp') ? 'active-field' : '' }}">
            <a href="https://wa.me/{{ $cleanWaNumber }}" target="_blank" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-brands fa-whatsapp"></i></span>
                <span class="chip-text">{{ $masterCard->whatsapp ?? '6395392537' }}</span>
            </a>
        </div>

        <!-- Telegram -->
        <div class="show_telegram f-item {{ $isFieldActive('telegram') ? 'active-field' : '' }}">
            <a href="https://t.me/{{ $masterCard->telegram ?? '#' }}" target="_blank" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-brands fa-telegram"></i></span>
                <span class="chip-text">Telegram</span>
            </a>
        </div>

        <!-- Emails & Websites -->
        <div class="show_gmail_primary_email show_gmail show_email f-item {{ $isFieldActive('email') ? 'active-field' : '' }}">
            <a href="mailto:{{ $masterCard->gmail ?? 'info@tidong.in' }}" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-solid fa-envelope"></i></span>
                <span class="chip-text">{{ $masterCard->gmail ?? 'info@tidong.in' }}</span>
            </a>
        </div>

        <div class="show_yahoo_email f-item {{ $isFieldActive('yahoo_email') ? 'active-field' : '' }}">
            <a href="mailto:{{ $masterCard->yahoo_email ?? '#' }}" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-brands fa-yahoo"></i></span>
                <span class="chip-text">Yahoo</span>
            </a>
        </div>

        <div class="show_other_email f-item {{ $isFieldActive('other_email') ? 'active-field' : '' }}">
            <a href="mailto:{{ $masterCard->other_email ?? '#' }}" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-solid fa-at"></i></span>
                <span class="chip-text">Other Email</span>
            </a>
        </div>

        <div class="show_website_url show_website f-item {{ $isFieldActive('website') ? 'active-field' : '' }}">
            <a href="{{ $websiteUrl }}" target="_blank" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-solid fa-globe"></i></span>
                <span class="chip-text">Website</span>
            </a>
        </div>

        <!-- Social Media Links -->
        <div class="show_facebook f-item {{ $isFieldActive('facebook') ? 'active-field' : '' }}">
            <a href="{{ $facebookUrl }}" target="_blank" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-brands fa-facebook"></i></span>
                <span class="chip-text">Facebook</span>
            </a>
        </div>

        <div class="show_instagram f-item {{ $isFieldActive('instagram') ? 'active-field' : '' }}">
            <a href="{{ $instagramUrl }}" target="_blank" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-brands fa-instagram"></i></span>
                <span class="chip-text">Instagram</span>
            </a>
        </div>

        <div class="show_linkedin f-item {{ $isFieldActive('linkedin') ? 'active-field' : '' }}">
            <a href="{{ $linkedinUrl }}" target="_blank" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-brands fa-linkedin"></i></span>
                <span class="chip-text">LinkedIn</span>
            </a>
        </div>

        <div class="show_youtube_channel show_youtube f-item {{ $isFieldActive('youtube') ? 'active-field' : '' }}">
            <a href="{{ $youtubeUrl }}" target="_blank" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-brands fa-youtube"></i></span>
                <span class="chip-text">YouTube</span>
            </a>
        </div>

        <!-- Payments & UPI -->
        <div class="show_upi_id f-item {{ $isFieldActive('upi') ? 'active-field' : '' }}">
            <a href="#" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-solid fa-wallet"></i></span>
                <span class="chip-text">UPI</span>
            </a>
        </div>

        <div class="show_google_pay f-item {{ $isFieldActive('gpay') ? 'active-field' : '' }}">
            <a href="#" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-brands fa-google-pay"></i></span>
                <span class="chip-text">GPay</span>
            </a>
        </div>

        <div class="show_paytm f-item {{ $isFieldActive('paytm') ? 'active-field' : '' }}">
            <a href="#" class="info-chip-item d-inline-flex align-items-center gap-1">
                <span class="action-icon" style="@if($customIconColor) color: {{ $customIconColor }} !important; @endif"><i class="fa-solid fa-money-check"></i></span>
                <span class="chip-text">Paytm</span>
            </a>
        </div>
    </div>

    <!-- Bottom Footer Section (Address & Card No) -->
    <div>
        <div class="opacity-75 mb-1 d-flex align-items-start card-sub-text" style="font-size: 0.65rem; line-height: 1.25; max-height: 2.8em; overflow: hidden; color: inherit;">
            <div class="show_google_maps_link show_location_url show_maps f-item {{ $isFieldActive('google_maps') ? 'active-field' : '' }} me-1 mt-0.5">
                <a href="{{ $locationUrl }}" target="_blank" class="text-warning text-decoration-none">
                    <i class="fa-solid fa-map-location-dot" style="font-size: 0.75rem;"></i>
                </a>
            </div>
            <div>
                <span class="show_street_address show_address f-item {{ $isFieldActive('street_address') ? 'active-field' : '' }}">{{ $masterCard->address ?? '9A, Shakti Vihar' }}</span>
                <span class="show_area_colony show_area f-item {{ $isFieldActive('area') ? 'active-field' : '' }}">{{ !empty($masterCard->area) ? ', ' . $masterCard->area : '' }}</span>
                <span class="show_city_district show_city f-item {{ $isFieldActive('city') ? 'active-field' : '' }}">{{ !empty($masterCard->city) ? ', ' . $masterCard->city : '' }}</span>
                <span class="show_state f-item {{ $isFieldActive('state') ? 'active-field' : '' }}">{{ !empty($masterCard->state) ? ', ' . $masterCard->state : '' }}</span>
                <span class="show_pincode f-item {{ $isFieldActive('pincode') ? 'active-field' : '' }}">{{ !empty($masterCard->pincode) ? ' - ' . $masterCard->pincode : '' }}</span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-end border-top pt-1" style="border-color: rgba(255,255,255,0.2) !important;">
            <span class="font-monospace" style="font-size: 0.8rem; opacity: 0.9;">{{ $fullCardNo }}</span>
            <span class="fst-italic fw-semibold card-sub-text" style="font-size: 0.8rem; opacity: 0.95; color: inherit;">Powered by Tidong</span>
        </div>
    </div>
</div>

<script>
(function() {
    const wrapperId = "{{ $wrapperId }}";
    
    // Named Themes Mapping
    const staticThemes = {
        'white': '#ffffff', 'classic pure white': '#ffffff', 'pure white': '#ffffff',
        'light': '#f8fafc', 'black': '#0f172a',
        'dark': 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)',
        'blue': 'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)',
        'purple': 'linear-gradient(135deg, #884386 0%, #632d62 100%)',
        'gold': 'linear-gradient(135deg, #bf953f 0%, #fcf6ba 50%, #b38728 100%)',
        'emerald': 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
        'sunset': 'linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%)',
        'red': 'linear-gradient(135deg, #cb2d3e 0%, #ef473a 100%)'
    };

    function getDynamicGradient(seedNumber) {
        let num = parseInt(seedNumber) || 1;
        let hue1 = (num * 137) % 360; 
        let hue2 = (hue1 + 45) % 360;
        return `linear-gradient(135deg, hsl(${hue1}, 65%, 45%) 0%, hsl(${hue2}, 75%, 25%) 100%)`;
    }

    const fontFamiliesMap = {
        'roboto': "'Roboto', sans-serif",
        'playfair': "'Playfair Display', serif",
        'poppins': "'Poppins', sans-serif",
        'montserrat': "'Montserrat', sans-serif",
        'cinzel': "'Cinzel', serif",
        'inter': "'Inter', sans-serif"
    };

    function applyCardRendering() {
        const cardWrapper = document.getElementById(wrapperId);
        if (!cardWrapper) return;

        let allSelects = Array.from(document.querySelectorAll('select'));

        // 1. Theme Engine (Reads from Form or Data-Theme Attribute)
        let themeAttr = (cardWrapper.dataset.theme || '').toLowerCase();
        let themeSelect = allSelects.find(s => 
            s.name.includes('theme') || s.id.includes('theme') || s.name.includes('category') ||
            Array.from(s.options).some(o => o.text.toLowerCase().includes('theme') || o.text.toLowerCase().includes('white') || o.text.toLowerCase().includes('vibrant'))
        );

        let optText = themeAttr;
        let valText = themeAttr;

        if (themeSelect) {
            let selectedOpt = themeSelect.options[themeSelect.selectedIndex];
            if (selectedOpt) optText = selectedOpt.text.toLowerCase();
            valText = themeSelect.value.toLowerCase();
        }

        let matchedBg = null;
        Object.keys(staticThemes).forEach(key => {
            if (optText.includes(key) || valText.includes(key)) {
                matchedBg = staticThemes[key];
            }
        });

        if (!matchedBg) {
            let numMatch = (optText + ' ' + valText).match(/\d+/);
            let themeSeed = numMatch ? numMatch[0] : (themeSelect ? themeSelect.selectedIndex + 1 : 1);
            matchedBg = getDynamicGradient(themeSeed);
        }

        if (matchedBg) {
            cardWrapper.style.setProperty('background', matchedBg, 'important');
            cardWrapper.style.setProperty('background-image', matchedBg.includes('gradient') ? matchedBg : 'none', 'important');
        }

        // Live Create Form Sync (Only runs on create/edit page when inputs exist)
        let fontSelect = allSelects.find(s => s.name.includes('font') || s.id.includes('font') || Array.from(s.options).some(o => o.text.toLowerCase().includes('roboto') || o.text.toLowerCase().includes('playfair')));
        if (fontSelect) {
            let optT = fontSelect.options[fontSelect.selectedIndex] ? fontSelect.options[fontSelect.selectedIndex].text.toLowerCase() : '';
            let valT = fontSelect.value.toLowerCase();
            Object.keys(fontFamiliesMap).forEach(key => {
                if (optT.includes(key) || valT.includes(key)) cardWrapper.style.setProperty('font-family', fontFamiliesMap[key], 'important');
            });
        }

        let modeSelect = allSelects.find(s => s.name.includes('display_mode') || s.name.includes('icon_mode') || s.id.includes('display_mode') || Array.from(s.options).some(o => o.text.toLowerCase().includes('clean') || o.text.toLowerCase().includes('only icons')));
        if (modeSelect && modeSelect.value) {
            let val = modeSelect.value.toString().toLowerCase();
            let optT = modeSelect.options[modeSelect.selectedIndex] ? modeSelect.options[modeSelect.selectedIndex].text.toLowerCase() : '';
            cardWrapper.classList.remove('mode-icon-text', 'mode-icon_text', 'mode-only-icons', 'mode-only_icons', 'mode-only-text');
            if (val.includes('clean') || val.includes('only_icon') || val.includes('icon_only') || optT.includes('only icons') || optT.includes('clean')) {
                cardWrapper.classList.add('mode-only_icons');
            } else {
                cardWrapper.classList.add('mode-icon_text');
            }
        }

        let textColorPickers = document.querySelectorAll('input[type="color"][name*="text_color"], input[type="color"][id*="text_color"]');
        let textInputs = document.querySelectorAll('input[type="text"][name*="text_color"], input[type="text"][id*="text_color"]');
        let activeTextColor = null;

        textColorPickers.forEach(picker => { if (picker.value) { activeTextColor = picker.value; textInputs.forEach(txtInp => txtInp.value = picker.value); } });
        textInputs.forEach(txtInp => {
            let val = txtInp.value ? txtInp.value.trim() : '';
            if (val && /^#([0-9A-F]{3}){1,2}$/i.test(val)) { activeTextColor = val; textColorPickers.forEach(picker => picker.value = val); }
        });

        if (activeTextColor) {
            cardWrapper.style.setProperty('color', activeTextColor, 'important');
            cardWrapper.querySelectorAll('.card-main-text, .card-sub-text, span, div, p').forEach(el => { el.style.setProperty('color', activeTextColor, 'important'); });
        }

        let iconColorPickers = document.querySelectorAll('input[type="color"][name*="icon_color"], input[type="color"][id*="icon_color"]');
        let iconTextInputs = document.querySelectorAll('input[type="text"][name*="icon_color"], input[type="text"][id*="icon_color"]');
        let activeIconColor = null;

        iconColorPickers.forEach(picker => { if (picker.value) { activeIconColor = picker.value; iconTextInputs.forEach(txtInp => txtInp.value = picker.value); } });
        iconTextInputs.forEach(txtInp => {
            let val = txtInp.value ? txtInp.value.trim() : '';
            if (val && /^#([0-9A-F]{3}){1,2}$/i.test(val)) { activeIconColor = val; iconColorPickers.forEach(picker => picker.value = val); }
        });

        if (activeIconColor) {
            cardWrapper.querySelectorAll('.action-icon, .action-icon i').forEach(el => {
                el.style.setProperty('color', activeIconColor, 'important');
                if (cardWrapper.classList.contains('style-regular')) el.style.setProperty('border-color', activeIconColor, 'important');
            });
        }

        let styleSelect = allSelects.find(s => s.name.includes('icon_style') || s.id.includes('icon_style') || Array.from(s.options).some(o => o.text.toLowerCase().includes('outline') || o.text.toLowerCase().includes('regular')));
        if (styleSelect) {
            let val = styleSelect.value.toLowerCase();
            let optT = styleSelect.options[styleSelect.selectedIndex] ? styleSelect.options[styleSelect.selectedIndex].text.toLowerCase() : '';
            cardWrapper.classList.remove('style-solid', 'style-regular', 'style-badge', 'style-square', 'style-circle');
            if (val.includes('outline') || val.includes('regular') || optT.includes('outline') || optT.includes('regular')) {
                cardWrapper.classList.add('style-regular');
            } else if (val.includes('circle') || optT.includes('circle')) {
                cardWrapper.classList.add('style-circle');
            } else if (val.includes('square') || optT.includes('square')) {
                cardWrapper.classList.add('style-square');
            } else {
                cardWrapper.classList.add('style-solid');
            }
        }

        // Apply Live Checkbox listeners ONLY on create/edit form
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        if (checkboxes.length > 0) {
            checkboxes.forEach(cb => {
                let parentBox = cb.closest('.form-check') || cb.closest('div') || cb.closest('label');
                let labelText = parentBox ? parentBox.textContent.toLowerCase().replace(/[^a-z0-9]/g, '') : '';
                let nameAttr = (cb.name + ' ' + cb.id).toLowerCase().replace(/[^a-z0-9]/g, '');
                let combinedKey = labelText + nameAttr;

                if (combinedKey.includes('primaryphone') || (combinedKey.includes('phone') && !combinedKey.includes('alternate') && !combinedKey.includes('alt'))) {
                    cardWrapper.querySelectorAll('.show_primary_phone').forEach(el => { cb.checked ? el.classList.add('active-field') : el.classList.remove('active-field'); });
                } else if (combinedKey.includes('alternatephone') || combinedKey.includes('altphone')) {
                    cardWrapper.querySelectorAll('.show_alternate_phone').forEach(el => { cb.checked ? el.classList.add('active-field') : el.classList.remove('active-field'); });
                } else {
                    const mapping = [
                        { keys: ['business'], target: ['.show_business_name', '.show_business'] },
                        { keys: ['designation'], target: ['.show_designation'] },
                        { keys: ['tagline', 'motto'], target: ['.show_tagline', '.show_motto'] },
                        { keys: ['nickname'], target: ['.show_nickname'] },
                        { keys: ['photo', 'profile'], target: ['.show_photo', '.show_profile_photo'] },
                        { keys: ['whatsapp'], target: ['.show_whatsapp_number', '.show_whatsapp'] },
                        { keys: ['telegram'], target: ['.show_telegram'] },
                        { keys: ['gmail', 'primaryemail'], target: ['.show_gmail_primary_email', '.show_gmail'] },
                        { keys: ['yahoo'], target: ['.show_yahoo_email'] },
                        { keys: ['otheremail'], target: ['.show_other_email'] },
                        { keys: ['website'], target: ['.show_website_url', '.show_website'] },
                        { keys: ['facebook'], target: ['.show_facebook'] },
                        { keys: ['instagram'], target: ['.show_instagram'] },
                        { keys: ['linkedin'], target: ['.show_linkedin'] },
                        { keys: ['youtube'], target: ['.show_youtube_channel', '.show_youtube'] },
                        { keys: ['upi'], target: ['.show_upi_id'] },
                        { keys: ['gpay', 'googlepay'], target: ['.show_google_pay'] },
                        { keys: ['paytm'], target: ['.show_paytm'] },
                        { keys: ['qr', 'qrcode'], target: ['.show_qr_code', '.show_qr_code_image'] },
                        { keys: ['streetaddress', 'address'], target: ['.show_street_address', '.show_address'] },
                        { keys: ['area', 'colony'], target: ['.show_area_colony', '.show_area'] },
                        { keys: ['city', 'district'], target: ['.show_city_district', '.show_city'] },
                        { keys: ['state'], target: ['.show_state'] },
                        { keys: ['pincode'], target: ['.show_pincode'] },
                        { keys: ['googlemaps', 'maps', 'location'], target: ['.show_google_maps_link', '.show_location_url'] }
                    ];

                    mapping.forEach(item => {
                        if (item.keys.some(k => combinedKey.includes(k))) {
                            item.target.forEach(selector => {
                                cardWrapper.querySelectorAll(selector).forEach(el => { cb.checked ? el.classList.add('active-field') : el.classList.remove('active-field'); });
                            });
                        }
                    });
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', applyCardRendering);
    document.body.addEventListener('input', applyCardRendering);
    document.body.addEventListener('change', applyCardRendering);
    document.body.addEventListener('click', applyCardRendering);
    applyCardRendering();
})();
</script>