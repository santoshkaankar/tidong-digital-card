<section class="py-5 bg-white border-top border-bottom" id="qr-access"><div class="container"><div class="row align-items-center g-4">
<div class="col-md-5 text-center"><div class="p-3 bg-light rounded-4 d-inline-block border shadow-sm">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&amp;data={{ urlencode(url('/')) }}" width="180" height="180" loading="lazy" alt="QR code to the public TIDONG homepage" class="img-fluid rounded">
<p class="mt-2 mb-0 fw-bold text-dark small"><i class="fas fa-camera text-primary me-1"></i>Scan for TIDONG Home</p></div></div>
<div class="col-md-7 text-center text-md-start"><span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold mb-2">Smart QR Access</span>
<h3 class="fw-bold text-dark mb-3">Scan &amp; Explore TIDONG®</h3><p class="text-muted mb-4">Scan the QR to open the TIDONG homepage. Restaurant table menus use their own secure QR codes supplied by the participating business.</p>
<a href="{{ url('/') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold"><i class="fas fa-qrcode me-2"></i> Open Homepage</a></div></div></div></section>