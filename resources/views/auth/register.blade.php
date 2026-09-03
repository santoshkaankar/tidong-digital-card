<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 tracking-wide">Create Account</h2>
        <p class="text-sm text-gray-600 mt-1 font-medium">Register as User, Merchant or Service Partner</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
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

        <!-- Dynamic Business Type Section -->
        <div id="vendor-fields-container" class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl" style="display: none;">
            <div class="mb-3">
                <label for="business_type" class="block font-semibold text-sm text-gray-800">Select Your Business Service <span class="text-red-600">*</span></label>
                <select name="business_type" id="business_type" class="form-select rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20 w-full" required onchange="handleBusinessTypeChange(this.value)">
                    <option value="" disabled selected>-- Select Service Type --</option>
                    <option value="food">Food & Restaurant</option>
                    <option value="Hotel">Hotel / Stay</option>
                    <option value="emporium">Emporium</option>
                    <option value="taxi">Taxi / Cab Service</option>
                    <option value="money_exchange">Money Exchange</option>
                    <option value="guide">Tourist Guide</option>
                </select>
                <x-input-error :messages="$errors->get('business_type')" class="mt-2 text-red-600" />
            </div>

            <!-- Taxi Vehicle Field -->
            <div id="vehicle-field" class="mt-3" style="display: none;">
                <label for="vehicle_no" class="block font-semibold text-sm text-gray-800">Vehicle Number / Permit</label>
                <input id="vehicle_no" name="vehicle_no" type="text" class="block mt-1 w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm" placeholder="e.g. RJ-14-TA-1234" />
            </div>

            <!-- License Field for Guide & Exchange -->
            <div id="license-field" class="mt-3" style="display: none;">
                <label for="license_no" class="block font-semibold text-sm text-gray-800">Govt License / Reg. Number</label>
                <input id="license_no" name="license_no" type="text" class="block mt-1 w-full rounded-lg border border-gray-300 bg-white py-2 px-3 text-sm" placeholder="e.g. LIC-2026-8890" />
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
    function handleRoleChange(role) {
        const container = document.getElementById('vendor-fields-container');
        const businessSelect = document.getElementById('business_type');
        
        if (role === 'business') {
            container.style.display = 'block';
            if (businessSelect) {
                businessSelect.disabled = false;
                businessSelect.setAttribute('required', 'required');
                if (!businessSelect.value) {
                    businessSelect.value = 'food';
                }
                handleBusinessTypeChange(businessSelect.value);
            }
        } else {
            container.style.display = 'none';
            if (businessSelect) {
                businessSelect.removeAttribute('required');
                businessSelect.disabled = true;
            }
            document.getElementById('vehicle-field').style.display = 'none';
            document.getElementById('license-field').style.display = 'none';
        }
    }

    function handleBusinessTypeChange(type) {
        const vehicleField = document.getElementById('vehicle-field');
        const licenseField = document.getElementById('license-field');

        if (vehicleField) {
            vehicleField.style.display = (type === 'taxi') ? 'block' : 'none';
        }
        if (licenseField) {
            licenseField.style.display = (type === 'money_exchange' || type === 'guide') ? 'block' : 'none';
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const roleSelect = document.getElementById('role');
        if (roleSelect) {
            handleRoleChange(roleSelect.value);
        }
    });
    </script>
</x-guest-layout>