@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Configure Classic Card Display</h4>
                    <a href="{{ route('member.card.create') }}" class="btn btn-sm btn-light">Back</a>
                </div>
                <div class="card-body">
                    <form action="{{ isset($card) ? route('member.card.update', $card->id) : route('member.card.store') }}" method="POST">
                        @csrf
                        @if(isset($card))
                            @method('PUT')
                        @endif

                        <p class="text-muted mb-4">Toggle the switches below to choose which details you want to display on your Classic Card design.</p>

                        <div class="list-group mb-4">
                            <!-- 1. Card Holder Name (Locked/Required) -->
                            <div class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                <span><i class="fas fa-user text-dark me-2"></i> Card Holder Name <small class="text-muted">(Required)</small></span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" checked disabled>
                                    <input type="hidden" name="show_name" value="1">
                                </div>
                            </div>

                            <!-- 2. Nick Name (Database 'owner_name' field) -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-id-badge text-primary me-2"></i> Nick Name</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_nick_name" value="1" {{ isset($card) && $card->show_nick_name ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- 3. Profile Photo / Logo -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-image text-primary me-2"></i> Profile Photo / Logo</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_profile_image" value="1" {{ isset($card) && $card->show_profile_image ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- 4. Business & Tagline -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-building text-primary me-2"></i> Business / Company Name</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_business" value="1" {{ isset($card) && $card->show_business ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-tag text-primary me-2"></i> Tagline / Designation</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_tagline" value="1" {{ isset($card) && $card->show_tagline ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- 5. Contact Details -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-phone text-success me-2"></i> Primary Phone Number</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_phone" value="1" {{ isset($card) && $card->show_phone ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-phone-alt text-success me-2"></i> Alternate Phone Number</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_alt_phone" value="1" {{ isset($card) && $card->show_alt_phone ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fab fa-whatsapp text-success me-2"></i> WhatsApp Number</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_whatsapp" value="1" {{ isset($card) && $card->show_whatsapp ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- 6. Emails -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-envelope text-danger me-2"></i> Gmail / Primary Email</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_gmail" value="1" {{ isset($card) && $card->show_gmail ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-envelope text-warning me-2"></i> Yahoo Email</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_yahoo_email" value="1" {{ isset($card) && $card->show_yahoo_email ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-envelope-open text-secondary me-2"></i> Other Email</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_other_email" value="1" {{ isset($card) && $card->show_other_email ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- 7. Social Links -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fab fa-facebook text-primary me-2"></i> Facebook URL</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_facebook" value="1" {{ isset($card) && $card->show_facebook ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fab fa-instagram text-danger me-2"></i> Instagram URL</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_instagram" value="1" {{ isset($card) && $card->show_instagram ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fab fa-twitter text-info me-2"></i> Twitter / X URL</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_twitter_x" value="1" {{ isset($card) && $card->show_twitter_x ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fab fa-linkedin text-primary me-2"></i> LinkedIn URL</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_linkedin" value="1" {{ isset($card) && $card->show_linkedin ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fab fa-youtube text-danger me-2"></i> YouTube Channel</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_youtube" value="1" {{ isset($card) && $card->show_youtube ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fab fa-telegram text-info me-2"></i> Telegram</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_telegram" value="1" {{ isset($card) && $card->show_telegram ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-globe text-secondary me-2"></i> Website</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_website" value="1" {{ isset($card) && $card->show_website ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- 8. Payment & UPI -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-wallet text-purple me-2"></i> PhonePe / UPI</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_phonepe" value="1" {{ isset($card) && $card->show_phonepe ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-wallet text-success me-2"></i> Google Pay (GPay)</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_gpay" value="1" {{ isset($card) && $card->show_gpay ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-wallet text-info me-2"></i> Paytm</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_paytm" value="1" {{ isset($card) && $card->show_paytm ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-qrcode text-dark me-2"></i> Payment QR Code</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_qr_code" value="1" {{ isset($card) && $card->show_qr_code ? 'checked' : '' }}>
                                </div>
                            </div>

                            <!-- 9. About & Other -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-info-circle text-primary me-2"></i> About Us / Profile</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_about_us" value="1" {{ isset($card) && $card->show_about_us ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-concierge-bell text-success me-2"></i> Other</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_services" value="1" {{ isset($card) && $card->show_services ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-map-marker-alt text-danger me-2"></i> Address</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_address" value="1" {{ isset($card) && $card->show_address ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-map text-warning me-2"></i> Google Map Link</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="show_map" value="1" {{ isset($card) && $card->show_map ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">Save Display Preferences</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection