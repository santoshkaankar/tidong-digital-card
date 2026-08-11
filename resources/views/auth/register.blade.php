<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Select Role')" />
            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required onchange="toggleBusinessType(this)">
                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>User / Member</option>
                <option value="business" {{ old('role') == 'business' ? 'selected' : '' }}>Business</option>
                <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Business Type Selection (Only from Database Master + Other) -->
        <div class="mt-4" id="business-type-container" style="display: none;">
            <x-input-label for="business_type" :value="__('Business Type')" />
            <select id="business_type" name="business_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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

                <!-- Static 'Other' Option -->
                <option value="other" {{ old('business_type') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms & Conditions Checkbox -->
        <div class="mt-4 block">
            <label for="terms" class="inline-flex items-center">
                <input id="terms" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="terms" required>
                <span class="ms-2 text-sm text-gray-600">I agree to the <a href="#" target="_blank" class="underline text-indigo-600 hover:text-indigo-900">Terms & Conditions</a> and <a href="#" target="_blank" class="underline text-indigo-600 hover:text-indigo-900">Privacy Policy</a></span>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <!-- JavaScript to toggle Business Type field -->
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
        
        // Run on page load in case of old input validation failure
        document.addEventListener("DOMContentLoaded", function() {
            const roleSelect = document.getElementById('role');
            if (roleSelect && roleSelect.value === 'business') {
                document.getElementById('business-type-container').style.display = 'block';
            }
        });
    </script>
</x-guest-layout>