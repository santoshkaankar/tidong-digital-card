<!-- 1. Personal & Business Details -->
<div class="form-section-title">1. Basic & Personal Details</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary small">Card Holder / Display Name *</label>
        <input type="text" name="name" class="form-control bg-light" required value="{{ old('name', $card->name ?? auth()->user()->name) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary small">Business / Profession *</label>
        <input type="text" name="business_name" class="form-control bg-light" required placeholder="e.g. Tidong Marketing Pvt Ltd" value="{{ old('business_name', $card->business_name ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label text-secondary small">Designation / Post</label>
        <input type="text" name="designation" class="form-control bg-light" placeholder="e.g. Director / Manager" value="{{ old('designation', $card->designation ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label text-secondary small">Nick Name / Known As (Optional)</label>
        <input type="text" name="nickname" class="form-control bg-light" placeholder="e.g. Lalo" value="{{ old('nickname', $card->nickname ?? '') }}">
    </div>
    <div class="col-md-12">
        <label class="form-label text-secondary small">Tagline / Business Slogan</label>
        <input type="text" name="tagline" class="form-control bg-light" placeholder="e.g. Quality You Can Trust" value="{{ old('tagline', $card->tagline ?? '') }}">
    </div>
</div>

<!-- 2. Contact Numbers -->
<div class="form-section-title">2. Contact Details</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label fw-bold text-secondary small">Primary Phone *</label>
        <input type="text" name="phone" class="form-control bg-light" required placeholder="98765xxxxx" value="{{ old('phone', $card->phone ?? auth()->user()->mobile ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label text-secondary small">Alternate Phone</label>
        <input type="text" name="alt_phone" class="form-control bg-light" placeholder="91234xxxxx" value="{{ old('alt_phone', $card->alt_phone ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label text-secondary small">WhatsApp Number</label>
        <input type="text" name="whatsapp" class="form-control bg-light" placeholder="98765xxxxx" value="{{ old('whatsapp', $card->whatsapp ?? '') }}">
    </div>
</div>

<!-- 3. Emails -->
<div class="form-section-title">3. Email Addresses</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label text-secondary small">Gmail</label>
        <input type="email" name="gmail" class="form-control bg-light" placeholder="example@gmail.com" value="{{ old('gmail', $card->gmail ?? auth()->user()->email ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label text-secondary small">Yahoo Email</label>
        <input type="email" name="yahoo_email" class="form-control bg-light" placeholder="example@yahoo.com" value="{{ old('yahoo_email', $card->yahoo_email ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label text-secondary small">Other / Corporate Email</label>
        <input type="email" name="other_email" class="form-control bg-light" placeholder="business@domain.com" value="{{ old('other_email', $card->other_email ?? '') }}">
    </div>
</div>

<!-- 4. Social & Links -->
<div class="form-section-title">4. Social Media & Web Links</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label text-secondary small">Facebook Profile / Page</label>
        <input type="text" name="facebook" class="form-control bg-light" placeholder="Facebook URL" value="{{ old('facebook', $card->facebook ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label text-secondary small">Instagram Link</label>
        <input type="text" name="instagram" class="form-control bg-light" placeholder="Instagram URL" value="{{ old('instagram', $card->instagram ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label text-secondary small">Twitter / X Handle</label>
        <input type="text" name="twitter_x" class="form-control bg-light" placeholder="Twitter / X URL" value="{{ old('twitter_x', $card->twitter_x ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label text-secondary small">LinkedIn Profile</label>
        <input type="text" name="linkedin" class="form-control bg-light" placeholder="LinkedIn URL" value="{{ old('linkedin', $card->linkedin ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label text-secondary small">YouTube Channel</label>
        <input type="text" name="youtube" class="form-control bg-light" placeholder="YouTube Channel URL" value="{{ old('youtube', $card->youtube ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label text-secondary small">Telegram Link</label>
        <input type="text" name="telegram" class="form-control bg-light" placeholder="Telegram Link" value="{{ old('telegram', $card->telegram ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label text-secondary small">Official Website URL</label>
        <input type="text" name="website_link" class="form-control bg-light" placeholder="https://yourwebsite.com" value="{{ old('website_link', $card->website_link ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label text-secondary small">Google Maps Location Link</label>
        <input type="text" name="map_location_link" class="form-control bg-light" placeholder="https://maps.google.com/..." value="{{ old('map_location_link', $card->map_location_link ?? '') }}">
    </div>
