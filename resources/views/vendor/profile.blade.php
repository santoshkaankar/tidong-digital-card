<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>वेंडर प्रोफ़ाइल व सेटिंग्स</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-4" style="max-width: 700px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold m-0"><i class="fas fa-user-cog text-primary me-2"></i>वेंडर प्रोफ़ाइल सेटिंग्स</h4>
        <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-secondary btn-sm fw-bold">
            <i class="fas fa-arrow-left me-1"></i> डैशबोर्ड पर लौटें
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show p-2 small" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close p-2" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('vendor.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <h6 class="fw-bold text-uppercase text-muted border-bottom pb-2 mb-3 small">सामान्य जानकारी</h6>
                
                <div class="mb-3">
                    <label class="form-label fw-bold small">रेस्टोरेंट / वेंडर का नाम</label>
                    <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">ईमेल आईडी</label>
                    <input type="email" class="form-control bg-light" value="{{ auth()->user()->email }}" disabled>
                </div>

                <h6 class="fw-bold text-uppercase text-muted border-bottom pb-2 my-4 small">पेमेंट व स्कैनर सेटिंग्स</h6>

                <div class="mb-3">
                    <label class="form-label fw-bold small">UPI / Payment QR Code अपलोड करें</label>
                    <input type="file" name="payment_qr" class="form-control" accept="image/*">
                    <small class="text-muted" style="font-size: 11px;">GPay, PhonePe, Paytm या बैंक स्कैनर की इमेज (JPG, PNG) अपलोड करें।</small>
                </div>

                @if(auth()->user()->payment_qr)
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <p class="small fw-bold text-dark mb-2">वर्तमान पेमेंट QR कोड:</p>
                        <img src="{{ asset('storage/' . str_replace(['public/', 'storage/'], '', auth()->user()->payment_qr)) }}" 
                             alt="Payment QR" 
                             class="img-thumbnail rounded shadow-sm" 
                             style="max-width: 160px;">
                    </div>
                @endif

                <div class="mt-4 pt-2 border-top">
                    <button type="submit" class="btn btn-primary fw-bold px-4 rounded-3">
                        <i class="fas fa-save me-1"></i> प्रोफाइल सेव करें
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>