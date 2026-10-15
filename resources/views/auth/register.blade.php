<x-guest-layout>
    <!-- Back to Home Button -->
    <div class="mb-4">
        <a href="{{ url('/') }}" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition">
            <i class="fas fa-arrow-left me-2"></i> &larr; Back to Home Page
        </a>
    </div>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 tracking-wide">Create Account</h2>
        <p class="text-sm text-gray-600 mt-1 font-medium">Register as Member, Business Partner or Delivery Service</p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name / Firm Name -->
        <div>
            <label for="name" class="block font-semibold text-sm text-gray-800">Full Name / Firm Name</label>
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
                <option value="business" {{ old('role') == 'business' ? 'selected' : '' }}>Business Partner / Vendor</option>
                <option value="delivery" {{ old('role') == 'delivery' ? 'selected' : '' }}>Delivery Service</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2 text-red-600" />
        </div>

        <!-- Dynamic Member / Sponsor Section (Sirf Member ke liye dikhega) -->
        <div id="member-fields-container" class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-xl">
            <div class="mb-3">
                <label for="sponsor_referral_id" class="block font-semibold text-sm text-gray-800">Sponsor Referral ID <span class="text-xs text-gray-500 font-normal">(Optional)</span></label>
                <input id="sponsor_referral_id" class="block mt-1 w-full rounded-lg border border-gray-300 bg-white py-2.5 px-3 text-gray-900 text-sm" type="text" name="sponsor_referral_id" value="{{ old('sponsor_referral_id', 'TDMS6395GSSS') }}" placeholder="Enter sponsor referral ID" />
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
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-semibold text-sm text-gray-800">Password</label>
            <input id="password" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block font-semibold text-sm text-gray-800">Confirm Password</label>
            <input id="password_confirmation" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
        </div>

        <!-- Terms & Conditions -->
        <div class="mt-4 flex items-center">
            <input type="checkbox" name="terms" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" id="terms" required>
            <label class="ms-2 text-sm text-gray-600" for="terms">
                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms & Conditions</a>
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
        const memberContainer = document.getElementById('member-fields-container');
        if (role === 'member') {
            memberContainer.style.display = 'block';
        } else {
            memberContainer.style.display = 'none';
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