<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Welcome Back!</h2>
        <p class="text-sm text-gray-500">Log in to manage your digital card & catalogs</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email or Username -->
        <div>
            <x-input-label for="login_input" :value="__('Email or Username')" class="font-semibold text-gray-700" />
            <x-text-input id="login_input" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" type="text" name="login_input" :value="old('login_input')" required autofocus autocomplete="username" placeholder="Enter email or username" />
            <x-input-error :messages="$errors->get('login_input')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="font-semibold text-gray-700" />
            <x-text-input id="password" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password Link -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="underline text-sm text-blue-600 hover:text-blue-800 rounded-md focus:outline-none" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition duration-200 shadow-md">
                {{ __('LOG IN') }}
            </x-primary-button>
        </div>

        <!-- Register Link -->
        <div class="text-center mt-5 text-sm text-gray-600">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-800">
                Register Now
            </a>
        </div>
    </form>
</x-guest-layout>