<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 tracking-wide">Reset Password</h2>
        <p class="text-sm text-gray-600 mt-1 font-medium">Create a new secure password for your account</p>
    </div>

    <!-- Error Alert Box -->
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 text-sm rounded-xl font-medium text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $token ?? request()->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-semibold text-sm text-gray-800">Email Address</label>
            <input id="email" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-100 py-2.5 px-3 text-gray-900 text-sm" type="email" name="email" value="{{ old('email', request()->email) }}" required readonly />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
        </div>

        <!-- New Password -->
        <div class="mt-4">
            <label for="password" class="block font-semibold text-sm text-gray-800">New Password</label>
            <input id="password" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" autofocus />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block font-semibold text-sm text-gray-800">Confirm Password</label>
            <input id="password_confirmation" class="block mt-1 w-full rounded-lg border border-gray-300 bg-gray-50 py-2.5 px-3 text-gray-900 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-500/20" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600" />
        </div>

        <!-- Action Button -->
        <div class="mt-6">
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition duration-200">
                RESET PASSWORD
            </button>
        </div>
    </form>
</x-guest-layout>