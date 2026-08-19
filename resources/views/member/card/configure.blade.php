<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Visiting Card Configuration - Tidong® Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: hidden; }
        #sidebar { min-width: 260px; max-width: 260px; background: #0f172a; color: #fff; transition: all 0.3s ease; min-height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000; }
        #sidebar .sidebar-header { padding: 20px; background: #1e293b; font-size: 1.25rem; font-weight: bold; display: flex; align-items: center; gap: 10px; color: #38bdf8; }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 12px 20px; font-size: 0.95rem; display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; transition: all 0.3s; }
        #sidebar ul li a:hover, #sidebar ul li.active a { color: #fff; background: #1e293b; border-left: 4px solid #38bdf8; }
        #content { margin-left: 260px; width: calc(100% - 260px); min-height: 100vh; transition: all 0.3s ease; }
        .top-navbar { background: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.04); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .form-card { background: #fff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: none; }
        .form-section-title { font-size: 1.05rem; font-weight: 700; color: #0f172a; border-left: 4px solid #38bdf8; padding-left: 10px; margin-bottom: 20px; margin-top: 10px; }
        @media (max-width: 992px) { #sidebar { margin-left: -260px; } #sidebar.active { margin-left: 0; } #content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

    {{-- Member Sidebar --}}
    @include('member.sidebar')

    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-brand fw-bold text-dark mb-0 h6 d-flex align-items-center gap-2">
                <i class="fas fa-id-card text-primary"></i> Digital Visiting Card Configuration
                @if(isset($card) && $card->card_no)
                    <span class="badge bg-success ms-2">Card No: {{ $card->card_no }}</span>
                @endif
            </div>
            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('member.dashboard') }}" class="btn btn-outline-dark btn-sm">
                    <i class="fas fa-home me-1"></i> Dashboard
                </a>
            </div>
        </nav>

        <div class="container-fluid py-4 px-4">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="form-card p-4 p-md-5">
                        <div class="text-center mb-5">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-2">🎴 Digital Card Settings</span>
                            <h2 class="fw-bold text-dark">Manage Your Profile Details</h2>
                            <p class="text-muted">Yahan update ki gayi details aapke digital visiting card aur public profile par reflect hongi.</p>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                                <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger rounded-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Corrected Store Master Route --}}
                        <form action="{{ route('member.card.configure.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="country_id" id="country_id" value="{{ old('country_id', $card->country_id ?? '1') }}">
                            <input type="hidden" name="state_id" id="state_id" value="{{ old('state_id', $card->state_id ?? '') }}">

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

                            <!-- Submit Button -->
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm">
                                    <i class="fas fa-save me-2"></i> Save & Update Card Details 🚀
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <footer class="text-center py-4 text-muted small border-top mt-5">
            &copy; {{ date('Y') }} Tidong® Portal. All rights reserved.
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            function initSelect2(selector, placeholderText) {
                $(selector).select2({
                    theme: 'bootstrap-5',
                    placeholder: placeholderText,
                    allowClear: true,
                    tags: true,
                    ajax: {
                        url: "{{ route('search.locations') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { q: params.term };
                        },
                        processResults: function (data) {
                            return { 
                                results: Array.isArray(data) ? data : (data.results || []) 
                            };
                        }
                    }
                });
            }

            initSelect2('#area_search', 'Search Area');
            initSelect2('#pincode_search', 'Search Pincode');
            initSelect2('#city_search', 'Search City');

            $('#area_search, #pincode_search, #city_search').on('select2:select', function(e) {
                let data = e.params.data;
                if(data) {
                    if(data.area) {
                        $('#area_search').html(`<option value="${data.area}" selected>${data.area}</option>`).trigger('change.select2');
                    }
                    if(data.pincode) {
                        $('#pincode_search').html(`<option value="${data.pincode}" selected>${data.pincode}</option>`).trigger('change.select2');
                    }
                    if(data.city) {
                        $('#city_search').html(`<option value="${data.city}" selected>${data.city}</option>`).trigger('change.select2');
                    }
                    if(data.state) {
                        $('#state').val(data.state);
                    }
                }
            });
        });

        document.getElementById('sidebarCollapse')?.addEventListener('click', function () {
            document.getElementById('sidebar')?.classList.toggle('active');
        });
    </script>
</body>
</html>