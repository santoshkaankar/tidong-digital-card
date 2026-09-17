@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-primary">Affiliate Program & Stage Structure</h2>
        <p class="text-muted">Poore 14 Stages, T&C, Shopping Limits aur Deductions ki poori jankari</p>
    </div>

    {{-- Rules Overview --}}
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <i class="bi bi-gift text-success fs-1 mb-2"></i>
                <h6 class="fw-bold">Registration Bonus</h6>
                <p class="small text-muted mb-0">Sign up par instant <strong>45,40,000 T-Coins</strong> (1 T-Coin = ₹1) prapt karein.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <i class="bi bi-cart-check text-primary fs-1 mb-2"></i>
                <h6 class="fw-bold">₹25,000 Shopping Limit</h6>
                <p class="small text-muted mb-0">Self aur Team members ki minimum ₹25k shopping par hi stage clear hoti hai.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <i class="bi bi-clock-history text-warning fs-1 mb-2"></i>
                <h6 class="fw-bold">Daily T-Coin Deduction</h6>
                <p class="small text-muted mb-0">14 minus (Completed Stages) = Daily deduction. Jitni jaldi stage poori karenge, deduction utna kam hoga.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 text-center p-3">
                <i class="bi bi-receipt text-danger fs-1 mb-2"></i>
                <h6 class="fw-bold">TDS & Admin Deductions</h6>
                <p class="small text-muted mb-0">10% Admin Charge + 5% TDS (PAN Verified) / 20% TDS (Without PAN) deduct hota hai.</p>
            </div>
        </div>
    </div>

    {{-- Stages Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white fw-bold py-3">All 14 Affiliate Stages</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover text-center align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Stage</th>
                            <th>Designation</th>
                            <th>Leg A Req.</th>
                            <th>Leg B Req.</th>
                            <th>Gross Incentive</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $stages = DB::table('affiliate_stages')->orderBy('stage_no', 'asc')->get();
                        @endphp
                        @foreach($stages as $stg)
                        <tr>
                            <td><span class="badge bg-primary">Stage {{ $stg->stage_no }}</span></td>
                            <td class="fw-bold text-start">{{ $stg->stage_name }}</td>
                            <td>{{ number_format($stg->leg_a_count) }}</td>
                            <td>{{ number_format($stg->leg_b_count) }}</td>
                            <td class="text-success fw-bold">₹{{ number_format($stg->incentive_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection