<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 tracking-wide">Verify OTP & Reset</h2>
        <p class="text-sm text-gray-600 mt-1 font-medium">Enter the OTP sent to your mobile number and set a new password</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Alert Box -->
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 text-sm rounded-xl font-medium text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.reset.mobile') }}">
        @csrf
        <input type="hidden" name="mobile" value="{{ $mobile }}">

        <!-- OTP Input -->
        <div>
            <label for="otp" class="block font-semibold text-sm text-gray-800">Enter OTP</label>
            <input id="otp" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="text" name="otp" required autofocus maxlength="4" placeholder="4-digit OTP" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2 text-red-600" />
        </div>

        <!-- New Password -->
        <div class="mt-4">
            <label for="password" class="block font-semibold text-sm text-gray-800">New Password</label>
            <input id="password" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="password" name="password" required placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block font-semibold text-sm text-gray-800">Confirm Password</label>
            <input id="password_confirmation" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="password" name="password_confirmation" required placeholder="••••••••" />
        </div>

        <!-- Action Button -->
        <div class="mt-6">
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition duration-200">
                RESET PASSWORD
            </button>
        </div>
    </form>
</x-guest-layout>