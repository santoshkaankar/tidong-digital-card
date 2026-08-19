@php
    $themeStyle = $themeStyle ?? ($cardView->theme_style ?? 'default');
    $fullCardNo = $fullCardNo ?? ($cardView->full_card_no ?? ($masterCard->card_no ?? '12091-080000001-A1'));
    
    $toggles = [];
    if (isset($fieldToggles)) {
        if (is_string($fieldToggles)) {
            $toggles = json_decode($fieldToggles, true) ?? [];
        } elseif (is_array($fieldToggles)) {
            $toggles = $fieldToggles;
        } elseif (is_object($fieldToggles)) {
            $toggles = (array) $fieldToggles;
        }
    } elseif (isset($cardView->field_toggles)) {
        if (is_string($cardView->field_toggles)) {
            $toggles = json_decode($cardView->field_toggles, true) ?? [];
        } else {
            $toggles = (array) $cardView->field_toggles;
        }
    }
    
    $isSavedCardContext = empty($toggles) && isset($cardView);

    $show = function($key) use ($toggles, $isSavedCardContext) {
        if ($key === 'show_name') return true;
        if ($isSavedCardContext) return true;
        if (array_key_exists($key, $toggles)) {
            return (bool)$toggles[$key];
        }
        return false; 
    };

    $themeVal = strtolower($themeStyle);
    $cardBg = 'linear-gradient(135deg, #1e293b 0%, #0f172a 100%)';
    $cardColor = '#ffffff';

    if (str_contains($themeVal, 'gold') || str_contains($themeVal, 'metal-gold') || str_contains($themeVal, 'rose-gold')) {
        $cardBg = 'linear-gradient(135deg, #bf953f 0%, #fcf6ba 25%, #b38728 50%, #fbf5b7 75%, #aa771c 100%)';
        $cardColor = '#3d2c04';
    } elseif (str_contains($themeVal, 'paper') || str_contains($themeVal, 'parchment') || str_contains($themeVal, 'torn')) {
        $cardBg = '#f4ebd0';
        $cardColor = '#4a3b32';
    } elseif (str_contains($themeVal, 'jeans') || str_contains($themeVal, 'denim')) {
        $cardBg = 'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)';
        $cardColor = '#ffffff';
    } elseif (str_contains($themeVal, 'white') || str_contains($themeVal, 'classic-white')) {
        $cardBg = '#ffffff';
        $cardColor = '#0f172a';
    } elseif (str_contains($themeVal, 'dark') || str_contains($themeVal, 'obsidian') || str_contains($themeVal, 'classic-dark')) {
        $cardBg = '#000000';
        $cardColor = '#f1f5f9';
    } elseif (str_contains($themeVal, 'sunset')) {
        $cardBg = 'linear-gradient(135deg, #f857a6 0%, #ff5858 100%)';
        $cardColor = '#ffffff';
    } elseif (str_contains($themeVal, 'ocean')) {
        $cardBg = 'linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%)';
        $cardColor = '#ffffff';
    } elseif (str_contains($themeVal, 'glass') || str_contains($themeVal, 'crystal')) {
        $cardBg = 'rgba(255, 255, 255, 0.25)';
        $cardColor = '#0f172a';
    }
@endphp

<style>
    .card-material-wrapper .f-item {
        display: none !important;
    }
    .card-material-wrapper .f-item.active-field {
        display: block !important;
    }
    .card-material-wrapper span.f-item.active-field {
        display: inline !important;
    }
    .card-material-wrapper div.f-item.active-field {
        display: block !important;
    }
    .auto-fit-card {
        width: 100%;
        max-width: 410px;
        min-height: 240px;
        aspect-ratio: 1.6 / 1;
    }
</style>

