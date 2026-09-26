@extends('delivery.partials.layout')

@section('content')
<div class="max-w-2xl mx-auto px-3 sm:px-4 py-4 space-y-4">

    <!-- Header & Profile Card -->
    <div class="flex items-center justify-between bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="relative w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center overflow-hidden shrink-0">
                @if($user->profile_photo && file_exists(public_path($user->profile_photo)))
                    <img src="{{ asset($user->profile_photo) }}" class="w-full h-full object-cover">
                @else
                    <span class="text-indigo-600 font-bold text-lg">{{ strtoupper(substr($user->name ?? 'D', 0, 1)) }}</span>
                @endif
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-900 leading-snug">{{ $user->name }}</h2>
                <p class="text-[11px] font-medium text-slate-500">{{ $user->mobile ?? $user->email }}</p>
            </div>
        </div>

        <!-- KYC Status Badge -->
        @php
            $kycClass = match($user->kyc_status ?? 'not_verified') {
                'verified' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'pending'  => 'bg-amber-50 text-amber-700 border-amber-200',
                'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                default    => 'bg-slate-100 text-slate-600 border-slate-200',
            };
        @endphp
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider {{ $kycClass }}">
            KYC: {{ str_replace('_', ' ', $user->kyc_status ?? 'Not Verified') }}
        </span>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <div class="font-bold flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-rose-600"></i> Please fix the errors below:
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Form -->
    <form action="{{ route('delivery.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <!-- SECTION 1: Personal Information -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3.5">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                <i class="fas fa-user-circle text-indigo-500 text-xs"></i>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Personal Information</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Profile Photo</label>
                    <input type="file" name="profile_photo" accept="image/*" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Mobile Number</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" placeholder="Enter mobile number" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Gender</label>
                    <select name="gender" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Date of Birth (DOB)</label>
                    <input type="date" name="dob" value="{{ old('dob', $user->dob) }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>
            </div>
        </div>

        <!-- SECTION 2: Address & Location (Smart Dual Search) -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3.5">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-location-dot text-indigo-500 text-xs"></i>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Address & Location</h3>
                </div>
                <span class="text-[10px] text-indigo-600 font-semibold" id="pincodeStatus">Type PIN or Area Name to search</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Pincode Field -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Pincode</label>
                    <input type="text" id="pincodeInput" name="pincode" maxlength="6" value="{{ old('pincode', $user->pincode) }}" placeholder="e.g. 324001" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <!-- Area Search with Auto-Complete Datalist -->
                <div class="relative">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Area / Locality</label>
                    <input type="text" id="areaInput" name="area" list="areaList" value="{{ old('area', $user->area) }}" placeholder="Colony / Area / Post Office" autocomplete="off" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                    <datalist id="areaList"></datalist>
                </div>

                <!-- City / District -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">City / District</label>
                    <input type="text" id="cityInput" name="city" value="{{ old('city', $user->city) }}" placeholder="District / City" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <!-- State -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">State</label>
                    <input type="text" id="stateInput" name="state" value="{{ old('state', $user->state) }}" placeholder="State" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <!-- House No / Village / Street Address -->
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">House No. / Village / Street Address</label>
                    <textarea name="address" rows="2" placeholder="Enter complete address details" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">{{ old('address', $user->address) }}</textarea>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Vehicle & Driving License Details -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3.5">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                <i class="fas fa-motorcycle text-indigo-500 text-xs"></i>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Vehicle & License Details</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Vehicle Number (RC)</label>
                    <input type="text" name="vehicle_no" value="{{ old('vehicle_no', $user->vehicle_no) }}" placeholder="e.g. RJ20 AB 1234" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Driving License Number</label>
                    <input type="text" name="license_no" value="{{ old('license_no', $user->license_no) }}" placeholder="e.g. DL-1420110012345" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>
            </div>
        </div>

        <!-- SECTION 4: KYC & Identity Documents Upload -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3.5">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                <i class="fas fa-id-card text-indigo-500 text-xs"></i>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">KYC & Identity Verification</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- PAN Card -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">PAN Card Number</label>
                    <input type="text" name="pan_number" value="{{ old('pan_number', $user->pan_number) }}" placeholder="ABCDE1234F" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none uppercase">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">PAN Card Photo</label>
                    <input type="file" name="pan_image" accept="image/*" class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-slate-100 file:text-slate-700">
                </div>

                <!-- Aadhaar Card -->
                <div class="sm:col-span-2 border-t border-slate-100 pt-3">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Aadhaar Card Number</label>
                    <input type="text" name="aadhaar_number" maxlength="12" value="{{ old('aadhaar_number', $user->aadhaar_number) }}" placeholder="12-Digit Aadhaar Number" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Aadhaar Front Image</label>
                    <input type="file" name="aadhaar_front_image" accept="image/*" class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-slate-100 file:text-slate-700">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Aadhaar Back Image</label>
                    <input type="file" name="aadhaar_back_image" accept="image/*" class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-slate-100 file:text-slate-700">
                </div>
            </div>
        </div>

        <!-- SECTION 5: Bank & Settlement Details -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3.5">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                <i class="fas fa-building-columns text-indigo-500 text-xs"></i>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Bank & Settlement Details</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Account Holder Name</label>
                    <input type="text" name="account_holder_name" value="{{ old('account_holder_name', $user->account_holder_name) }}" placeholder="As per bank passbook" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" placeholder="e.g. SBI, HDFC" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Account Number</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $user->account_number) }}" placeholder="Account Number" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">IFSC Code</label>
                    <input type="text" name="ifsc_code" value="{{ old('ifsc_code', $user->ifsc_code) }}" placeholder="e.g. SBIN0001234" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none uppercase">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">UPI ID (Google Pay / PhonePe / Paytm)</label>
                    <input type="text" name="upi_id" value="{{ old('upi_id', $user->upi_id) }}" placeholder="username@upi" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>
            </div>
        </div>

        <!-- SECTION 6: Change Password -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3.5">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-lock text-indigo-500 text-xs"></i>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Account Security</h3>
                </div>
                <span class="text-[10px] text-slate-400">Leave blank to keep current password</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">New Password</label>
                    <input type="password" name="password" placeholder="New password" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" placeholder="Confirm password" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-indigo-500 outline-none">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white font-bold py-3.5 px-4 rounded-xl shadow-xs transition text-xs flex items-center justify-center gap-2">
            <i class="fas fa-save text-xs"></i>
            <span>Save Profile & Verification Info</span>
        </button>
    </form>
