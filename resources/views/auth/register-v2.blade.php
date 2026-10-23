<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account · TIDONG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
<main class="mx-auto flex min-h-screen max-w-5xl items-center justify-center p-4 sm:p-6">
 <section class="w-full overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg">
  <div class="grid lg:grid-cols-[0.8fr_1.2fr]">
   <aside class="bg-slate-900 px-6 py-8 text-white sm:px-10">
    <a href="{{ url('/') }}" class="inline-flex items-center rounded-xl bg-white p-3"><img src="{{ asset('assets/tidong-full.jpeg') }}" alt="TIDONG" class="h-9 w-auto max-w-full object-contain sm:h-12"></a>
    <h1 class="mt-10 text-3xl font-bold">Create your account</h1>
    <p class="mt-3 text-slate-300">One account for your role. Vendor outlets are created after registration from the Vendor dashboard.</p>
    <p class="mt-8 text-sm text-slate-300">Already registered?
      <a href="{{ route('login') }}" class="font-semibold text-white underline underline-offset-4">Login</a>
    </p>
   </aside>
   <div class="px-5 py-7 sm:px-9 sm:py-9">
    @if (session('success')) <p role="status" class="mb-4 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</p> @endif
    @if ($errors->any())
      <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
       <strong>Check the following fields:</strong><ul class="mt-2 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif
    <form action="{{ route('register.store') }}" method="POST" class="grid gap-4 sm:grid-cols-2" id="registration-form">
     @csrf
     <div class="sm:col-span-2"><label class="mb-1 block text-sm font-semibold" for="name">1. Full Name *</label>
       <input class="w-full rounded-xl border p-3" id="name" name="name" autocomplete="name" maxlength="255" required value="{{ old('name') }}" placeholder="Your full name"></div>
     <div><label class="mb-1 block text-sm font-semibold" for="mobile">2. Mobile Number *</label>
       <input class="w-full rounded-xl border p-3" id="mobile" name="mobile" type="tel" inputmode="tel" required autocomplete="tel" value="{{ old('mobile') }}" placeholder="Mobile with country code"></div>
     <div><label class="mb-1 block text-sm font-semibold" for="email">3. Email Address *</label>
       <input class="w-full rounded-xl border p-3" id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" placeholder="you@example.com"></div>
     <div class="sm:col-span-2"><label class="mb-1 block text-sm font-semibold" for="username">4. Username *</label>
       <input class="w-full rounded-xl border p-3" id="username" name="username" minlength="4" maxlength="30" pattern="[a-zA-Z][a-zA-Z0-9._-]+" required autocomplete="username" value="{{ old('username') }}" placeholder="Unique username">
       <p class="mt-1 text-xs text-slate-500">At least 4 characters, starting with a letter.</p>
     </div>
     <div class="sm:col-span-2"><label class="mb-1 block text-sm font-semibold" for="account_type">5. Account Type *</label>
       <select class="w-full rounded-xl border bg-white p-3" id="account_type" name="account_type" required>
        <option value="">Choose account type</option>
        @foreach(['vendor'=>'Vendor','delivery'=>'Delivery Partner','professional'=>'Professional','service_provider'=>'Service Provider','customer'=>'Customer / Member'] as $code => $label)
         <option value="{{ $code }}" @selected(old('account_type') === $code) @disabled(!in_array($code, $registrationTypes, true))>{{ $label }}{{ !in_array($code, $registrationTypes, true) ? ' — Unavailable' : '' }}</option>
        @endforeach
       </select><p class="mt-1 text-xs text-slate-500">Register here as a Vendor, not as a business or outlet. You can add businesses and outlets later from your Vendor dashboard.</p>
     </div>
     <div id="vendor-name-field" class="hidden sm:col-span-2"><label class="mb-1 block text-sm font-semibold" for="vendor_name">Vendor Name *</label>
       <input class="w-full rounded-xl border p-3" id="vendor_name" name="vendor_name" maxlength="255" value="{{ old('vendor_name') }}" placeholder="Registered vendor name" disabled>
       <p class="mt-1 text-xs text-slate-500">Register the Vendor under its own name. Businesses/outlets are created separately after approval.</p></div>
     <fieldset id="customer-referral" class="hidden space-y-3 rounded-2xl border border-teal-200 bg-teal-50 p-4 sm:col-span-2" aria-label="Customer referral settings">
       <legend class="px-2 text-sm font-semibold">Customer Referral & Placement</legend>
       <div><label class="mb-1 block text-sm font-semibold" for="referral_code">Referral ID</label>
         <input class="w-full rounded-xl border bg-white p-3" id="referral_code" name="referral_code" maxlength="16" value="{{ old('referral_code', $referralCode ?? '') }}" @readonly($referralLinked) placeholder="Automatically filled when available">
         <p class="mt-1 text-xs text-slate-500">Referral links are verified on the server. Direct registrations use the first affiliate member's referral ID when configured.</p>
       </div>
       <div><span class="mb-2 block text-sm font-semibold">Placement Preference *</span>
        <div class="flex flex-wrap gap-4">
          <label class="flex cursor-pointer items-center gap-2"><input type="radio" name="leg" value="A" @checked(old('leg', $selectedLeg) === 'A')> Left</label>
          <label class="flex cursor-pointer items-center gap-2"><input type="radio" name="leg" value="B" @checked(old('leg', $selectedLeg) === 'B')> Right</label>
        </div>
        <p class="mt-1 text-xs text-slate-500">Placement is completed only after email verification and network eligibility checks.</p>
       </div>
     </fieldset>
     <div><label class="mb-1 block text-sm font-semibold" for="password">6. Password *</label>
       <input class="w-full rounded-xl border p-3" id="password" name="password" type="password" minlength="8" autocomplete="new-password" required placeholder="At least 8 characters"></div>
     <div><label class="mb-1 block text-sm font-semibold" for="password_confirmation">7. Confirm Password *</label>
       <input class="w-full rounded-xl border p-3" id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required placeholder="Confirm password"></div>
     <label class="sm:col-span-2 flex items-start gap-3 text-sm"><input name="terms" value="1" type="checkbox" class="mt-1 h-4 w-4" required>
       <span>I agree to the @if(config('tidong_registration.terms_url'))<a class="font-semibold text-teal-700 underline" href="{{ config('tidong_registration.terms_url') }}" target="_blank" rel="noopener">Terms & Conditions</a>@else Terms & Conditions (publication pending) @endif *</span></label>
     <button class="rounded-xl bg-teal-700 px-6 py-3 font-semibold text-white hover:bg-teal-800 focus:outline-none focus:ring-4 focus:ring-teal-300 sm:col-span-2" type="submit">Register</button>
     <div class="text-center text-sm text-slate-600 sm:col-span-2">Already registered? <a class="font-semibold text-teal-700 underline" href="{{ route('login') }}">Login</a> · <a class="font-semibold text-teal-700 underline" href="{{ route('password.request') }}">Forgot password?</a></div>
    </form>
   </div>
  </div>
 </section>
</main>
<script>
(function(){
 const account = document.getElementById('account_type');
 const conditional = document.getElementById('customer-referral');
 const vendorField = document.getElementById('vendor-name-field');
 const vendorName = document.getElementById('vendor_name');
 function update(){
   const isCustomer = account.value === 'customer';
   conditional.classList.toggle('hidden', !isCustomer);
   conditional.querySelectorAll('input').forEach(el => { el.disabled = !isCustomer; });
   const isVendor = account.value === 'vendor';
   vendorField.classList.toggle('hidden', !isVendor);
   vendorName.disabled = !isVendor;
   vendorName.required = isVendor;
 }
 account.addEventListener('change', update);
 update();
})();
</script>
</body></html>
