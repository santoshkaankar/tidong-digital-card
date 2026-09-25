<!-- MEMBER REFER & EARN POPUP MODAL -->
<div class="modal fade" id="referEarnModal" tabindex="-1" aria-labelledby="referEarnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header text-white border-0 py-3" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2 mb-0" id="referEarnModalLabel">
                    <i class="fas fa-gift text-warning fs-4"></i> Refer & Earn Program
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4 text-center">
                <!-- Icon & Description -->
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-2" style="width: 60px; height: 60px;">
                        <i class="fas fa-share-alt fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Invite Friends & Earn Rewards</h5>
                    <p class="text-muted small mb-0">Share your unique referral link with your friends and network. Your Referral ID will be automatically applied when they register!</p>
                </div>

                <!-- Referral Code Box -->
                <div class="p-3 bg-light rounded-3 mb-3 border text-center">
                    <small class="text-uppercase fw-bold text-muted d-block mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Your Referral ID</small>
                    <span class="fs-4 fw-bold text-primary" id="memberReferralCode">
                        {{ Auth::check() ? (Auth::user()->referral_id ?? Auth::user()->id) : '' }}
                    </span>
                </div>

                <!-- Referral Link Input & Copy Button -->
                <div class="mb-4 text-start">
                    <label class="form-label small fw-bold text-muted mb-1">Direct Registration Link:</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-white fw-semibold text-secondary" id="memberReferralLink" 
                               value="{{ url('/register?ref=' . (Auth::check() ? (Auth::user()->referral_id ?? Auth::user()->id) : '')) }}" readonly>
                        <button class="btn btn-primary px-3 fw-bold" type="button" onclick="copyMemberReferralLink()">
                            <i class="fas fa-copy me-1" id="copyMemberIcon"></i> <span id="copyMemberBtnText">Copy</span>
                        </button>
                    </div>
                </div>

                <!-- Social Share Buttons Grid -->
                <div class="text-start">
                    <label class="form-label small fw-bold text-muted mb-2">Share via Social Media:</label>
                    
                    @php
                        $shareUrl = url('/register?ref=' . (Auth::check() ? (Auth::user()->referral_id ?? Auth::user()->id) : ''));
                        $shareText = "Join me on Tidong Portal! Register using my referral link:";
                    @endphp

                    <!-- First Row: WhatsApp, Facebook, Twitter/X -->
                    <div class="d-flex flex-wrap gap-2 justify-content-between mb-2">
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($shareText . ' ' . $shareUrl) }}" 
                           target="_blank" class="btn btn-success flex-fill fw-bold py-2 rounded-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="fab fa-whatsapp fs-5"></i> WhatsApp
                        </a>

                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" 
                           target="_blank" class="btn btn-primary flex-fill fw-bold py-2 rounded-3 d-flex align-items-center justify-content-center gap-2" style="background-color: #1877f2; border: none;">
                            <i class="fab fa-facebook-f fs-5"></i> Facebook
                        </a>

                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($shareText) }}&url={{ urlencode($shareUrl) }}" 
                           target="_blank" class="btn btn-dark flex-fill fw-bold py-2 rounded-3 d-flex align-items-center justify-content-center gap-2">
                            <i class="fab fa-x-twitter fs-5"></i> Twitter
                        </a>
                    </div>

                    <!-- Second Row: LinkedIn, Telegram, Instagram -->
                    <div class="d-flex flex-wrap gap-2 justify-content-between">
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" 
                           target="_blank" class="btn flex-fill fw-bold py-2 rounded-3 text-white d-flex align-items-center justify-content-center gap-2" style="background-color: #0a66c2; border: none;">
                            <i class="fab fa-linkedin-in fs-5"></i> LinkedIn
                        </a>

                        <a href="https://t.me/share/url?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareText) }}" 
                           target="_blank" class="btn flex-fill fw-bold py-2 rounded-3 text-white d-flex align-items-center justify-content-center gap-2" style="background-color: #229ed9; border: none;">
                            <i class="fab fa-telegram-plane fs-5"></i> Telegram
                        </a>

                        <button type="button" onclick="copyForInstagram()" class="btn flex-fill fw-bold py-2 rounded-3 text-white d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); border: none;">
                            <i class="fab fa-instagram fs-5"></i> Instagram
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function copyMemberReferralLink() {
    const linkInput = document.getElementById('memberReferralLink');
    if (!linkInput) return;

    linkInput.select();
    linkInput.setSelectionRange(0, 99999);
    
    navigator.clipboard.writeText(linkInput.value).then(() => {
        const btnText = document.getElementById('copyMemberBtnText');
        const btnIcon = document.getElementById('copyMemberIcon');
        
        if (btnText) btnText.innerText = 'Copied!';
        if (btnIcon) btnIcon.className = 'fas fa-check me-1';
        
        setTimeout(() => {
            if (btnText) btnText.innerText = 'Copy';
            if (btnIcon) btnIcon.className = 'fas fa-copy me-1';
        }, 2000);
    }).catch(err => {
        console.error('Copy failed:', err);
    });
}

function copyForInstagram() {
    copyMemberReferralLink();
    alert('Referral link copied to clipboard! You can now paste it in your Instagram Story, Bio, or Direct Message.');
}
</script>