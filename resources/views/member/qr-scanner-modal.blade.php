<!-- QR Scanner Component File -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4 text-center">
        <h5 class="fw-bold text-dark mb-2">
            <i class="fas fa-qrcode me-2 text-primary"></i> Fast QR Code Scanner
        </h5>
        <p class="text-muted small mb-4">Kisi bhi QR code ko camera ke samne layein, yeh automatic scan karke URL open kar dega.</p>
        
        <!-- QR Scanner Viewport Container -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div id="reader" class="rounded overflow-hidden border shadow-sm"></div>
            </div>
        </div>

        <!-- Result Status -->
        <div id="scan-result" class="mt-3"></div>
    </div>
</div>

<!-- Include html5-qrcode Library via CDN (Agar aapne layout me pehle se nahi joda hai) -->
@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Code matched = ${decodedText}`, decodedResult);
            
            document.getElementById('scan-result').innerHTML = `
                <div class="alert alert-success py-2 small">
                    <i class="fas fa-check-circle me-1"></i> Successfully Scanned: 
                    <a href="${decodedText}" class="fw-bold text-dark text-decoration-underline" target="_blank">${decodedText}</a>
                </div>
            `;

            if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                window.location.href = decodedText;
            }
        }

        function onScanFailure(error) {
            // Silent for scan misses
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", 
            { 
                fps: 10, 
                qrbox: { width: 250, height: 250 } 
            }, 
            false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    });
</script>
@endpush