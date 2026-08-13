<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Digital Visiting Card - Tidong® Portal</title>
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
        .form-section-title { font-size: 1.1rem; font-weight: 700; color: #0f172a; border-left: 4px solid #38bdf8; padding-left: 10px; margin-bottom: 20px; }
        @media (max-width: 992px) { #sidebar { margin-left: -260px; } #sidebar.active { margin-left: 0; } #content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

    @include('member.sidebar')

    <div id="content">
        <nav class="top-navbar">
            <button type="button" id="sidebarCollapse" class="btn btn-dark d-lg-none">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-brand fw-bold text-dark mb-0 h6 d-flex align-items-center gap-2">
                <i class="fas fa-id-badge text-primary"></i> Digital Visiting Card Studio
            </div>
            <div class="ms-auto">
                <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </nav>

        <div class="container-fluid py-4 px-4">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="form-card p-4 p-md-5">
                        <div class="text-center mb-5">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-2">✨ User Portal Studio</span>
                            <h2 class="fw-bold text-dark">Create Your Digital Visiting Card</h2>
                            <p class="text-muted">Stand out digitally with your own customized interactive business card & catalog.</p>
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

                        <form action="{{ route('member.card.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-section-title">1. Basic & Personal Details</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Person / Card Holder Name *</label>
                                    <input type="text" name="name" class="form-control bg-light" required placeholder="e.g. Santosh Kumar Sharma" value="{{ old('name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Business / Store Name *</label>
                                    <input type="text" name="business_name" class="form-control bg-light" required placeholder="e.g. Tidong Marketing Pvt Ltd" value="{{ old('business_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Tagline / Slogan</label>
                                    <input type="text" name="tagline" class="form-control bg-light" placeholder="e.g. Quality You Can Trust" value="{{ old('tagline') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Nick Name (Optional)</label>
                                    <input type="text" name="owner_name" class="form-control bg-light" placeholder="e.g. Kaankar" value="{{ old('owner_name') }}">
                                </div>
                            </div>

                            <div class="form-section-title">2. Contact Numbers</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-secondary small">Primary Phone *</label>
                                    <input type="text" name="phone" class="form-control bg-light" required placeholder="9876543210" value="{{ old('phone') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small">Alternate Phone</label>
                                    <input type="text" name="alt_phone" class="form-control bg-light" placeholder="9123456780" value="{{ old('alt_phone') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small">WhatsApp Number</label>
                                    <input type="text" name="whatsapp" class="form-control bg-light" placeholder="9876543210" value="{{ old('whatsapp') }}">
                                </div>
                            </div>

                            <div class="form-section-title">3. Email Addresses</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small">Gmail</label>
                                    <input type="email" name="gmail" class="form-control bg-light" placeholder="example@gmail.com" value="{{ old('gmail') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small">Yahoo Email</label>
                                    <input type="email" name="yahoo_email" class="form-control bg-light" placeholder="example@yahoo.com" value="{{ old('yahoo_email') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small">Other Email</label>
                                    <input type="email" name="other_email" class="form-control bg-light" placeholder="business@domain.com" value="{{ old('other_email') }}">
                                </div>
                            </div>

                            <div class="form-section-title">4. Social Media & Web Links</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4"><input type="text" name="facebook" class="form-control bg-light" placeholder="Facebook URL" value="{{ old('facebook') }}"></div>
                                <div class="col-md-4"><input type="text" name="instagram" class="form-control bg-light" placeholder="Instagram URL" value="{{ old('instagram') }}"></div>
                                <div class="col-md-4"><input type="text" name="twitter_x" class="form-control bg-light" placeholder="Twitter / X URL" value="{{ old('twitter_x') }}"></div>
                                <div class="col-md-4"><input type="text" name="linkedin" class="form-control bg-light" placeholder="LinkedIn URL" value="{{ old('linkedin') }}"></div>
                                <div class="col-md-4"><input type="text" name="youtube" class="form-control bg-light" placeholder="YouTube Channel URL" value="{{ old('youtube') }}"></div>
                                <div class="col-md-4"><input type="text" name="telegram" class="form-control bg-light" placeholder="Telegram Link" value="{{ old('telegram') }}"></div>
                                <div class="col-md-6"><input type="text" name="website_link" class="form-control bg-light" placeholder="Official Website Link" value="{{ old('website_link') }}"></div>
                                <div class="col-md-6"><input type="text" name="map_location_link" class="form-control bg-light" placeholder="Google Map Location Link" value="{{ old('map_location_link') }}"></div>
                            </div>

                            <div class="form-section-title">5. Payment Apps Numbers</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-3"><input type="text" name="phonepe" class="form-control bg-light" placeholder="PhonePe Number" value="{{ old('phonepe') }}"></div>
                                <div class="col-md-3"><input type="text" name="gpay" class="form-control bg-light" placeholder="Google Pay Number" value="{{ old('gpay') }}"></div>
                                <div class="col-md-3"><input type="text" name="paytm" class="form-control bg-light" placeholder="Paytm Number" value="{{ old('paytm') }}"></div>
                                <div class="col-md-3"><input type="text" name="upi_id" class="form-control bg-light" placeholder="UPI ID (e.g. @ybl)" value="{{ old('upi_id') }}"></div>
                            </div>

                            <div class="form-section-title">6. About Us / Description</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <textarea name="about_us" class="form-control bg-light" rows="3" placeholder="Write something about your business...">{{ old('about_us') }}</textarea>
                                </div>
                            </div>

                            <div class="form-section-title">7. Media Uploads & Location</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Profile Photo / Logo</label>
                                    <input type="file" name="photo" class="form-control bg-light">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">Payment QR Code Image</label>
                                    <input type="file" name="qr_code" class="form-control bg-light">
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-secondary small">House No. / Street / Landmark</label>
                                    <input type="text" name="address" class="form-control bg-light" placeholder="e.g. Plot No 12, Main Street" value="{{ old('address') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary small">Area *</label>
                                    <select id="area_search" name="area" class="form-control bg-light" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary small">Pincode *</label>
                                    <select id="pincode_search" name="pincode" class="form-control bg-light" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary small">City *</label>
                                    <select id="city_search" name="city" class="form-control bg-light" required></select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-secondary small">State *</label>
                                    <input type="text" id="state" name="state" class="form-control bg-light" required readonly placeholder="Auto-filled" value="{{ old('state') }}">
                                </div>
                            </div>

                            <div class="form-section-title">8. Other Details</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <textarea name="services_or_products" class="form-control bg-light" rows="3" placeholder="Add other details here...">{{ old('services_or_products') }}</textarea>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm">
                                    Save & Continue 🚀
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <footer class="text-center py-4 text-muted small border-top mt-5">
                &copy; {{ date('Y') }} Tidong® Portal. All rights reserved.
            </footer>
        </div>
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
                    ajax: {
                        url: '/search-locations',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data })
                    }
                });
            }

            initSelect2('#area_search', 'Search Area');
            initSelect2('#pincode_search', 'Search Pincode');
            initSelect2('#city_search', 'Search City');

            $('#area_search, #pincode_search, #city_search').on('select2:select', function(e) {
                let data = e.params.data;
                $('#area_search').html(`<option value="${data.area}" selected>${data.area}</option>`).trigger('change');
                $('#pincode_search').html(`<option value="${data.pincode}" selected>${data.pincode}</option>`).trigger('change');
                $('#city_search').html(`<option value="${data.city}" selected>${data.city}</option>`).trigger('change');
                $('#state').val(data.state);
            });
        });

        document.getElementById('sidebarCollapse')?.addEventListener('click', function () {
            document.getElementById('sidebar')?.classList.toggle('active');
        });
    </script>
</body>
</html>