<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 tracking-wide">Create Account</h2>
        <p class="text-sm text-gray-600 mt-1 font-medium">Register as User, Merchant or Service Partner</p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block font-semibold text-sm text-gray-800">Name / Business Name</label>
            <input id="name" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="text" name="name" :value="old('name')" required autofocus placeholder="Enter full name or firm name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <label for="email" class="block font-semibold text-sm text-gray-800">Email</label>
            <input id="email" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="email" name="email" :value="old('email')" required placeholder="example@mail.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
        </div>

        <!-- Mobile Number -->
        <div class="mt-4">
            <label for="mobile" class="block font-semibold text-sm text-gray-800">Mobile Number</label>
            <input id="mobile" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="text" name="mobile" :value="old('mobile')" required placeholder="Enter mobile number" />
            <x-input-error :messages="$errors->get('mobile')" class="mt-2 text-red-600" />
        </div>

        <!-- Username -->
        <div class="mt-4">
            <label for="username" class="block font-semibold text-sm text-gray-800">Username</label>
            <input id="username" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="text" name="username" :value="old('username')" placeholder="Enter username (optional)" />
            <x-input-error :messages="$errors->get('username')" class="mt-2 text-red-600" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <label for="role" class="block font-semibold text-sm text-gray-800">Select Account Type</label>
            <select id="role" name="role" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" required onchange="handleRoleChange(this.value)">
                <option value="member" {{ old('role') == 'member' ? 'selected' : '' }}>Member / Customer / Tourist</option>
                <option value="business" {{ old('role') == 'business' ? 'selected' : '' }}>Business / Service Partner</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2 text-red-600" />
        </div>

        <!-- Dynamic Member / Sponsor Section (Only for Members / Binary MLM Network) -->
        <div id="member-fields-container" class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-xl">
            <div class="mb-3">
                <label for="sponsor_referral_id" class="block font-semibold text-sm text-gray-800">Sponsor Referral ID <span class="text-xs text-gray-500 font-normal">(Optional, defaults to root ID if left blank)</span></label>
                <input id="sponsor_referral_id" class="block mt-1 w-full rounded-lg border border-gray-300 bg-white py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="text" name="sponsor_referral_id" value="{{ old('sponsor_referral_id', 'TDMS6395GSSS') }}" placeholder="Enter sponsor referral ID" />
                <x-input-error :messages="$errors->get('sponsor_referral_id')" class="mt-2 text-red-600" />
            </div>

            <!-- Binary Position Toggle Switch -->
            <div>
                <label class="block font-semibold text-sm text-gray-800 mb-1">Binary Position (Leg)</label>
                <div class="flex items-center bg-white border border-gray-300 rounded-lg p-1 w-full">
                    <label class="flex-1 text-center py-2 rounded-md cursor-pointer text-sm font-semibold transition-all duration-200 bg-blue-600 text-white shadow-sm" id="label-left">
                        <input type="radio" name="position" value="left" class="hidden" checked onchange="updateToggleStyle()">
                        Left Leg (A)
                    </label>
                    <label class="flex-1 text-center py-2 rounded-md cursor-pointer text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-gray-100" id="label-right">
                        <input type="radio" name="position" value="right" class="hidden" onchange="updateToggleStyle()">
                        Right Leg (B)
                    </label>
                </div>
                <x-input-error :messages="$errors->get('position')" class="mt-2 text-red-600" />
            </div>
        </div>

        <!-- Dynamic Business Type Section -->
        <div id="vendor-fields-container" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl" style="display: none;">
            <div class="mb-3">
                <label for="business_type" class="block font-semibold text-sm text-gray-800">Select Your Business Service <span class="text-red-600">*</span></label>
                <select name="business_type" id="business_type" class="form-select rounded-lg border border-gray-300 bg-white py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 w-full" disabled onchange="handleBusinessTypeChange(this.value)">
                    <option value="" disabled selected>-- Select Service Type --</option>
                    
                    <optgroup label="Food & Hospitality">
                        <option value="restaurant">Restaurant (Dine-in / KDS / POS / Tiffin / Street Food)</option>
                        <option value="catering">Catering Service (Event & Bulk Food)</option>
                        <option value="cafe_icecream">Cafe & Ice Cream Parlour</option>
                        <option value="bakery">Bakery & Cake Shop</option>
                        <option value="hotel">Hotel / Resort / Guest House</option>
                        <option value="homestay">Homestay / PG Accommodations</option>
                    </optgroup>

                    <optgroup label="Events, Venue & Media">
                        <option value="marriage_home">Marriage Home / Banquet Hall / Garden</option>
                        <option value="event_planner">Event & Wedding Planner</option>
                        <option value="tent_decoration">Tent House & Decoration</option>
                        <option value="photography">Photography & Videography</option>
                        <option value="dj_sound">DJ & Sound System</option>
                    </optgroup>

                    <optgroup label="Transport & Travel">
                        <option value="taxi">Taxi / Cab Service</option>
                        <option value="bike_rental">Bike / Scooter Rental</option>
                        <option value="tourist_guide">Tourist Guide</option>
                        <option value="travel_agency">Travel & Tour Operator</option>
                    </optgroup>

                    <optgroup label="Retail & Shopping">
                        <option value="emporium">Handicraft & Emporium</option>
                        <option value="grocery">Grocery & Supermarket</option>
                        <option value="clothing">Clothing & Fashion Store</option>
                    </optgroup>

                    <optgroup label="Health, Wellness & Beauty">
                        <option value="salon_spa">Salon, Spa & Beauty Parlour</option>
                        <option value="gym_fitness">Gym & Fitness Center</option>
                        <option value="medical_pharmacy">Medical / Pharmacy</option>
                    </optgroup>

                    <optgroup label="Financial & Professional Services">
                        <option value="money_exchange">Money Exchange (Forex)</option>
                        <option value="real_estate">Real Estate & Property Dealer</option>
                        <option value="coaching_tuition">Coaching & Education Institute</option>
                        <option value="local_services">Local Utility & Support Services</option>
                    </optgroup>
                </select>
                <x-input-error :messages="$errors->get('business_type')" class="mt-2 text-red-600" />
            </div>

            <!-- Taxi & Rental Vehicle Field -->
            <div id="vehicle-field" class="mt-3" style="display: none;">
                <label for="vehicle_no" class="block font-semibold text-xs text-gray-700">Vehicle Number / Registration</label>
                <input id="vehicle_no" name="vehicle_no" type="text" class="block mt-1 w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm" placeholder="e.g. RJ-14-TA-1234" />
            </div>

            <!-- License Field for Regulated Services -->
            <div id="license-field" class="mt-3" style="display: none;">
                <label for="license_no" class="block font-semibold text-xs text-gray-700">Govt License / FSSAI / Reg. Number</label>
                <input id="license_no" name="license_no" type="text" class="block mt-1 w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm" placeholder="e.g. FSSAI / LIC-2026-8890" />
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-semibold text-sm text-gray-800">Password</label>
            <input id="password" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block font-semibold text-sm text-gray-800">Confirm Password</label>
            <input id="password_confirmation" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
        </div>

        <!-- Terms & Conditions Checkbox -->
        <div class="mt-4 flex items-center">
            <input type="checkbox" name="terms" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" id="terms" required>
            <label class="ms-2 text-sm text-gray-600" for="terms">
                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms & Conditions</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
            </label>
        </div>

        <!-- Register Button -->
        <div class="mt-6">
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition duration-200">
                REGISTER NOW
            </button>
        </div>

        <div class="text-center mt-5 text-sm text-gray-600 font-medium">
            Already registered? 
            <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800 underline ms-1">
                Log In Here
            </a>
        </div>
    </form>

    <script>
    function updateToggleStyle() {
        const leftRadio = document.querySelector('input[name="position"][value="left"]');
        const rightRadio = document.querySelector('input[name="position"][value="right"]');
        const labelLeft = document.getElementById('label-left');
        const labelRight = document.getElementById('label-right');

        if (leftRadio.checked) {
            labelLeft.className = "flex-1 text-center py-2 rounded-md cursor-pointer text-sm font-semibold transition-all duration-200 bg-blue-600 text-white shadow-sm";
            labelRight.className = "flex-1 text-center py-2 rounded-md cursor-pointer text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-gray-100";
        } else {
            labelRight.className = "flex-1 text-center py-2 rounded-md cursor-pointer text-sm font-semibold transition-all duration-200 bg-blue-600 text-white shadow-sm";
            labelLeft.className = "flex-1 text-center py-2 rounded-md cursor-pointer text-sm font-semibold transition-all duration-200 text-gray-700 hover:bg-gray-100";
        }
    }

    function handleRoleChange(role) {
        const vendorContainer = document.getElementById('vendor-fields-container');
        const memberContainer = document.getElementById('member-fields-container');
        const businessSelect = document.getElementById('business_type');
        const sponsorInput = document.getElementById('sponsor_referral_id');
        
        if (role === 'business') {
            vendorContainer.style.display = 'block';
            memberContainer.style.display = 'none';
            if (businessSelect) {
                businessSelect.disabled = false;
                businessSelect.setAttribute('required', 'required');
                if (!businessSelect.value) {
                    businessSelect.value = 'restaurant';
                }
                handleBusinessTypeChange(businessSelect.value);
            }
            if (sponsorInput) sponsorInput.removeAttribute('required');
        } else {
            vendorContainer.style.display = 'none';
            memberContainer.style.display = 'block';
            if (businessSelect) {
                businessSelect.removeAttribute('required');
                businessSelect.disabled = true;
            }
            if (sponsorInput && !sponsorInput.value) {
                sponsorInput.value = 'TDMS6395GSSS'; // Default fallback ID
            }

            document.getElementById('vehicle-field').style.display = 'none';
            document.getElementById('license-field').style.display = 'none';
        }
    }

    function handleBusinessTypeChange(type) {
        const vehicleField = document.getElementById('vehicle-field');
        const licenseField = document.getElementById('license-field');

        const vehicleTypes = ['taxi', 'bike_rental'];
        const licenseTypes = [
            'money_exchange', 'tourist_guide', 'bakery', 'cafe_icecream', 'catering', 'marriage_home', 'medical_pharmacy'
        ];

        if (vehicleField) {
            vehicleField.style.display = vehicleTypes.includes(type) ? 'block' : 'none';
        }
        if (licenseField) {
            licenseField.style.display = licenseTypes.includes(type) ? 'block' : 'none';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const roleSelect = document.getElementById('role');
        if (roleSelect) {
            handleRoleChange(roleSelect.value);
        }
        updateToggleStyle();
    });
    </script>
</x-guest-layout>
```[cite: 2]

### Controller (`AuthController.php`) mein bhi ek chota sa badlaav karna hoga:
Agar user ne sponsor ID khali chhod di ho, toh controller usko automatically `TDMS6395GSSS` maan le. Iske liye `AuthController.php` ke `register` method mein validation rule ko thoda sa update kar dein[cite: 2]:

```php
        $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'username'            => ['nullable', 'string', 'max:255', 'unique:users'],
            'email'               => ['required', 'email', 'unique:users'],
            'mobile'              => ['required', 'string', 'max:15', 'unique:users'],
            'password'            => ['required', 'min:6'],
            'role'                => ['required', 'in:admin,employee,business,member,vendor'],
            'business_type'       => ['nullable', 'string', 'max:255'],
            'sponsor_referral_id' => ['nullable', 'string', 'exists:users,referral_id'],
            'position'            => ['required_if:role,member', 'string', 'in:left,right'],
        ]);

        // Agar sponsor_referral_id khali hai toh default set kar dein
        $sponsorReferralId = $request->sponsor_referral_id ?: 'TDMS6395GSSS';