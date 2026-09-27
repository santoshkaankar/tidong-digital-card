<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 tracking-wide">Forgot Password?</h2>
        <p class="text-sm text-gray-600 mt-1 font-medium">Enter your registered mobile number to receive an OTP</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Alert Box -->
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 text-sm rounded-xl font-medium text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.send.otp') }}">
        @csrf

        <!-- Mobile Number -->
        <div>
            <label for="mobile" class="block font-semibold text-sm text-gray-800">Mobile Number</label>
            <input id="mobile" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="text" name="mobile" :value="old('mobile')" required autofocus maxlength="10" placeholder="Enter 10-digit mobile number" />
            <x-input-error :messages="$errors->get('mobile')" class="mt-2 text-red-600" />
        </div>

        <!-- Action Button -->
        <div class="mt-6">
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition duration-200">
                SEND OTP
            </button>
        </div>

        <!-- Back to Login -->
        <div class="text-center mt-6 text-sm text-gray-600 font-medium">
            Remembered your password? 
            <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-800 underline ms-1">
                Log In Here
            </a>
        </div>
    </form>
</x-guest-layout>