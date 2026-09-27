<!-- Clean QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm-custom">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            
            <div class="modal-header border-0 pb-0">
                <h6 class="fw-bold modal-title text-dark">
                    <i class="fas fa-qrcode text-primary me-2"></i> Fast QR Code Scanner
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center p-3">
                <p class="text-muted extra-small mb-3">Place the QR code in front of the camera to scan automatically.</p>

                <!-- Camera Viewport Box -->
                <div class="position-relative mx-auto overflow-hidden rounded-3 border bg-black" style="max-width: 280px; min-height: 250px;">
                    <div id="qr-reader" style="width: 100%;"></div>
                </div>

                <!-- Live Status Output -->
                <div id="scan-result" class="mt-3"></div>
            </div>

        </div>
    </div>
</div>