<div class="card-material-wrapper auto-fit-card theme-{{ $themeStyle }} position-relative rounded-4 p-3 d-flex flex-column justify-content-between shadow-lg" style="overflow: hidden; background: {{ $cardBg }}; color: {{ $cardColor }};">

    <div class="d-flex justify-content-between align-items-start w-100">
       <div style="padding-left: 0 !important; margin-left: 0 !important;">
           
           <div style="margin-bottom: 2px; margin-left: 0 !important;">
               <a href="https://tidong.in" target="_blank" class="text-decoration-none fw-bold text-truncate card-main-text" style="font-size: 1.1rem; line-height: 1.2; display: inline-block; color: inherit;">
                   {{ $masterCard->name ?? 'Card Holder Name' }}
               </a>
               
               @if(!empty($masterCard->nickname))
                   <span class="fst-italic show_nickname f-item {{ $show('show_nickname') ? 'active-field' : '' }}" style="font-size: 0.8rem; margin-left: 4px; opacity: 0.85;">
                       ({{ $masterCard->nickname }})
                   </span>
               @endif
           </div>
           
           @if(!empty($masterCard->business_name))
               <div class="show_business_name f-item {{ $show('show_business_name') ? 'active-field' : '' }}" style="margin-bottom: 2px; margin-left: 0 !important;">
                   <p class="mb-0 fw-semibold text-start" style="font-size: 0.8rem; line-height: 1.1; margin-left: 0 !important; opacity: 0.9;">
                       {{ $masterCard->business_name }}
                   </p>
               </div>
           @endif
           
           @if(!empty($masterCard->designation))
               <div class="show_designation f-item {{ $show('show_designation') ? 'active-field' : '' }}" style="margin-bottom: 2px; margin-left: 0 !important;">
                   <p class="mb-0 opacity-75 text-start card-sub-text" style="font-size: 0.7rem; line-height: 1.1; margin-left: 0 !important;">
                       {{ $masterCard->designation }}
                   </p>
               </div>
           @endif

           @if(!empty($masterCard->tagline))
               <div class="show_tagline f-item {{ $show('show_tagline') ? 'active-field' : '' }}" style="margin-left: 0 !important;">
                   <p class="mb-0 opacity-50 text-start card-sub-text" style="font-size: 0.65rem; line-height: 1.1; margin-left: 0 !important;">
                       {{ $masterCard->tagline }}
                   </p>
               </div>
           @endif

       </div>

        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <div class="show_qr_code f-item {{ $show('show_qr_code') ? 'active-field' : '' }}">
                @if(!empty($masterCard->qr_code))
                    <img src="{{ asset($masterCard->qr_code) }}" alt="QR" style="width: 42px; height: 42px; object-fit: cover;" class="rounded bg-white p-0.5 shadow-sm">
                @else
                    <div class="bg-white rounded p-1 text-dark d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-qrcode" style="font-size: 28px;"></i>
                    </div>
                @endif
            </div>

            <div class="show_photo f-item {{ $show('show_photo') ? 'active-field' : '' }}">
                @if(!empty($masterCard->photo))
                    <img src="{{ asset($masterCard->photo) }}" alt="Photo" class="rounded-circle border border-2 border-light shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold border border-2 border-light" style="width: 42px; height: 42px; font-size: 0.95rem;">
                        {{ strtoupper(substr($masterCard->name ?? 'T', 0, 1)) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 align-items-center my-1" style="max-height: 82px; overflow: hidden;">
        <div class="show_phone f-item {{ $show('show_phone') ? 'active-field' : '' }}" title="Phone">
            <a href="tel:{{ $masterCard->phone }}" class="text-success bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-solid fa-phone-alt" style="font-size: 0.95rem;"></i></a>
        </div>
        <div class="show_alt_phone f-item {{ $show('show_alt_phone') ? 'active-field' : '' }}" title="Alternate Phone">
            <a href="tel:{{ $masterCard->alt_phone }}" class="text-success bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-solid fa-phone" style="font-size: 0.95rem;"></i></a>
        </div>
        <div class="show_whatsapp f-item {{ $show('show_whatsapp') ? 'active-field' : '' }}" title="WhatsApp">
            <a href="https://wa.me/{{ $masterCard->whatsapp }}" target="_blank" class="text-success bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-brands fa-whatsapp" style="font-size: 1.05rem;"></i></a>
        </div>
        <div class="show_telegram f-item {{ $show('show_telegram') ? 'active-field' : '' }}" title="Telegram">
            <a href="#" class="text-info bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-brands fa-telegram" style="font-size: 1.05rem;"></i></a>
        </div>
        <div class="show_gmail f-item {{ $show('show_gmail') ? 'active-field' : '' }}" title="Email">
            <a href="mailto:{{ $masterCard->gmail }}" class="text-danger bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-solid fa-envelope" style="font-size: 0.95rem;"></i></a>
        </div>
        <div class="show_yahoo_email f-item {{ $show('show_yahoo_email') ? 'active-field' : '' }}" title="Yahoo Email">
            <a href="#" class="text-primary bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-solid fa-envelope" style="font-size: 0.95rem;"></i></a>
        </div>
        <div class="show_other_email f-item {{ $show('show_other_email') ? 'active-field' : '' }}" title="Other Email">
            <a href="#" class="text-warning bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-solid fa-envelope" style="font-size: 0.95rem;"></i></a>
        </div>
        <div class="show_website f-item {{ $show('show_website') ? 'active-field' : '' }}" title="Website">
            <a href="{{ $masterCard->website_link ?? '#' }}" target="_blank" class="text-info bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-solid fa-globe" style="font-size: 0.95rem;"></i></a>
        </div>
        <div class="show_facebook f-item {{ $show('show_facebook') ? 'active-field' : '' }}" title="Facebook">
            <a href="#" class="text-primary bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-brands fa-facebook" style="font-size: 1.05rem;"></i></a>
        </div>
        <div class="show_instagram f-item {{ $show('show_instagram') ? 'active-field' : '' }}" title="Instagram">
            <a href="#" class="text-danger bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-brands fa-instagram" style="font-size: 1.05rem;"></i></a>
        </div>
        <div class="show_linkedin f-item {{ $show('show_linkedin') ? 'active-field' : '' }}" title="LinkedIn">
            <a href="#" class="text-info bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-brands fa-linkedin" style="font-size: 1.05rem;"></i></a>
        </div>
        <div class="show_youtube f-item {{ $show('show_youtube') ? 'active-field' : '' }}" title="YouTube">
            <a href="#" class="text-danger bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 34px; height: 34px;"><i class="fa-brands fa-youtube" style="font-size: 1.05rem;"></i></a>
        </div>
        <div class="show_upi_id f-item {{ $show('show_upi_id') ? 'active-field' : '' }}" title="UPI ID">
            <a href="{{ $masterCard->upi_link ?? '#' }}" target="_blank" class="text-dark bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm text-decoration-none" style="width: 34px; height: 34px;"><i class="fa-solid fa-wallet" style="font-size: 0.95rem;"></i></a>
        </div>
        <div class="show_gpay f-item {{ $show('show_gpay') ? 'active-field' : '' }}" title="Google Pay">
            <a href="{{ $masterCard->gpay_link ?? '#' }}" target="_blank" class="text-dark bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm text-decoration-none" style="width: 34px; height: 34px;"><i class="fa-solid fa-g" style="font-size: 0.95rem;"></i></a>
        </div>
        <div class="show_paytm f-item {{ $show('show_paytm') ? 'active-field' : '' }}" title="Paytm">
            <a href="{{ $masterCard->paytm_link ?? '#' }}" target="_blank" class="text-info bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm text-decoration-none" style="width: 34px; height: 34px;"><i class="fa-solid fa-p" style="font-size: 0.95rem;"></i></a>
        </div>
    </div>

    <div>
        <div class="opacity-75 mb-1 d-flex align-items-start card-sub-text" style="font-size: 0.65rem; line-height: 1.25; max-height: 2.8em; overflow: hidden;">
            <div class="show_location_url f-item me-1 mt-0.5 {{ $show('show_location_url') ? 'active-field' : '' }}">
                <a href="{{ $masterCard->map_location_link ?? '#' }}" target="_blank" class="text-warning text-decoration-none">
                    <i class="fa-solid fa-map-location-dot" style="font-size: 0.75rem;"></i>
                </a>
            </div>
            <div>
                <span class="show_address f-item {{ $show('show_address') ? 'active-field' : '' }}">{{ $masterCard->address ?? '' }}</span>
                <span class="show_area f-item {{ $show('show_area') ? 'active-field' : '' }}">{{ $masterCard->area ? ', ' . $masterCard->area : '' }}</span>
                <span class="show_city f-item {{ $show('show_city') ? 'active-field' : '' }}">{{ $masterCard->city ? ', ' . $masterCard->city : '' }}</span>
                <span class="show_state f-item {{ $show('show_state') ? 'active-field' : '' }}">{{ $masterCard->state ? ', ' . $masterCard->state : '' }}</span>
                <span class="show_pincode f-item {{ $show('show_pincode') ? 'active-field' : '' }}">{{ $masterCard->pincode ? ' - ' . $masterCard->pincode : '' }}</span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-end border-top pt-1" style="border-color: rgba(255,255,255,0.2) !important;">
            <a href="https://tidong.in" target="_blank" class="font-monospace text-decoration-none" style="font-size: 0.8rem; color: inherit; opacity: 0.9;">{{ $fullCardNo }}</a>
            <a href="https://tidong.in" target="_blank" class="text-decoration-none fst-italic fw-semibold card-sub-text" style="font-size: 0.8rem; opacity: 0.95; color: inherit;">Powered by Tidong</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(chk => {
        chk.addEventListener('change', function () {
            let rawName = this.name;
            if (!rawName) return;
            let fieldClass = rawName.replace('toggles[', '').replace(']', '').replace(/['"]+/g, '');
            document.querySelectorAll('.' + fieldClass).forEach(el => {
                el.classList.toggle('active-field', this.checked);
            });
        });
    });

    function applyThemeToCard(cardWrapper, val) {
        let bgStyle = '';
        let textColor = '#ffffff';
        let secondaryTextColor = '#cbd5e1';

        // Check if theme is dynamic vibrant or royal
        if (val.startsWith('dyn-vibrant-')) {
            let id = parseInt(val.replace('dyn-vibrant-', ''));
            let hash1 = (id * 37) % 360;
            let hash2 = (id * 83) % 360;
            bgStyle = `linear-gradient(135deg, hsl(${hash1}, 85%, 50%), hsl(${hash2}, 90%, 35%))`;
            textColor = '#ffffff';
            secondaryTextColor = '#f1f5f9';
        } else if (val.startsWith('dyn-royal-')) {
            let id = parseInt(val.replace('dyn-royal-', ''));
            let hash1 = (id * 37) % 360;
            let hash2 = (id * 83) % 360;
            bgStyle = `linear-gradient(135deg, hsl(${hash1}, 65%, 88%), hsl(${hash2}, 75%, 70%))`;
            textColor = '#0f172a';
            secondaryTextColor = '#334155';
        } else if (val === 'classic-white') {
            bgStyle = '#ffffff';
            textColor = '#111111';
            secondaryTextColor = '#555555';
        } else if (val === 'classic-dark' || val === 'default') {
            bgStyle = '#111827';
            textColor = '#ffffff';
            secondaryTextColor = '#9ca3af';
        } else if (val === 'classic-modern') {
            bgStyle = 'linear-gradient(135deg, #1e3a8a, #3b82f6)';
            textColor = '#ffffff';
            secondaryTextColor = '#93c5fd';
        } else if (val === 'metal-gold') {
            bgStyle = 'linear-gradient(135deg, #bf953f, #fcf6ba, #aa771c)';
            textColor = '#3d2c04';
            secondaryTextColor = '#5c4405';
        } else if (val === 'fabric-denim') {
            bgStyle = 'linear-gradient(135deg, #1e3c72, #2a5298)';
            textColor = '#ffffff';
            secondaryTextColor = '#e2e8f0';
        } 
        // --- Advanced CSS Patterns & Creative Textures ---
        else if (val === 'texture-old-wood') {
            bgStyle = 'linear-gradient(90deg, #3e2723 0%, #4e342e 50%, #3e2723 100%), repeating-linear-gradient(0deg, transparent, transparent 4px, rgba(0,0,0,0.3) 4px, rgba(0,0,0,0.3) 8px)';
            textColor = '#d7ccc8';
            secondaryTextColor = '#bcaaa4';
        } else if (val === 'texture-ripped-jeans') {
            bgStyle = 'linear-gradient(135deg, #1a237e 0%, #3949ab 100%), repeating-linear-gradient(45deg, rgba(255,255,255,0.07) 0px, rgba(255,255,255,0.07) 2px, transparent 2px, transparent 6px)';
            textColor = '#ffffff';
            secondaryTextColor = '#c5cae9';
        } else if (val === 'texture-fish-stones') {
            bgStyle = 'radial-gradient(circle at 30% 30%, #ff7043 0%, transparent 40%), radial-gradient(circle at 70% 70%, #26a69a 0%, transparent 50%), linear-gradient(135deg, #37474f, #263238)';
            textColor = '#ffffff';
            secondaryTextColor = '#e0f2f1';
        } else if (val === 'texture-torn-paper') {
            bgStyle = 'linear-gradient(135deg, #f9f9f9 0%, #eceff1 100%), repeating-linear-gradient(45deg, rgba(0,0,0,0.03) 0px, rgba(0,0,0,0.03) 2px, transparent 2px, transparent 4px)';
            textColor = '#212121';
            secondaryTextColor = '#555555';
        } else if (val === 'texture-spider-web') {
            bgStyle = 'radial-gradient(circle at center, #263238 0%, #0b1013 100%), repeating-radial-gradient(circle at center, transparent 0px, transparent 10px, rgba(255,255,255,0.05) 10px, rgba(255,255,255,0.05) 12px)';
            textColor = '#e0f7fa';
            secondaryTextColor = '#b2ebf2';
        } else if (val === 'texture-dusty-sand') {
            bgStyle = 'linear-gradient(135deg, #d7ccc8 0%, #a1887f 100%), radial-gradient(rgba(0,0,0,0.15) 15%, transparent 16%)';
            textColor = '#3e2723';
            secondaryTextColor = '#5d4037';
        } else if (val === 'texture-rusty-metal') {
            bgStyle = 'linear-gradient(135deg, #bf360c 0%, #4e342e 100%), repeating-linear-gradient(-45deg, rgba(0,0,0,0.3) 0px, rgba(0,0,0,0.3) 3px, transparent 3px, transparent 6px)';
            textColor = '#ffccbc';
            secondaryTextColor = '#ffab91';
        } else if (val === 'texture-neon-glow') {
            bgStyle = 'linear-gradient(135deg, #000428, #004e92)';
            textColor = '#00ffcc';
        } else if (val === 'texture-carbon-fiber') {
            bgStyle = 'radial-gradient(circle, #222 20%, #111 80%), repeating-linear-gradient(45deg, rgba(255,255,255,0.05) 0px, rgba(255,255,255,0.05) 2px, transparent 2px, transparent 4px)';
            textColor = '#ffffff';
        } else if (val === 'texture-marble-white') {
            bgStyle = 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%), radial-gradient(at 50% 50%, rgba(255,255,255,0.8) 0%, rgba(0,0,0,0.05) 100%)';
            textColor = '#2c3e50';
        } else if (val === 'texture-stained-glass') {
            bgStyle = 'linear-gradient(45deg, #ff9a9e, #fad0c4, #fad0c4, #a18cd1, #fbc2eb)';
            textColor = '#222222';
        } else if (val === 'texture-holographic') {
            bgStyle = 'linear-gradient(to right, #ff00ff, #00ffff, #ffff00, #ff00ff)';
            textColor = '#ffffff';
        } else if (val === 'texture-rose-gold') {
            bgStyle = 'linear-gradient(135deg, #b76e79, #e8b4b8, #d4af37)';
            textColor = '#ffffff';
        } else if (val === 'texture-midnight-velvet') {
            bgStyle = 'linear-gradient(135deg, #0f0c29, #302b63, #24243e)';
            textColor = '#e2e8f0';
        } else if (val === 'texture-emerald-silk') {
            bgStyle = 'linear-gradient(135deg, #0575e6, #00f2fe)';
            textColor = '#ffffff';
        } else if (val === 'texture-cyberpunk-grid') {
            bgStyle = 'linear-gradient(135deg, #f72585, #7209b7, #3a0ca3)';
            textColor = '#4cc9f0';
        } else if (val === 'texture-vintage-leather') {
            bgStyle = 'linear-gradient(135deg, #3e2723, #4e342e, #211512)';
            textColor = '#d7ccc8';
        } else if (val === 'texture-sunset-orange') {
            bgStyle = 'linear-gradient(135deg, #ff4e50, #f9d423)';
            textColor = '#222222';
        } else if (val === 'texture-deep-ocean') {
            bgStyle = 'linear-gradient(135deg, #2b5876, #4e4376)';
            textColor = '#ffffff';
        } else if (val === 'texture-royal-amethyst') {
            bgStyle = 'linear-gradient(135deg, #9d50bb, #6e48aa)';
            textColor = '#ffffff';
        } else if (val === 'texture-frost-glass') {
            bgStyle = 'linear-gradient(135deg, rgba(255,255,255,0.4), rgba(255,255,255,0.1))';
            textColor = '#111827';
        } else if (val === 'texture-matte-obsidian') {
            bgStyle = '#1a1a1a';
            textColor = '#a3a3a3';
        } else if (val === 'texture-liquid-chrome') {
            bgStyle = 'linear-gradient(135deg, #bdc3c7, #2c3e50)';
            textColor = '#ffffff';
        } else if (val === 'texture-plasma-energy') {
            bgStyle = 'linear-gradient(135deg, #ff0844, #ffb199)';
            textColor = '#ffffff';
        }

        if (bgStyle !== '') {
            cardWrapper.style.cssText += `; background: ${bgStyle} !important;`;
        }
        
        if (textColor !== '') {
            cardWrapper.style.setProperty('color', textColor, 'important');
            
            const textElements = cardWrapper.querySelectorAll('h1, h2, h3, h4, h5, h6, p, span, strong, small, .card-text, .name-title, .company-title');
            textElements.forEach(el => {
                el.style.setProperty('color', textColor, 'important');
            });
        }
    }

    const cardWrappers = document.querySelectorAll('.card-material-wrapper');
    
    cardWrappers.forEach(cardWrapper => {
        let classList = cardWrapper.className.split(' ');
        let themeClass = classList.find(cls => cls.startsWith('theme-'));
        let val = themeClass ? themeClass.replace('theme-', '') : 'default';
        applyThemeToCard(cardWrapper, val);
    });

    try {
        if (parent && parent.document) {
            const themeSelect = parent.document.getElementById('theme_style');
            if (themeSelect) {
                themeSelect.addEventListener('change', function() {
                    cardWrappers.forEach(cardWrapper => {
                        cardWrapper.className = cardWrapper.className.split(' ').filter(cls => !cls.startsWith('theme-')).join(' ');
                        cardWrapper.classList.add('theme-' + this.value);
                        applyThemeToCard(cardWrapper, this.value);
                    });
                });
            }
        }
    } catch(e) {}

    const localThemeSelect = document.getElementById('theme_style');
    if (localThemeSelect) {
        localThemeSelect.addEventListener('change', function() {
            cardWrappers.forEach(cardWrapper => {
                cardWrapper.className = cardWrapper.className.split(' ').filter(cls => !cls.startsWith('theme-')).join(' ');
                cardWrapper.classList.add('theme-' + this.value);
                applyThemeToCard(cardWrapper, this.value);
            });
        });
    }
});
</script>