</div>

<!-- 5. Payment Options -->
<div class="form-section-title">5. Payment & UPI Configuration</div>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label text-secondary small">PhonePe Number</label>
        <input type="text" name="phonepe" class="form-control bg-light" placeholder="PhonePe Number" value="{{ old('phonepe', $card->phonepe ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label text-secondary small">Google Pay Number</label>
        <input type="text" name="gpay" class="form-control bg-light" placeholder="Google Pay Number" value="{{ old('gpay', $card->gpay ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label text-secondary small">Paytm Number</label>
        <input type="text" name="paytm" class="form-control bg-light" placeholder="Paytm Number" value="{{ old('paytm', $card->paytm ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label text-secondary small">Primary UPI ID</label>
        <input type="text" name="upi_id" class="form-control bg-light" placeholder="username@ybl / upi" value="{{ old('upi_id', $card->upi_id ?? '') }}">
    </div>
</div>

<!-- 6. Media & Files -->
<div class="form-section-title">6. Branding Assets (Uploads)</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary small">Profile Picture / Company Logo</label>
        @if(isset($card) && $card->photo)
            <div class="mb-2"><small class="text-success"><i class="fas fa-check-circle me-1"></i> Current: {{ basename($card->photo) }}</small></div>
        @endif
        <input type="file" name="photo" class="form-control bg-light" accept="image/*">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary small">QR Code Image (Payment/Contact)</label>
        @if(isset($card) && $card->qr_code)
            <div class="mb-2"><small class="text-success"><i class="fas fa-check-circle me-1"></i> Current: {{ basename($card->qr_code) }}</small></div>
        @endif
        <input type="file" name="qr_code" class="form-control bg-light" accept="image/*">
    </div>
</div>

<!-- 7. Address & Location -->
<div class="form-section-title">7. Address & Location</div>
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <label class="form-label fw-bold text-secondary small">Premises / House No / Landmark / Street</label>
        <input type="text" name="address" class="form-control bg-light" placeholder="e.g. Office No 12, Main Market Road" value="{{ old('address', $card->address ?? '') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold text-secondary small">Area / Post Office *</label>
        <select id="area_search" name="area" class="form-control bg-light" required>
            @if(isset($card) && $card->area)
                <option value="{{ $card->area }}" selected>{{ $card->area }}</option>
            @endif
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold text-secondary small">Pincode *</label>
        <select id="pincode_search" name="pincode" class="form-control bg-light" required>
            @if(isset($card) && $card->pincode)
                <option value="{{ $card->pincode }}" selected>{{ $card->pincode }}</option>
            @endif
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold text-secondary small">City / District *</label>
        <select id="city_search" name="city" class="form-control bg-light" required>
            @if(isset($card) && $card->city)
                <option value="{{ $card->city }}" selected>{{ $card->city }}</option>
            @endif
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-bold text-secondary small">State *</label>
        <input type="text" id="state" name="state" class="form-control bg-light" required readonly placeholder="Auto-filled" value="{{ old('state', $card->state ?? '') }}">
    </div>
</div>

<!-- 8. About & Products Details -->
<div class="form-section-title">8. About & Offerings</div>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label text-secondary small">About Us / Summary</label>
        <textarea name="about_us" class="form-control bg-light" rows="3" placeholder="Brief info about your business profile...">{{ old('about_us', $card->about_us ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label text-secondary small">Key Products or Services</label>
        <textarea name="services_or_products" class="form-control bg-light" rows="3" placeholder="List products, key offerings or services...">{{ old('services_or_products', $card->services_or_products ?? '') }}</textarea>
    </div>
</div>