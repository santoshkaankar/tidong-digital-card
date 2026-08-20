<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Alert Box -->
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 text-sm rounded-xl font-medium text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 tracking-wide">Welcome Back!</h2>
        <p class="text-sm text-gray-600 mt-1 font-medium">Log in to manage your digital card & catalogs</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email, Mobile, or Username -->
        <div>
            <label for="login" class="block font-semibold text-sm text-gray-800">Email, Mobile, or Username</label>
            <input id="login" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" placeholder="Enter email, mobile, or username" />
            <x-input-error :messages="$errors->get('login')" class="mt-2 text-red-600" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-semibold text-sm text-gray-800">Password</label>
            <input id="password" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <!-- Remember Me & Safe Forgot Password Link -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-700 font-medium">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-600 hover:text-blue-800 font-bold transition" href="{{ route('password.request') }}">
                    Forgot Password?
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div class="mt-6">
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition duration-200">
                LOG IN
            </button>
        </div>

        <!-- Register Link -->
        <div class="text-center mt-6 text-sm text-gray-600 font-medium">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-800 underline ms-1">
                Register Now
            </a>
        </div>
    </form>
</x-guest-layout>