@php
    $textColor = $cardView->custom_text_color ?? request('custom_text_color') ?? '#ffffff';
    $iconColor = $cardView->custom_icon_color ?? request('custom_icon_color') ?? '#a3e635';
    $fontFamily = $cardView->font_family ?? request('font_family') ?? "'Poppins', sans-serif";
    $displayMode = $cardView->display_mode ?? request('display_mode') ?? 'icon_text';
    
    $toggles = isset($cardView->field_toggles) 
        ? (is_array($cardView->field_toggles) ? $cardView->field_toggles : json_decode($cardView->field_toggles, true)) 
        : [];
@endphp

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Montserrat:wght@400;600;700&family=Outfit:wght@400;600;700&family=Poppins:wght@400;600;700&family=Roboto:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    .digital-card-container {
        --card-text-color: {{ $textColor }};
        --card-icon-color: {{ $iconColor }};
        --card-font-family: {!! $fontFamily !!};

        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        font-family: var(--card-font-family);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
        color: var(--card-text-color) !important;
        width: 100%;
        box-sizing: border-box;
    }

    .digital-card-container h1, 
    .digital-card-container h2, 
    .digital-card-container h3, 
    .digital-card-container h4, 
    .digital-card-container h5, 
    .digital-card-container h6,
    .digital-card-container p, 
    .digital-card-container span, 
    .digital-card-container div {
        color: var(--card-text-color) !important;
    }

    .card-icon-accent {
        color: var(--card-icon-color) !important;
        fill: var(--card-icon-color) !important;
    }

    .action-pill-btn {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 30px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        backdrop-filter: blur(5px);
        transition: all 0.2s ease;
    }

    .social-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border: 1px solid rgba(255,255,255,0.15);
    }
</style>

<div class="digital-card-container" data-display-mode="{{ $displayMode }}">
    
    <!-- Header Top Row -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="preview-field-nickname {{ empty($toggles['nickname']) ? 'd-none' : '' }}">
            <span class="badge bg-white bg-opacity-10 rounded-pill px-3 py-1 text-lowercase" style="font-size: 11px;">
                (<span id="preview_text_nickname">{{ $masterCard->nickname ?? '' }}</span>)
            </span>
        </div>
        <div class="d-flex gap-2 ms-auto">
            <span class="social-icon-btn"><i class="fa-solid fa-qrcode card-icon-accent"></i></span>
            <span class="social-icon-btn"><i class="fa-solid fa-share-nodes card-icon-accent"></i></span>
        </div>
    </div>

    <!-- Main Profile Info -->
    <div class="mb-3">
        <!-- Business Name: Always Visible -->
        <h3 class="fw-bold mb-1" style="font-size: 20px; letter-spacing: -0.5px;" id="preview_text_business_name">
            {{ $masterCard->business_name ?? 'Business Name' }}
        </h3>

        <!-- Tagline -->
        <div class="preview-field-tagline {{ empty($toggles['tagline']) ? 'd-none' : '' }}">
            <p class="small mb-2 opacity-75" style="font-size: 12px;" id="preview_text_tagline">{{ $masterCard->tagline ?? '' }}</p>
        </div>

        <!-- Full Name -->
        <div class="preview-field-full_name {{ empty($toggles['full_name']) ? 'd-none' : '' }}">
            <div class="fw-semibold mt-2" style="font-size: 14px;" id="preview_text_full_name">{{ $masterCard->full_name ?? '' }}</div>
        </div>

        <!-- Designation -->
        <div class="preview-field-designation {{ empty($toggles['designation']) ? 'd-none' : '' }}">
            <div class="small opacity-75" style="font-size: 12px;" id="preview_text_designation">{{ $masterCard->designation ?? '' }}</div>
        </div>
    </div>

    <!-- Contact Pills -->
    <div class="d-flex flex-wrap gap-2 my-3">
        
        <!-- Primary Phone -->
        <div class="action-pill-btn preview-field-primary_phone {{ empty($toggles['primary_phone']) ? 'd-none' : '' }}">
            <i class="fa-solid fa-phone card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}" id="preview_text_primary_phone">{{ $masterCard->primary_phone ?? 'Phone' }}</span>
        </div>

        <!-- Secondary Phone -->
        <div class="action-pill-btn preview-field-secondary_phone {{ empty($toggles['secondary_phone']) ? 'd-none' : '' }}">
            <i class="fa-solid fa-mobile-screen card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}" id="preview_text_secondary_phone">{{ $masterCard->secondary_phone ?? 'Phone 2' }}</span>
        </div>

        <!-- WhatsApp -->
        <div class="action-pill-btn preview-field-whatsapp_no {{ empty($toggles['whatsapp_no']) ? 'd-none' : '' }}">
            <i class="fa-brands fa-whatsapp card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}" id="preview_text_whatsapp_no">WhatsApp</span>
        </div>

        <!-- Email -->
        <div class="action-pill-btn preview-field-email {{ empty($toggles['email']) ? 'd-none' : '' }}">
            <i class="fa-solid fa-envelope card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}" id="preview_text_email">Email</span>
        </div>

        <!-- Website -->
        <div class="action-pill-btn preview-field-website_url {{ empty($toggles['website_url']) ? 'd-none' : '' }}">
            <i class="fa-solid fa-globe card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}" id="preview_text_website_url">Website</span>
        </div>

    </div>

    <!-- Social Media Buttons -->
    <div class="d-flex flex-wrap gap-2 my-3">
        <div class="action-pill-btn preview-field-facebook {{ empty($toggles['facebook']) ? 'd-none' : '' }}">
            <i class="fa-brands fa-facebook-f card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}">Facebook</span>
        </div>

        <div class="action-pill-btn preview-field-instagram {{ empty($toggles['instagram']) ? 'd-none' : '' }}">
            <i class="fa-brands fa-instagram card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}">Instagram</span>
        </div>

        <div class="action-pill-btn preview-field-linkedin {{ empty($toggles['linkedin']) ? 'd-none' : '' }}">
            <i class="fa-brands fa-linkedin-in card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}">LinkedIn</span>
        </div>

        <div class="action-pill-btn preview-field-youtube {{ empty($toggles['youtube']) ? 'd-none' : '' }}">
            <i class="fa-brands fa-youtube card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}">YouTube</span>
        </div>

        <div class="action-pill-btn preview-field-telegram {{ empty($toggles['telegram']) ? 'd-none' : '' }}">
            <i class="fa-brands fa-telegram card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}">Telegram</span>
        </div>

        <div class="action-pill-btn preview-field-twitter {{ empty($toggles['twitter']) ? 'd-none' : '' }}">
            <i class="fa-brands fa-x-twitter card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}">Twitter</span>
        </div>
    </div>

    <!-- Full Address Section -->
    <div class="preview-field-address {{ empty($toggles['address']) ? 'd-none' : '' }}">
        <div class="pt-2 mt-2 border-top border-white border-opacity-10 small opacity-75" style="font-size: 11px;">
            <i class="fa-solid fa-location-dot card-icon-accent me-1"></i>
            <span id="preview_text_street_address">{{ $masterCard->street_address ?? '' }}</span> 
            <span id="preview_text_city">{{ $masterCard->city ?? '' }}</span> 
            <span id="preview_text_state">{{ $masterCard->state ?? '' }}</span> 
            <span id="preview_text_pincode">{{ $masterCard->pincode ?? '' }}</span>
        </div>
    </div>

    <!-- UPI Details Section -->
    <div class="preview-field-upi_id {{ empty($toggles['upi_id']) ? 'd-none' : '' }}">
        <div class="action-pill-btn mt-2">
            <i class="fa-solid fa-qrcode card-icon-accent pill-icon {{ $displayMode === 'text_only' ? 'd-none' : '' }}"></i>
            <span class="pill-text {{ $displayMode === 'icon_only' ? 'd-none' : '' }}">UPI: <span id="preview_text_upi_id">{{ $masterCard->upi_id ?? '' }}</span></span>
        </div>
    </div>

    <!-- Card Footer -->
    <div class="mt-3 pt-2 d-flex justify-content-between align-items-center border-top border-white border-opacity-10" style="font-size: 10px;">
        <span class="opacity-50">12891-888000001-V1</span>
        <span class="fw-bold opacity-75">Powered by Tidong*</span>
    </div>
</div>