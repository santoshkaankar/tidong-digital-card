<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 tracking-wide">Create Account</h2>
        <p class="text-sm text-gray-600 mt-1 font-medium">Get your digital identity card in seconds</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block font-semibold text-sm text-gray-800">Name</label>
            <input id="name" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter full name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <label for="email" class="block font-semibold text-sm text-gray-800">Email</label>
            <input id="email" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="example@mail.com" />
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
            <input id="username" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="text" name="username" :value="old('username')" required placeholder="Enter username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2 text-red-600" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <label for="role" class="block font-semibold text-sm text-gray-800">Select Role</label>
            <select id="role" name="role" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" required onchange="toggleBusinessType(this)">
                <option value="member" {{ old('role') == 'member' ? 'selected' : '' }}>User / Member</option>
                <option value="business" {{ old('role') == 'business' ? 'selected' : '' }}>Business</option>
                <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2 text-red-600" />
        </div>

        <!-- Business Type Selection -->
        <div class="mt-4" id="business-type-container" style="display: none;">
            <label for="business_type" class="block font-semibold text-sm text-gray-800">Business Type</label>
            <select id="business_type" name="business_type" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20">
                <option value="">-- Select Business Category --</option>
                @php
                    $categories = [];
                    if (\Illuminate\Support\Facades\Schema::hasTable('vendor_categories')) {
                        $categories = \DB::table('vendor_categories')->get();
                    }
                @endphp

                @foreach($categories as $cat)
                    <option value="{{ $cat->name ?? $cat->slug }}" {{ old('business_type') == ($cat->name ?? $cat->slug) ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach

                <option value="other" {{ old('business_type') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <x-input-error :messages="$errors->get('business_type')" class="mt-2 text-red-600" />
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
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600" />
        </div>

        <!-- Terms Checkbox -->
        <div class="mt-4 block">
            <label for="terms" class="inline-flex items-center cursor-pointer">
                <input id="terms" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="terms" required>
                <span class="ms-2 text-xs text-gray-700 font-medium">
                    I agree to the <a href="#" target="_blank" class="underline text-blue-600 hover:text-blue-800">Terms & Conditions</a> and <a href="#" target="_blank" class="underline text-blue-600 hover:text-blue-800">Privacy Policy</a>
                </span>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-2 text-red-600" />
        </div>

        <!-- Visible Solid Blue Register Button -->
        <div class="mt-6">
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition duration-200">
                REGISTER
            </button>
        </div>

        <!-- Already Registered Link -->
        <div class="text-center mt-5 text-sm text-gray-600 font-medium">
            Already registered? 
            <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800 underline ms-1">
                Log In Here
            </a>
        </div>
    </form>

    <script>
        function toggleBusinessType(select) {
            const container = document.getElementById('business-type-container');
            if (select.value === 'business') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
                document.getElementById('business_type').value = '';
            }
        }
        
        document.addEventListener("DOMContentLoaded", function() {
            const roleSelect = document.getElementById('role');
            if (roleSelect && roleSelect.value === 'business') {
                document.getElementById('business-type-container').style.display = 'block';
            }
        });
    </script>
</x-guest-layout>