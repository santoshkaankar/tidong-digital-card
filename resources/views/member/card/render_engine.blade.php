@php
    // Database se saved theme uthayi ja rahi hai taaki list page par sahi theme render ho
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
    
    $show = function($key) use ($toggles) {
        if ($key === 'show_name') return true;
        if (array_key_exists($key, $toggles)) {
            return (bool)$toggles[$key];
        }
        return false; 
    };
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

<div class="card-material-wrapper auto-fit-card theme-{{ $themeStyle }} position-relative rounded-4 p-3 d-flex flex-column justify-content-between shadow-lg" style="overflow: hidden;">

    <div class="d-flex justify-content-between align-items-start w-100">
       <div style="padding-left: 0 !important; margin-left: 0 !important;">
           
           <div style="margin-bottom: 2px; margin-left: 0 !important;">
               <a href="https://tidong.in" target="_blank" class="text-decoration-none fw-bold text-truncate card-main-text" style="font-size: 1.1rem; line-height: 1.2; display: inline-block;">
                   {{ $masterCard->name ?? 'Card Holder Name' }}
               </a>
               
               @if(!empty($masterCard->nickname))
                   <span class="text-warning fst-italic show_nickname f-item {{ $show('show_nickname') ? 'active-field' : '' }}" style="font-size: 0.8rem; margin-left: 4px;">
                       ({{ $masterCard->nickname }})
                   </span>
               @endif
           </div>
           
           @if(!empty($masterCard->business_name))
               <div class="show_business_name f-item {{ $show('show_business_name') ? 'active-field' : '' }}" style="margin-bottom: 2px; margin-left: 0 !important;">
                   <p class="mb-0 fw-semibold text-info text-start" style="font-size: 0.8rem; line-height: 1.1; margin-left: 0 !important;">
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

        <div class="d-flex justify-content-between align-items-end border-top border-secondary pt-1">
            <a href="https://tidong.in" target="_blank" class="font-monospace text-warning text-decoration-none" style="font-size: 0.8rem;">{{ $fullCardNo }}</a>
            <a href="https://tidong.in" target="_blank" class="text-decoration-none fst-italic fw-semibold card-sub-text" style="font-size: 0.8rem; opacity: 0.95;">Powered by Tidong</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Real-time sync for checkboxes during creation/editing
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

    // Theme apply karne ka common function
    function applyThemeToCard(cardWrapper, val) {
        let bgStyle = 'linear-gradient(135deg, #1e293b, #0f172a)';
        let textColor = '#ffffff';
        let secondaryTextColor = '#cbd5e1';
        
        const isLight = (val === 'classic-white' || val.includes('paper') || val.includes('light') || val.includes('vintage') || val.includes('metal-gold'));

        if(isLight) {
            if(val.includes('metal-gold')) {
                bgStyle = 'linear-gradient(135deg, #d4af37, #aa771c, #f3e5ab)';
                textColor = '#111111';
                secondaryTextColor = '#333333';
            } else {
                bgStyle = '#ffffff';
                textColor = '#111111';
                secondaryTextColor = '#555555';
            }
        } else if(val.includes('wood')) {
            bgStyle = 'linear-gradient(135deg, #5c4033, #3b2219)';
            textColor = '#ffffff';
            secondaryTextColor = '#e2e8f0';
        } else if(val.includes('metal-silver')) {
            bgStyle = 'linear-gradient(135deg, #bdc3c7, #2c3e50)';
            textColor = '#ffffff';
            secondaryTextColor = '#e2e8f0';
        }

        cardWrapper.style.setProperty('background', bgStyle, 'important');
        cardWrapper.style.setProperty('color', textColor, 'important');

        cardWrapper.querySelectorAll('.card-main-text').forEach(el => {
            el.style.setProperty('color', textColor, 'important');
        });
        cardWrapper.querySelectorAll('.card-sub-text').forEach(el => {
            el.style.setProperty('color', secondaryTextColor, 'important');
        });
    }

    // 2. List page ke sabhi cards par unki saved theme apply karna
    const cardWrappers = document.querySelectorAll('.card-material-wrapper');
    cardWrappers.forEach(cardWrapper => {
        let classList = cardWrapper.className.split(' ');
        let themeClass = classList.find(cls => cls.startsWith('theme-'));
        let val = themeClass ? themeClass.replace('theme-', '') : 'default';
        applyThemeToCard(cardWrapper, val);
    });

    // 3. Create/Edit page ke live preview dropdown ko fix karna
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

    // Agar preview iframe ke andar hi select ho toh uske liye bhi
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