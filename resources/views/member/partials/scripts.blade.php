<!-- Bootstrap 5 Bundle (Includes Popper JS for Dropdowns) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- HTML5 QR Code CDN -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    function setTheme(theme) {
        if (theme === 'system') {
            const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
            localStorage.setItem('app_theme', 'system');
        } else {
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('app_theme', theme);
        }
        updateThemeIcon(theme);
    }

    function updateThemeIcon(theme) {
        const icon = document.getElementById('currentThemeIcon');
        if (!icon) return;

        if (theme === 'dark') {
            icon.className = 'bi bi-moon-stars-fill text-primary';
        } else if (theme === 'system') {
            icon.className = 'bi bi-circle-half text-secondary';
        } else {
            icon.className = 'bi bi-sun-fill text-warning';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const savedTheme = localStorage.getItem('app_theme') || 'light';
        setTheme(savedTheme);
    });
</script>

<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';

    function testVoice() {
        if (window.KDS_NOTIFIER && typeof window.KDS_NOTIFIER.testVoice === 'function') {
            window.KDS_NOTIFIER.testVoice();
        }
    }

    function speakOrder(text) {
        if (window.KDS_NOTIFIER && typeof window.KDS_NOTIFIER.playNewOrderAlert === 'function') {
            window.KDS_NOTIFIER.playNewOrderAlert(text);
        }
    }

    function resolveWaiterCall(callId) {
        if (!callId) return;

        if (window.KDS_NOTIFIER && typeof window.KDS_NOTIFIER.stopAll === 'function') {
            window.KDS_NOTIFIER.stopAll();
        }

        var card = document.getElementById('waiter-call-card-' + callId);
        if (card) {
            card.style.display = 'none';
        }

        fetch('/member/waiter-calls/' + callId + '/resolve', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            location.reload();
        })
        .catch(function(err) {
            console.error('Resolve Error:', err);
            location.reload();
        });
    }

    setInterval(function() {
        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(response) { return response.text(); })
        .then(function(html) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            
            var newContent = doc.getElementById('waiter-calls-container');
            var oldContent = document.getElementById('waiter-calls-container');

            if (newContent && oldContent) {
                if (oldContent.innerHTML.trim() !== newContent.innerHTML.trim()) {
                    oldContent.innerHTML = newContent.innerHTML;
                }
            }
        })
        .catch(function(err) { console.log('Polling Error:', err); });
    }, 4000);
</script>

<!-- QR Code Scanner Engine Script -->
<script>
    let globalHtml5QrCode = null;

    function openGlobalQrScanner() {
        let modalEl = document.getElementById('qrScannerModal');
        if (!modalEl) return;
        let modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }

    document.addEventListener("DOMContentLoaded", function () {
        const modalEl = document.getElementById('qrScannerModal');
        if (!modalEl) return;

        modalEl.addEventListener('shown.bs.modal', function () {
            startCameraScanner();
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            stopCameraScanner();
        });
    });

    function startCameraScanner() {
        const resultDiv = document.getElementById('scan-result');
        if (resultDiv) resultDiv.innerHTML = '';

        if (!globalHtml5QrCode) {
            globalHtml5QrCode = new Html5Qrcode("qr-reader");
        }

        const config = { 
            fps: 15, 
            qrbox: { width: 200, height: 200 } 
        };

        globalHtml5QrCode.start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanFailure
        ).catch(err => {
            console.error("Camera Error: ", err);
            if (resultDiv) {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger py-2 small mb-0">
                        <i class="fas fa-exclamation-circle me-1"></i> Camera access error. Settings check karein.
                    </div>
                `;
            }
        });
    }

    function stopCameraScanner() {
        if (globalHtml5QrCode && globalHtml5QrCode.isScanning) {
            globalHtml5QrCode.stop().then(() => {
                console.log("Scanner stopped.");
            }).catch(err => console.error("Scanner stop fail", err));
        }
    }

    function onScanSuccess(decodedText) {
        stopCameraScanner();

        const resultDiv = document.getElementById('scan-result');
        if (resultDiv) {
            resultDiv.innerHTML = `
                <div class="alert alert-success py-2 small fw-bold mb-0">
                    <i class="fas fa-spinner fa-spin me-1"></i> Scanned! Redirecting...
                </div>
            `;
        }

        setTimeout(() => {
            window.location.href = decodedText;
        }, 400);
    }

    function onScanFailure(error) {
        // Silent scanning
    }
</script>