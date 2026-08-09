<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($card) ? 'Edit Digital Visiting Card' : 'Create Digital Visiting Card & Brochure' }}</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS for searchable dropdown -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        body { background-color: #f8f9fa; }
        #sidebar {
            position: fixed; top: 0; left: 0; height: 100vh; width: 260px;
            background: #212529; color: #fff; transition: all 0.3s; z-index: 1000;
        }
        #sidebar .brand {
            font-size: 1.2rem; padding: 20px; background: #1a1e21; text-align: center;
            font-weight: bold; border-bottom: 1px solid #373b3e;
        }
        #sidebar .nav-link {
            color: #adb5bd; padding: 12px 20px; margin: 4px 10px; border-radius: 8px; transition: 0.2s;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active { color: #fff; background: #0d6efd; }
        #main-content { margin-left: 260px; padding: 20px; }
        @media (max-width: 768px) {
            #sidebar { width: 100%; height: auto; position: relative; }
            #main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Menu -->
    <div id="sidebar" class="d-flex flex-column">
        <div class="brand">
            <i class="fas fa-shield-alt text-primary me-2"></i> Admin Panel
        </div>
        <ul class="nav nav-pills flex-column mb-auto p-2">
            <li class="nav-item mb-1">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('card.create') }}" class="nav-link {{ request()->routeIs('card.create') ? 'active' : '' }}">
                    <i class="fas fa-id-card me-2"></i> Create Card
                </a>
            </li>
        </ul>
        <div class="p-3 border-top border-secondary">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <div id="main-content">
        <nav class="navbar navbar-expand navbar-light bg-white px-4 rounded-4 shadow-sm mb-4">
            <div class="container-fluid">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="fw-bold text-dark"><i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name ?? 'Admin' }}</span>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container my-4">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-header bg-primary text-white text-center py-4 rounded-top-4">
                            <h3 class="mb-0">{{ isset($card) ? '✏️ Edit Digital Visiting Card / Brochure' : '🚀 Create Digital Visiting Card / Brochure' }}</h3>
                            <p class="mb-0 text-white-50">Complete details below.</p>
                        </div>
                        <div class="card-body p-4 p-md-5">

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ isset($card) ? route('card.update', $card->id) : route('card.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <!-- 1. Basic Details -->
                                <h4 class="text-primary mb-3">1. Basic & Personal Details</h4>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Person/Card Holder Name *</label>
                                        <input type="text" name="name" class="form-control" required placeholder="e.g. Saharsh Sharma" value="{{ old('name', $card->name ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Business / Store Name *</label>
                                        <input type="text" name="business_name" class="form-control" required placeholder="e.g. Sharma General Store" value="{{ old('business_name', $card->business_name ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tagline / Slogan</label>
                                        <input type="text" name="tagline" class="form-control" placeholder="e.g. Quality You Can Trust" value="{{ old('tagline', $card->tagline ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nick Name (Optional)</label>
                                        <input type="text" name="owner_name" class="form-control" placeholder="e.g. Kaankar" value="{{ old('owner_name', $card->owner_name ?? '') }}">
                                    </div>
                                </div>

                                <!-- 2. Contact Numbers -->
                                <h4 class="text-primary mb-3">2. Contact Numbers</h4>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Primary Phone *</label>
                                        <input type="text" name="phone" class="form-control" required placeholder="9876543210" value="{{ old('phone', $card->phone ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Alternate Phone</label>
                                        <input type="text" name="alt_phone" class="form-control" placeholder="9123456780" value="{{ old('alt_phone', $card->alt_phone ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">WhatsApp Number</label>
                                        <input type="text" name="whatsapp" class="form-control" placeholder="9876543210" value="{{ old('whatsapp', $card->whatsapp ?? '') }}">
                                    </div>
                                </div>

                                <!-- 3. Emails -->
                                <h4 class="text-primary mb-3">3. Email Addresses</h4>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label">Gmail</label>
                                        <input type="email" name="gmail" class="form-control" placeholder="example@gmail.com" value="{{ old('gmail', $card->gmail ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Yahoo Email</label>
                                        <input type="email" name="yahoo_email" class="form-control" placeholder="example@yahoo.com" value="{{ old('yahoo_email', $card->yahoo_email ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Other Email</label>
                                        <input type="email" name="other_email" class="form-control" placeholder="business@domain.com" value="{{ old('other_email', $card->other_email ?? '') }}">
                                    </div>
                                </div>

                                <!-- 4. Social Media & Web Links -->
                                <h4 class="text-primary mb-3">4. Social Media & Web Links</h4>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4"><input type="text" name="facebook" class="form-control" placeholder="Facebook URL" value="{{ old('facebook', $card->facebook ?? '') }}"></div>
                                    <div class="col-md-4"><input type="text" name="instagram" class="form-control" placeholder="Instagram URL" value="{{ old('instagram', $card->instagram ?? '') }}"></div>
                                    <div class="col-md-4"><input type="text" name="twitter_x" class="form-control" placeholder="Twitter / X URL" value="{{ old('twitter_x', $card->twitter_x ?? '') }}"></div>
                                    <div class="col-md-4"><input type="text" name="linkedin" class="form-control" placeholder="LinkedIn URL" value="{{ old('linkedin', $card->linkedin ?? '') }}"></div>
                                    <div class="col-md-4"><input type="text" name="youtube" class="form-control" placeholder="YouTube Channel URL" value="{{ old('youtube', $card->youtube ?? '') }}"></div>
                                    <div class="col-md-4"><input type="text" name="telegram" class="form-control" placeholder="Telegram Link" value="{{ old('telegram', $card->telegram ?? '') }}"></div>
                                    <div class="col-md-6"><input type="text" name="website_link" class="form-control" placeholder="Official Website Link" value="{{ old('website_link', $card->website_link ?? '') }}"></div>
                                    <div class="col-md-6"><input type="text" name="map_location_link" class="form-control" placeholder="Google Map Location Link" value="{{ old('map_location_link', $card->map_location_link ?? '') }}"></div>
                                </div>

                                <!-- 5. Payment Apps Numbers -->
                                <h4 class="text-primary mb-3">5. Payment Apps Numbers</h4>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3"><input type="text" name="phonepe" class="form-control" placeholder="PhonePe Number" value="{{ old('phonepe', $card->phonepe ?? '') }}"></div>
                                    <div class="col-md-3"><input type="text" name="gpay" class="form-control" placeholder="Google Pay Number" value="{{ old('gpay', $card->gpay ?? '') }}"></div>
                                    <div class="col-md-3"><input type="text" name="paytm" class="form-control" placeholder="Paytm Number" value="{{ old('paytm', $card->paytm ?? '') }}"></div>
                                    <div class="col-md-3"><input type="text" name="upi_id" class="form-control" placeholder="UPI ID (e.g. @ybl)" value="{{ old('upi_id', $card->upi_id ?? '') }}"></div>
                                </div>

                                <!-- 6. Catalog / Brochure Details -->
                                <h4 class="text-primary mb-3">6. Catalog / Brochure Details</h4>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label">About Us / Business Description</label>
                                        <textarea name="about_us" class="form-control" rows="3" placeholder="Write something about the business...">{{ old('about_us', $card->about_us ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Products / Services Offered (Catalog Items)</label>
                                        <textarea name="services_or_products" class="form-control" rows="3" placeholder="Item 1, Item 2, Item 3...">{{ old('services_or_products', $card->services_or_products ?? '') }}</textarea>
                                    </div>
                                </div>

                                <!-- 7. Media Uploads & Location -->
                                <h4 class="text-primary mb-3">7. Media Uploads & Location</h4>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Profile Photo / Logo</label>
                                        @if(isset($card) && $card->photo)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $card->photo) }}" alt="Photo" width="75" class="rounded border mb-1">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="removePhoto">
                                                    <label class="form-check-label text-danger small fw-bold" for="removePhoto">Remove current photo</label>
                                                </div>
                                            </div>
                                        @endif
                                        <input type="file" name="photo" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Payment QR Code Image</label>
                                        @if(isset($card) && $card->qr_code)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $card->qr_code) }}" alt="QR Code" width="75" class="rounded border mb-1">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remove_qr" value="1" id="removeQr">
                                                    <label class="form-check-label text-danger small fw-bold" for="removeQr">Remove current QR code</label>
                                                </div>
                                            </div>
                                        @endif
                                        <input type="file" name="qr_code" class="form-control">
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">House No. / Street / Landmark</label>
                                        <input type="text" name="address" class="form-control" placeholder="e.g. Plot No 12, Main Street" value="{{ old('address', $card->address ?? '') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Area *</label>
                                        <select id="area_search" name="area" class="form-control" required>
                                            @if(isset($card) && $card->area)
                                                <option value="{{ $card->area }}" selected>{{ $card->area }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Pincode *</label>
                                        <select id="pincode_search" name="pincode" class="form-control" required>
                                            @if(isset($card) && $card->pincode)
                                                <option value="{{ $card->pincode }}" selected>{{ $card->pincode }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">City *</label>
                                        <select id="city_search" name="city" class="form-control" required>
                                            @if(isset($card) && $card->city)
                                                <option value="{{ $card->city }}" selected>{{ $card->city }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">State *</label>
                                        <input type="text" id="state" name="state" class="form-control" required readonly placeholder="Auto-filled" value="{{ old('state', $card->state ?? '') }}">
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow">
                                        {{ isset($card) ? 'Update Digital Card ✏️' : 'Generate Digital Card 🚀' }}
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
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
    </script>
</body>
</html>