</div>

<!-- Dynamic Bi-directional Pincode & Area Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pincodeInput  = document.getElementById('pincodeInput');
    const areaInput     = document.getElementById('areaInput');
    const areaList      = document.getElementById('areaList');
    const cityInput     = document.getElementById('cityInput');
    const stateInput    = document.getElementById('stateInput');
    const pincodeStatus = document.getElementById('pincodeStatus');

    let areaCache = [];

    // 1. PINCODE ENTER PAR -> City, State & Area Options Auto-Fetch
    if (pincodeInput) {
        pincodeInput.addEventListener('input', function () {
            const pin = this.value.trim();
            if (pin.length === 6) {
                pincodeStatus.textContent = 'Fetching location details...';
                pincodeStatus.className = 'text-[10px] text-amber-600 font-semibold';

                fetch(`/delivery/pincode-lookup/${pin}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            if (data.district) cityInput.value = data.district;
                            if (data.state) stateInput.value = data.state;

                            areaList.innerHTML = '';
                            if (data.areas && data.areas.length > 0) {
                                data.areas.forEach(areaName => {
                                    const opt = document.createElement('option');
                                    opt.value = areaName;
                                    areaList.appendChild(opt);
                                });
                            }
                            pincodeStatus.textContent = 'Location matched!';
                            pincodeStatus.className = 'text-[10px] text-emerald-600 font-bold';
                        } else {
                            pincodeStatus.textContent = 'Invalid Pincode';
                            pincodeStatus.className = 'text-[10px] text-rose-500 font-bold';
                        }
                    })
                    .catch(() => {
                        pincodeStatus.textContent = 'Lookup failed';
                        pincodeStatus.className = 'text-[10px] text-rose-500 font-bold';
                    });
            }
        });
    }

    // 2. AREA SEARCH PAR -> Pincode, City & State Auto-Fetch (Reverse Lookup)
    if (areaInput) {
        areaInput.addEventListener('input', function () {
            const query = this.value.trim();

            // Check if selected option matches cached results
            const selectedMatch = areaCache.find(item => item.area === query || `${item.area} (${item.district})` === query);
            if (selectedMatch) {
                pincodeInput.value = selectedMatch.pincode;
                cityInput.value    = selectedMatch.district;
                stateInput.value   = selectedMatch.state;
                areaInput.value    = selectedMatch.area;
                pincodeStatus.textContent = 'Location auto-filled!';
                pincodeStatus.className = 'text-[10px] text-emerald-600 font-bold';
                return;
            }

            // At least 3 characters entered for live search
            if (query.length >= 3) {
                pincodeStatus.textContent = 'Searching areas...';
                pincodeStatus.className = 'text-[10px] text-amber-600 font-semibold';

                fetch(`/delivery/area-search?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        areaCache = data;
                        areaList.innerHTML = '';

                        if (data.length > 0) {
                            data.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item.area;
                                opt.textContent = `${item.area} - PIN: ${item.pincode} (${item.district})`;
                                areaList.appendChild(opt);
                            });
                            pincodeStatus.textContent = 'Select area from dropdown';
                            pincodeStatus.className = 'text-[10px] text-indigo-600 font-semibold';
                        } else {
                            pincodeStatus.textContent = 'No matching area found';
                            pincodeStatus.className = 'text-[10px] text-rose-500 font-semibold';
                        }
                    })
                    .catch(() => {
                        pincodeStatus.textContent = 'Search error';
                        pincodeStatus.className = 'text-[10px] text-rose-500 font-bold';
                    });
            }
        });
    }
});
</script>
@endsection