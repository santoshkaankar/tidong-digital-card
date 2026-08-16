@php
    $themeStyle = $themeStyle ?? 'default';
    $fullCardNo = $fullCardNo ?? ($masterCard->card_no ?? '12091-080000001-A1');
    
    // Convert fieldToggles to an array if it's a JSON string or object
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
    
    // Helper function to check if a field should be shown
    $show = function($key) use ($toggles) {
        if ($key === 'show_name') return true;
        
        if (array_key_exists($key, $toggles)) {
            return (bool)$toggles[$key];
        }
        
        return false; 
    };
@endphp

<style>
    .card-material-wrapper {
        color: var(--theme-text-color, #ffffff) !important;
    }
    .card-material-wrapper a {
        color: var(--theme-text-color, #ffffff);
    }
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

<!-- Card Container -->
<div class="card-material-wrapper auto-fit-card theme-{{ $themeStyle }} position-relative rounded-4 p-3 text-white d-flex flex-column justify-content-between shadow-lg" style="background: var(--card-bg, linear-gradient(135deg, #1e293b, #0f172a)); overflow: hidden;">

    <!-- Top Section: Details on Left, QR Code & Profile Photo on Right -->
    <div class="d-flex justify-content-between align-items-start w-100">
        
       <!-- Left Side Details - Exact Left Aligned -->
       <div style="padding-left: 0 !important; margin-left: 0 !important;">
           
           <!-- 1. Name & Nickname -->
           <div style="margin-bottom: 2px; margin-left: 0 !important;">
               <a href="https://tidong.in" target="_blank" class="text-white text-decoration-none fw-bold text-truncate" style="font-size: 1.1rem; line-height: 1.2; display: inline-block;">
                   {{ $masterCard->name ?? 'Card Holder Name' }}
               </a>
               
               @if(!empty($masterCard->nickname))
                   <span class="text-warning fst-italic show_nickname f-item {{ $show('show_nickname') ? 'active-field' : '' }}" style="font-size: 0.8rem; margin-left: 4px;">
                       ({{ $masterCard->nickname }})
                   </span>
               @endif
           </div>
           
           <!-- 2. Business / Profession Name -->
           @if(!empty($masterCard->business_name))
               <div class="show_business_name f-item {{ $show('show_business_name') ? 'active-field' : '' }}" style="margin-bottom: 2px; margin-left: 0 !important;">
                   <p class="mb-0 fw-semibold text-info text-start" style="font-size: 0.8rem; line-height: 1.1; margin-left: 0 !important;">
                       {{ $masterCard->business_name }}
                   </p>
               </div>
           @endif
           
           <!-- 3. Designation / Post -->
           @if(!empty($masterCard->designation))
               <div class="show_designation f-item {{ $show('show_designation') ? 'active-field' : '' }}" style="margin-bottom: 2px; margin-left: 0 !important;">
                   <p class="mb-0 text-light opacity-75 text-start" style="font-size: 0.7rem; line-height: 1.1; margin-left: 0 !important;">
                       {{ $masterCard->designation }}
                   </p>
               </div>
           @endif

           <!-- 4. Tagline / Slogan -->
           @if(!empty($masterCard->tagline))
               <div class="show_tagline f-item {{ $show('show_tagline') ? 'active-field' : '' }}" style="margin-left: 0 !important;">
                   <p class="mb-0 text-light opacity-50 text-start" style="font-size: 0.65rem; line-height: 1.1; margin-left: 0 !important;">
                       {{ $masterCard->tagline }}
                   </p>
               </div>
           @endif

       </div>

        <!-- Right Side: QR Code First, then Profile Photo -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <!-- QR Code First -->
            <div class="show_qr_code f-item {{ $show('show_qr_code') ? 'active-field' : '' }}">
                @if(!empty($masterCard->qr_code))
                    <img src="{{ asset($masterCard->qr_code) }}" alt="QR" style="width: 42px; height: 42px; object-fit: cover;" class="rounded bg-white p-0.5 shadow-sm">
                @else
                    <div class="bg-white rounded p-1 text-dark d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                        <i class="fa-solid fa-qrcode" style="font-size: 28px;"></i>
                    </div>
                @endif
            </div>

            <!-- Profile Photo Second -->
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

    <!-- Middle Section: Icons -->
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
            <a href="{{ $masterCard->upi_link ?? 'upi://pay?pa=' . ($masterCard->upi_id ?? '') }}" target="_blank" class="text-dark bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm text-decoration-none" style="width: 34px; height: 34px;"><i class="fa-solid fa-wallet" style="font-size: 0.95rem;"></i></a>
        </div>
        
        <div class="show_gpay f-item {{ $show('show_gpay') ? 'active-field' : '' }}" title="Google Pay">
            <a href="{{ $masterCard->gpay_link ?? 'tez://upi/pay?pa=' . ($masterCard->gpay_id ?? '') }}" target="_blank" class="text-dark bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm text-decoration-none" style="width: 34px; height: 34px;"><i class="fa-solid fa-g" style="font-size: 0.95rem;"></i></a>
        </div>
        
        <div class="show_paytm f-item {{ $show('show_paytm') ? 'active-field' : '' }}" title="Paytm">
            <a href="{{ $masterCard->paytm_link ?? 'paytmmp://pay?pa=' . ($masterCard->paytm_id ?? '') }}" target="_blank" class="text-info bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm text-decoration-none" style="width: 34px; height: 34px;"><i class="fa-solid fa-p" style="font-size: 0.95rem;"></i></a>
        </div>
    </div>

    <!-- Bottom Section: Address & Powered by Tidong -->
    <div>
        <!-- Address & Map logo -->
        <div class="text-light opacity-75 mb-1 d-flex align-items-start" style="font-size: 0.65rem; line-height: 1.25; max-height: 2.8em; overflow: hidden;">
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

        <!-- Card Line, Card No & Powered by Tidong -->
        <div class="d-flex justify-content-between align-items-end border-top border-secondary pt-1">
            <a href="https://tidong.in" target="_blank" class="font-monospace text-warning text-decoration-none" style="font-size: 0.8rem;">{{ $fullCardNo }}</a>
            <a href="https://tidong.in" target="_blank" class="text-light text-decoration-none fst-italic fw-semibold" style="font-size: 0.8rem; opacity: 0.95;">Powered by Tidong</a>
        </div>
    </div>
</div>




<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Checkboxes toggle handler
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

    // 2. Force Theme & Text Color Fixer with !important
    const cardWrapper = document.querySelector('.card-material-wrapper');
    
    if (cardWrapper) {
        function applyTheme(val) {
            if (!val) return;
            
            // Remove previous theme classes
            cardWrapper.className = cardWrapper.className.split(' ').filter(cls => !cls.startsWith('theme-')).join(' ');
            cardWrapper.classList.add('theme-' + val);
            
            let bgStyle = 'linear-gradient(135deg, #1e293b, #0f172a)';
            let textColor = '#ffffff';
            let secondaryTextColor = 'rgba(255, 255, 255, 0.9)';
            
            // Determine colors based on selected theme type
            if(val === 'classic-white' || val.includes('paper') || val.includes('light') || val.includes('vintage')) {
                bgStyle = '#fdfbf7';
                textColor = '#111111';
                secondaryTextColor = '#333333';
            } else if(val.includes('wood')) {
                bgStyle = 'linear-gradient(135deg, #5c4033, #3b2219)';
                textColor = '#ffffff';
                secondaryTextColor = 'rgba(255, 255, 255, 0.9)';
            } else if(val.includes('metal-gold')) {
                bgStyle = 'linear-gradient(135deg, #d4af37, #aa771c, #f3e5ab)';
                textColor = '#111111';
                secondaryTextColor = '#222222';
            } else if(val.includes('metal-silver')) {
                bgStyle = 'linear-gradient(135deg, #bdc3c7, #2c3e50)';
                textColor = '#ffffff';
                secondaryTextColor = 'rgba(255, 255, 255, 0.9)';
            } else if(val.includes('fabric')) {
                bgStyle = 'linear-gradient(135deg, #3b5998, #192f6a)';
                textColor = '#ffffff';
                secondaryTextColor = 'rgba(255, 255, 255, 0.9)';
            } else {
                bgStyle = 'linear-gradient(135deg, #1e293b, #0f172a)';
                textColor = '#ffffff';
                secondaryTextColor = 'rgba(255, 255, 255, 0.9)';
            }

            // Apply background and wrapper color usingsetProperty with 'important'
            cardWrapper.style.setProperty('background', bgStyle, 'important');
            cardWrapper.style.setProperty('color', textColor, 'important');

            // Forcefully apply text color to all text tags inside the wrapper
            const allElements = cardWrapper.querySelectorAll('*');
            allElements.forEach(el => {
                if (!el.classList.contains('text-warning') && 
                    !el.classList.contains('text-info') && 
                    !el.classList.contains('text-success') && 
                    !el.classList.contains('text-danger')) {
                    
                    if(['H1','H2','H3','H4','H5','H6','STRONG','B','A'].includes(el.tagName)) {
                        el.style.setProperty('color', textColor, 'important');
                    } else if (el.children.length === 0 || ['P', 'SPAN', 'LABEL', 'LI'].includes(el.tagName)) {
                        // Leaf elements or direct text holders get secondary color
                        el.style.setProperty('color', secondaryTextColor, 'important');
                    }
                }
            });
        }

        // Sync with parent window's theme dropdown
        function checkAndSyncTheme() {
            try {
                const themeSelect = parent.document.getElementById('theme_style');
                if (themeSelect && themeSelect.value) {
                    applyTheme(themeSelect.value);
                }
            } catch (e) {}
        }

        try {
            const themeSelect = parent.document.getElementById('theme_style');
            if (themeSelect) {
                themeSelect.addEventListener('change', function() {
                    applyTheme(this.value);
                });
                applyTheme(themeSelect.value);
            }
        } catch (e) {}

        // Polling to ensure real-time change synchronization inside iframe
        setInterval(checkAndSyncTheme, 300);
    }
});
</script>