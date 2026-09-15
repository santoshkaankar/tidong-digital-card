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
    @include('member.partials.sidebar')

    <div id="content">
        @include('member.partials.top-navbar')

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

                        <form action="{{ route('member.card.configure.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="country_id" id="country_id" value="{{ old('country_id', $card->country_id ?? '1') }}">
                            <input type="hidden" name="state_id" id="state_id" value="{{ old('state_id', $card->state_id ?? '') }}">

                            {{-- Form Sections Partials --}}
                            @include('member.partials.card-form-sections')

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