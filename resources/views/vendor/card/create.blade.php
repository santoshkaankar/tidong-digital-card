@extends('vendor.card.layout')

@section('title', 'Configure Digital Card Studio')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Left Column: Master Form & View Designer Tabs -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <ul class="nav nav-pills card-header-pills" id="cardTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="master-tab" data-bs-toggle="tab" data-bs-target="#master-panel" type="button" role="tab">
                                <i class="fa-solid fa-sliders me-1"></i> 1. Master Profile Config
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="designer-tab" data-bs-toggle="tab" data-bs-target="#designer-panel" type="button" role="tab">
                                <i class="fa-solid fa-paint-brush me-1"></i> 2. Card View Designer
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="cardTabContent">
                        
                        <!-- TAB 1: MASTER CONFIG FORM (Pure Input Fields) -->
                        <div class="tab-pane fade show active" id="master-panel" role="tabpanel">
                            <form action="{{ route('vendor.card.master.save') }}" method="POST">
                                @csrf
                                
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-building me-2"></i>Business & Personal Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Business Name <span class="text-danger">*</span></label>
                                        <input type="text" name="business_name" id="input_business_name" class="form-control live-text-input" value="{{ $masterCard->business_name ?? 'Business Name' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Full Name</label>
                                        <input type="text" name="full_name" id="input_full_name" class="form-control live-text-input" value="{{ $masterCard->full_name ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nickname</label>
                                        <input type="text" name="nickname" id="input_nickname" class="form-control live-text-input" value="{{ $masterCard->nickname ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Designation</label>
                                        <input type="text" name="designation" id="input_designation" class="form-control live-text-input" value="{{ $masterCard->designation ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Company Name</label>
                                        <input type="text" name="company_name" id="input_company_name" class="form-control live-text-input" value="{{ $masterCard->company_name ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tagline / Motto</label>
                                        <input type="text" name="tagline" id="input_tagline" class="form-control live-text-input" value="{{ $masterCard->tagline ?? '' }}">
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-address-book me-2"></i>Contact Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Primary Phone</label>
                                        <input type="text" name="primary_phone" id="input_primary_phone" class="form-control live-text-input" value="{{ $masterCard->primary_phone ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Secondary Phone</label>
                                        <input type="text" name="secondary_phone" id="input_secondary_phone" class="form-control live-text-input" value="{{ $masterCard->secondary_phone ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">WhatsApp Number</label>
                                        <input type="text" name="whatsapp_no" id="input_whatsapp_no" class="form-control live-text-input" value="{{ $masterCard->whatsapp_no ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email Address</label>
                                        <input type="email" name="email" id="input_email" class="form-control live-text-input" value="{{ $masterCard->email ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Website URL</label>
                                        <input type="url" name="website_url" id="input_website_url" class="form-control live-text-input" value="{{ $masterCard->website_url ?? '' }}">
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-location-dot me-2"></i>Address Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Pincode</label>
                                        <input type="text" name="pincode" id="input_pincode" class="form-control live-text-input" value="{{ $masterCard->pincode ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">City</label>
                                        <input type="text" name="city" id="input_city" class="form-control live-text-input" value="{{ $masterCard->city ?? '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">State</label>
                                        <input type="text" name="state" id="input_state" class="form-control live-text-input" value="{{ $masterCard->state ?? '' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Street Address</label>
                                        <input type="text" name="street_address" id="input_street_address" class="form-control live-text-input" value="{{ $masterCard->street_address ?? '' }}">
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-share-nodes me-2"></i>Social & Payment</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Facebook Link</label>
                                        <input type="url" name="facebook" id="input_facebook" class="form-control live-text-input" value="{{ $masterCard->facebook ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Instagram Link</label>
                                        <input type="url" name="instagram" id="input_instagram" class="form-control live-text-input" value="{{ $masterCard->instagram ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">LinkedIn Link</label>
                                        <input type="url" name="linkedin" id="input_linkedin" class="form-control live-text-input" value="{{ $masterCard->linkedin ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">YouTube Link</label>
                                        <input type="url" name="youtube" id="input_youtube" class="form-control live-text-input" value="{{ $masterCard->youtube ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Telegram Link</label>
                                        <input type="url" name="telegram" id="input_telegram" class="form-control live-text-input" value="{{ $masterCard->telegram ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Twitter / X Link</label>
                                        <input type="url" name="twitter" id="input_twitter" class="form-control live-text-input" value="{{ $masterCard->twitter ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">UPI ID</label>
                                        <input type="text" name="upi_id" id="input_upi_id" class="form-control live-text-input" value="{{ $masterCard->upi_id ?? '' }}">
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-floppy-disk me-2"></i>Save Master Config</button>
                                </div>
                            </form>
                        </div>

                        <!-- TAB 2: CARD VIEW DESIGNER (Toggles, Display Mode & Styling) -->
                        <div class="tab-pane fade" id="designer-panel" role="tabpanel">
                            <form action="{{ route('vendor.card.view.save') }}" method="POST" id="cardViewForm">
                                @csrf
                                
                                <!-- Display Mode Option -->
                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-border-all me-2"></i>Button Display Mode</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <select name="display_mode" id="display_mode" class="form-select form-select-lg fw-bold border-primary live-style-control">
                                            <option value="icon_text" selected>Icon + Text (Default)</option>
                                            <option value="icon_only">Icon Only (Compact Buttons)</option>
                                            <option value="text_only">Text Only (No Icons)</option>
                                        </select>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-toggle-on me-2"></i>Field Visibility Switches</h6>
                                
                                <div class="row g-2 mb-4">
                                    <!-- Business Name: ALWAYS ON & DISABLED -->
                                    <div class="col-md-6">
                                        <div class="p-2 border rounded bg-light d-flex align-items-center justify-content-between">
                                            <span class="fw-bold text-dark">Business Name</span>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" checked disabled>
                                                <input type="hidden" name="field_toggles[business_name]" value="1">
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $toggleFields = [
                                            'full_name' => 'Full Name',
                                            'nickname' => 'Nickname',
                                            'designation' => 'Designation',
                                            'tagline' => 'Tagline / Motto',
                                            'primary_phone' => 'Primary Phone',
                                            'secondary_phone' => 'Secondary Phone',
                                            'whatsapp_no' => 'WhatsApp Number',
                                            'email' => 'Email Address',
                                            'website_url' => 'Website URL',
                                            'address' => 'Full Address',
                                            'facebook' => 'Facebook',
                                            'instagram' => 'Instagram',
                                            'linkedin' => 'LinkedIn',
                                            'youtube' => 'YouTube',
                                            'telegram' => 'Telegram',
                                            'twitter' => 'Twitter / X',
                                            'upi_id' => 'UPI Payment'
                                        ];
                                    @endphp

                                    @foreach($toggleFields as $key => $label)
                                        <div class="col-md-6">
                                            <div class="p-2 border rounded d-flex align-items-center justify-content-between">
                                                <span class="fw-semibold text-secondary">{{ $label }}</span>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input live-toggle-control" type="checkbox" name="field_toggles[{{ $key }}]" value="1" id="toggle_{{ $key }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-palette me-2"></i>Font, Colors & Theme</h6>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Font Family</label>
                                        <select name="font_family" id="font_family" class="form-select live-style-control">
                                            <option value="'Poppins', sans-serif">Poppins (Modern)</option>
                                            <option value="'Inter', sans-serif">Inter (Clean)</option>
                                            <option value="'Roboto', sans-serif">Roboto (Classic)</option>
                                            <option value="'Montserrat', sans-serif">Montserrat (Bold)</option>
                                            <option value="'Outfit', sans-serif">Outfit (Stylish)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Card Theme Background</label>
                                        <select name="theme_style" id="theme_style" class="form-select live-style-control">
                                            <option value="dark">Dark Obsidian (#0f172a)</option>
                                            <option value="royal">Royal Blue & Purple</option>
                                            <option value="emerald">Emerald Gradient</option>
                                            <option value="midnight">Midnight Black (#000000)</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Text Color</label>
                                        <input type="color" name="custom_text_color" id="custom_text_color" class="form-control form-control-color w-100 live-style-control" value="#ffffff">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Icon & Accent Color</label>
                                        <input type="color" name="custom_icon_color" id="custom_icon_color" class="form-control form-control-color w-100 live-style-control" value="#a3e635">
                                    </div>
                                </div>

                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-success px-4 py-2"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>Generate Card View</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Live Render Preview -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-bold"><i class="fa-solid fa-eye me-2"></i>Live Render Preview</span>
                    <span class="badge bg-success">Realtime Sync</span>
                </div>
                <div class="card-body p-3 bg-secondary bg-opacity-10 d-flex justify-content-center align-items-start" style="max-height: 85vh; overflow-y: auto;">
                    <div id="liveCardPreview" class="w-100">
                        @include('vendor.card.render_engine')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- REALTIME JAVASCRIPT LIVE ENGINE -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    // 1. Live Toggles Handler (Show / Hide Elements)
    const toggleSwitches = document.querySelectorAll('.live-toggle-control');
    toggleSwitches.forEach(function (sw) {
        sw.addEventListener('change', function () {
            const fieldKey = this.id.replace('toggle_', '');
            const previewTargets = document.querySelectorAll('.preview-field-' + fieldKey);
            
            previewTargets.forEach(function(el) {
                if (sw.checked) {
                    el.classList.remove('d-none');
                } else {
                    el.classList.add('d-none');
                }
            });
        });
    });

    // 2. Display Mode Handler (Icon Only / Icon + Text / Text Only)
    const displayModeSelect = document.getElementById('display_mode');
    if (displayModeSelect) {
        displayModeSelect.addEventListener('change', function () {
            const mode = this.value;
            const container = document.querySelector('.digital-card-container');
            if (container) {
                container.setAttribute('data-display-mode', mode);
            }
            
            const pillIcons = document.querySelectorAll('.pill-icon');
            const pillTexts = document.querySelectorAll('.pill-text');

            pillIcons.forEach(el => {
                if (mode === 'text_only') {
                    el.classList.add('d-none');
                } else {
                    el.classList.remove('d-none');
                }
            });

            pillTexts.forEach(el => {
                if (mode === 'icon_only') {
                    el.classList.add('d-none');
                } else {
                    el.classList.remove('d-none');
                }
            });
        });
    }

    // 3. Live Text Inputs Sync
    const textInputs = document.querySelectorAll('.live-text-input');
    textInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            const fieldName = this.name;
            const targetEl = document.getElementById('preview_text_' + fieldName);
            if (targetEl) {
                targetEl.textContent = this.value;
            }
        });
    });

    // 4. Live Style Sync (CSS Variables for Colors, Fonts & Theme)
    const textColorInput = document.getElementById('custom_text_color');
    const iconColorInput = document.getElementById('custom_icon_color');
    const fontFamilySelect = document.getElementById('font_family');
    const themeStyleSelect = document.getElementById('theme_style');

    function updateLiveStyles() {
        const cardBox = document.querySelector('.digital-card-container');
        if (!cardBox) return;

        if (textColorInput) {
            cardBox.style.setProperty('--card-text-color', textColorInput.value);
        }

        if (iconColorInput) {
            cardBox.style.setProperty('--card-icon-color', iconColorInput.value);
        }

        if (fontFamilySelect) {
            cardBox.style.setProperty('--card-font-family', fontFamilySelect.value);
        }

        if (themeStyleSelect) {
            const theme = themeStyleSelect.value;
            if (theme === 'dark') {
                cardBox.style.background = 'linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%)';
            } else if (theme === 'royal') {
                cardBox.style.background = 'linear-gradient(135deg, #31103f 0%, #0b0726 100%)';
            } else if (theme === 'emerald') {
                cardBox.style.background = 'linear-gradient(135deg, #064e3b 0%, #022c22 100%)';
            } else if (theme === 'midnight') {
                cardBox.style.background = '#000000';
            }
        }
    }

    [textColorInput, iconColorInput, fontFamilySelect, themeStyleSelect].forEach(item => {
        if (item) {
            item.addEventListener('input', updateLiveStyles);
            item.addEventListener('change', updateLiveStyles);
        }
    });

    // Run initial update
    updateLiveStyles();

});
</script>
@endsection