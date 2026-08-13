@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('member.cards.index') }}" class="btn btn-outline-dark btn-sm">🔙 Back to Cards</a>
        <h4 class="mb-0 fw-bold">Customize Your Visiting Card Display</h4>
        <div></div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <p class="text-muted mb-4">Aap apne card par jo-jo fields/icons show karna chahte hain, unhe yahan se tick / enable karein:</p>

            <form action="{{ route('member.card.update.display', $card->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- General & Business Info (Owner Name is Fixed & Locked as requested) -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Owner / Card Holder Name</h6>
                                <small class="text-muted">{{ $card->name }} (Always Enabled)</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input type="hidden" name="show_name" value="1">
                                <input class="form-check-input" type="checkbox" role="switch" value="1" checked disabled>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Business Name & Tagline</h6>
                                <small class="text-muted">{{ $card->business_name ?? 'Not Provided' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_business" value="1" {{ isset($card->show_business) && $card->show_business ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Tagline</h6>
                                <small class="text-muted">{{ $card->tagline ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_tagline" value="1" {{ isset($card->show_tagline) && $card->show_tagline ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Phone Number</h6>
                                <small class="text-muted">{{ $card->phone ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_phone" value="1" {{ isset($card->show_phone) && $card->show_phone ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Alternate Phone</h6>
                                <small class="text-muted">{{ $card->alt_phone ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_alt_phone" value="1" {{ isset($card->show_alt_phone) && $card->show_alt_phone ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">WhatsApp</h6>
                                <small class="text-muted">{{ $card->whatsapp ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_whatsapp" value="1" {{ isset($card->show_whatsapp) && $card->show_whatsapp ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <!-- Emails -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Gmail</h6>
                                <small class="text-muted">{{ $card->gmail ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_gmail" value="1" {{ isset($card->show_gmail) && $card->show_gmail ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Yahoo Email</h6>
                                <small class="text-muted">{{ $card->yahoo_email ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_yahoo_email" value="1" {{ isset($card->show_yahoo_email) && $card->show_yahoo_email ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Other Email</h6>
                                <small class="text-muted">{{ $card->other_email ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_other_email" value="1" {{ isset($card->show_other_email) && $card->show_other_email ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Facebook</h6>
                                <small class="text-muted">{{ $card->facebook ? 'Provided' : 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_facebook" value="1" {{ isset($card->show_facebook) && $card->show_facebook ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Instagram</h6>
                                <small class="text-muted">{{ $card->instagram ? 'Provided' : 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_instagram" value="1" {{ isset($card->show_instagram) && $card->show_instagram ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Twitter / X</h6>
                                <small class="text-muted">{{ $card->twitter_x ? 'Provided' : 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_twitter_x" value="1" {{ isset($card->show_twitter_x) && $card->show_twitter_x ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">LinkedIn</h6>
                                <small class="text-muted">{{ $card->linkedin ? 'Provided' : 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_linkedin" value="1" {{ isset($card->show_linkedin) && $card->show_linkedin ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">YouTube</h6>
                                <small class="text-muted">{{ $card->youtube ? 'Provided' : 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_youtube" value="1" {{ isset($card->show_youtube) && $card->show_youtube ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Telegram</h6>
                                <small class="text-muted">{{ $card->telegram ? 'Provided' : 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_telegram" value="1" {{ isset($card->show_telegram) && $card->show_telegram ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Website Link</h6>
                                <small class="text-muted">{{ $card->website_link ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_website" value="1" {{ isset($card->show_website) && $card->show_website ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <!-- Payments / UPI -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">PhonePe</h6>
                                <small class="text-muted">{{ $card->phonepe ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_phonepe" value="1" {{ isset($card->show_phonepe) && $card->show_phonepe ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Google Pay (GPay)</h6>
                                <small class="text-muted">{{ $card->gpay ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_gpay" value="1" {{ isset($card->show_gpay) && $card->show_gpay ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Paytm</h6>
                                <small class="text-muted">{{ $card->paytm ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_paytm" value="1" {{ isset($card->show_paytm) && $card->show_paytm ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">UPI ID</h6>
                                <small class="text-muted">{{ $card->upi_id ?? 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_upi" value="1" {{ isset($card->show_upi) && $card->show_upi ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <!-- Content Sections -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">About Us</h6>
                                <small class="text-muted">{{ $card->about_us ? 'Provided' : 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_about_us" value="1" {{ isset($card->show_about_us) && $card->show_about_us ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Services / Products</h6>
                                <small class="text-muted">{{ $card->services_or_products ? 'Provided' : 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_services" value="1" {{ isset($card->show_services) && $card->show_services ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <!-- Media & Address -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Photo</h6>
                                <small class="text-muted">{{ $card->photo ? 'Uploaded' : 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_photo" value="1" {{ isset($card->show_photo) && $card->show_photo ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">QR Code</h6>
                                <small class="text-muted">{{ $card->qr_code ? 'Uploaded' : 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_qr_code" value="1" {{ isset($card->show_qr_code) && $card->show_qr_code ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Address, Area, City, State</h6>
                                <small class="text-muted">{{ $card->city ?? '' }}{{ ($card->city && $card->state) ? ', ' : '' }}{{ $card->state ?? '' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_address" value="1" {{ isset($card->show_address) && $card->show_address ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">Map Location Link</h6>
                                <small class="text-muted">{{ $card->map_location_link ? 'Provided' : 'N/A' }}</small>
                            </div>
                            <div class="form-check form-switch fs-5">
                                <input class="form-check-input" type="checkbox" name="show_map" value="1" {{ isset($card->show_map) && $card->show_map ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-5 py-2 fw-bold">Save Preferences & View Card ✅</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection