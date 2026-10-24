<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vendor Dashboard - Digital Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<div class="mx-auto flex max-w-screen-2xl flex-col items-start gap-5 p-4 xl:flex-row xl:p-6">
    @include('vendor.partials.sidebar', ['navBusinesses' => $businesses])
<main class="w-full min-w-0 flex-1 space-y-6">
    <header class="flex flex-wrap items-start justify-between gap-4 rounded-xl bg-white p-6 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold">{{ $vendor->display_name }}</h1>
            <p class="mt-1 text-sm text-slate-500">Vendor Code: {{ $vendor->vendor_code }}</p>
            <p class="mt-2 font-semibold">Approval: {{ ucfirst($vendor->status) }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('customer.home') }}" class="rounded-lg border px-4 py-2">Customer Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button class="rounded-lg border px-4 py-2" type="submit">Logout</button>
            </form>
        </div>
    </header>

    @if (session('success'))
        <div class="rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="rounded-lg bg-red-50 p-4 text-red-800">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <section class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="vendor-setup-title">
        <h2 id="vendor-setup-title" class="text-xl font-bold text-slate-900">Get your store ready — step by step</h2>
        <p class="mt-2 text-sm text-slate-600">Business approval is only the first step. Customers see a store once its outlet is set up and ordering is separately enabled.</p>
        <ol class="mt-4 grid gap-3 text-sm md:grid-cols-3">
            <li class="rounded-xl border border-slate-200 bg-slate-50 p-4"><strong class="block text-slate-900">1. Choose your business</strong><span class="mt-1 block text-slate-600">Open its dashboard below. Approved businesses can move to outlet setup.</span><a class="mt-2 inline-block font-semibold text-blue-700 underline" href="#vendor-businesses">View my businesses →</a></li>
            <li class="rounded-xl border border-slate-200 bg-slate-50 p-4"><strong class="block text-slate-900">2. Add an outlet</strong><span class="mt-1 block text-slate-600">Enter outlet name and address in the outlet form below that business. Each outlet has its own setup.</span><a class="mt-2 inline-block font-semibold text-blue-700 underline" href="#vendor-businesses">Go to outlet setup →</a></li>
            <li class="rounded-xl border border-slate-200 bg-slate-50 p-4"><strong class="block text-slate-900">3. Prepare to accept orders</strong><span class="mt-1 block text-slate-600">Add menu or products, licences and required settings. Ordering stays OFF until separately activated.</span></li>
        </ol>
    </section>

    @if ($vendor->status !== 'approved')
        <section class="rounded-xl bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Application status: {{ ucfirst($vendor->status) }}</h2>
            <p class="mt-2 text-slate-600">Your application needs approval before you can add Businesses and Branches. New orders are disabled.</p>
        </section>
    @else
        <section class="rounded-xl bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">Create a Business</h2>
            <p class="mt-1 text-sm text-slate-600">Each Business requires platform approval before operating. You can create multiple Businesses.</p>
            <form method="POST" action="{{ route('vendor.businesses.store') }}" class="mt-5 flex flex-wrap items-end gap-3">
                @csrf
                <div class="min-w-56 flex-1">
                    <label for="name" class="mb-1 block text-sm font-medium">Business name</label>
                    <input id="name" name="name" required maxlength="255" value="{{ old('name') }}" class="w-full rounded-lg border p-3" placeholder="Example: City Restaurant">
                </div>
                <div class="min-w-56 flex-1">
                    <label for="business_type" class="mb-1 block text-sm font-medium">Business type</label>
                    <select id="business_type" name="business_type" required class="w-full rounded-lg border bg-white p-3">
                        <option value="" disabled {{ old('business_type') ? '' : 'selected' }}>Select business type</option>
                        <option value="restaurant" {{ old('business_type') === 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                        <option value="cafe" {{ old('business_type') === 'cafe' ? 'selected' : '' }}>Café / Coffee Shop</option>
                        <option value="cloud_kitchen" {{ old('business_type') === 'cloud_kitchen' ? 'selected' : '' }}>Cloud Kitchen</option>
                        <option value="bakery" {{ old('business_type') === 'bakery' ? 'selected' : '' }}>Bakery</option>
                        <option value="grocery" {{ old('business_type') === 'grocery' ? 'selected' : '' }}>Grocery / Kirana</option>
                        <option value="retail" {{ old('business_type') === 'retail' ? 'selected' : '' }}>Retail Shop</option>
                        <option value="shopping" {{ old('business_type') === 'shopping' ? 'selected' : '' }}>Shopping / E-commerce</option>
                        <option value="salon" {{ old('business_type') === 'salon' ? 'selected' : '' }}>Salon / Beauty</option>
                        <option value="spa" {{ old('business_type') === 'spa' ? 'selected' : '' }}>Spa / Wellness</option>
                        <option value="pharmacy" {{ old('business_type') === 'pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                        <option value="hotel" {{ old('business_type') === 'hotel' ? 'selected' : '' }}>Hotel / Accommodation</option>
                        <option value="taxi" {{ old('business_type') === 'taxi' ? 'selected' : '' }}>Taxi / Cab</option>
                        <option value="car_rental" {{ old('business_type') === 'car_rental' ? 'selected' : '' }}>Car / Bike Rental</option>
                        <option value="travel_agency" {{ old('business_type') === 'travel_agency' ? 'selected' : '' }}>Travel Agency</option>
                        <option value="delivery" {{ old('business_type') === 'delivery' ? 'selected' : '' }}>Delivery / Courier</option>
                        <option value="logistics" {{ old('business_type') === 'logistics' ? 'selected' : '' }}>Logistics / Transport</option>
                        <option value="money_exchange" {{ old('business_type') === 'money_exchange' ? 'selected' : '' }}>Money Exchange</option>
                        <option value="marriage_planner" {{ old('business_type') === 'marriage_planner' ? 'selected' : '' }}>Marriage Planner</option>
                        <option value="event_management" {{ old('business_type') === 'event_management' ? 'selected' : '' }}>Event Management</option>
                        <option value="real_estate" {{ old('business_type') === 'real_estate' ? 'selected' : '' }}>Real Estate</option>
                        <option value="education" {{ old('business_type') === 'education' ? 'selected' : '' }}>Education / Coaching</option>
                        <option value="repair_services" {{ old('business_type') === 'repair_services' ? 'selected' : '' }}>Repair Services</option>
                        <option value="professional_services" {{ old('business_type') === 'professional_services' ? 'selected' : '' }}>Professional Services</option>
                        <option value="sublimation" {{ old('business_type') === 'sublimation' ? 'selected' : '' }}>Sublimation / Personalized Gifts</option>
                        <option value="custom_printing" {{ old('business_type') === 'custom_printing' ? 'selected' : '' }}>Custom Printing Studio</option>
                        <option value="wholesale" {{ old('business_type') === 'wholesale' ? 'selected' : '' }}>Wholesale Business</option>
                        <option value="distributor" {{ old('business_type') === 'distributor' ? 'selected' : '' }}>Distributor / Dealer Network</option>
                        <option value="c_and_f" {{ old('business_type') === 'c_and_f' ? 'selected' : '' }}>C&amp;F Agent / Warehouse</option>
                        <option value="manufacturing" {{ old('business_type') === 'manufacturing' ? 'selected' : '' }}>Manufacturing Unit</option>
                        <option value="processing" {{ old('business_type') === 'processing' ? 'selected' : '' }}>Processing Unit</option>
                        <option value="other" {{ old('business_type') === 'other' ? 'selected' : '' }}>Other Business</option>
                    </select>
                </div>
                <button class="rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white">Add Business</button>
            </form>
        </section>
    @endif

    <section id="vendor-businesses" class="space-y-4">
        <h2 class="text-xl font-semibold">Your Businesses ({{ $businesses->count() }})</h2>
        @forelse ($businesses as $business)
            <article class="rounded-xl bg-white p-6 shadow-sm">
                <div class="flex flex-wrap justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold">{{ $business->name }}</h3>
                        <a href="{{ route('vendor.business.workspace', $business->id) }}" class="mt-3 inline-block rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Open Business Dashboard</a>
                        <p class="text-sm text-slate-600">{{ $business->business_type }} | Status: {{ ucfirst($business->status) }}</p>
                        <p class="text-sm font-semibold {{ $business->is_accepting_orders ? 'text-emerald-700' : 'text-amber-700' }}">Customer ordering: {{ $business->is_accepting_orders ? 'ON' : 'OFF — setup required' }}</p>
                        @if (in_array($business->business_type, ['sublimation', 'custom_printing', 'printing'], true))
                            <a href="{{ route('vendor.sublimation.index', $business->id) }}" class="mt-3 inline-block rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white">Open Sublimation Studio</a>
                        @endif
                    </div>
                    <div class="text-sm font-semibold {{ $business->branches->isEmpty() ? 'text-amber-700' : 'text-emerald-700' }}">{{ $business->branches->isEmpty() ? 'Next step: Add your first outlet' : $business->branches->count().' outlet(s) registered' }}</div>
                </div>
                @if ($business->branches->isNotEmpty())
                    <ul class="mt-4 list-inside list-disc text-sm">
                        @foreach ($business->branches as $branch)
                            <li>{{ $branch->name }} — {{ $branch->city ?: 'City not set' }} ({{ $branch->branch_code }}) <a href="{{ route('vendor.branch.edit', $branch->id) }}" class="ml-2 text-blue-700 underline">Request Branch Edit</a> <a href="{{ route('vendor.branch.licenses.index', $branch->id) }}" class="ml-2 text-blue-700 underline">GST / FSSAI / Licences</a></li>
                        @endforeach
                    </ul>
                @endif
                @if ($vendor->status === 'approved' && in_array($business->status, ['pending', 'approved'], true))
                    <h4 class="mt-5 border-t pt-4 text-base font-bold text-slate-900">Add outlet to {{ $business->name }}</h4>
                    <p class="mt-1 text-sm text-slate-600">Complete the outlet details below. Creating an outlet does not automatically accept customer orders.</p>
                    <form method="POST" action="{{ route('vendor.branches.store', $business->id) }}"
                          class="mt-5 grid gap-3 border-t pt-4 md:grid-cols-3" data-address-form data-address-url="{{ route('vendor.address.options') }}">
                        @csrf
                        <div><label class="block text-sm font-medium">Branch name</label>
                            <input name="name" required maxlength="255" class="mt-1 w-full rounded-lg border p-3" placeholder="Main Outlet"></div>
                        <div><label class="block text-sm font-medium">State</label>
                            <select data-address-state required class="mt-1 w-full rounded-lg border bg-white p-3"><option value="">Loading states...</option></select></div>
                        <div><label class="block text-sm font-medium">District</label>
                            <select data-address-district required disabled class="mt-1 w-full rounded-lg border bg-white p-3"><option value="">Choose a state first</option></select></div>
                        <div><label class="block text-sm font-medium">Post Office / Area</label>
                            <select data-address-office name="india_post_office_id" required disabled class="mt-1 w-full rounded-lg border bg-white p-3"><option value="">Choose a district first</option></select></div>
                        <div><label class="block text-sm font-medium">PIN Code</label>
                            <input data-address-pin name="pincode" readonly required maxlength="6" class="mt-1 w-full rounded-lg border bg-slate-50 p-3" placeholder="Auto from Post Office"></div>
                        <div><label class="block text-sm font-medium">Town / City (optional)</label>
                            <input name="city" maxlength="255" class="mt-1 w-full rounded-lg border p-3" placeholder="Enter actual city or town"></div>
                        <div><label class="block text-sm font-medium">Address</label>
                            <input name="address_line" maxlength="255" class="mt-1 w-full rounded-lg border p-3" placeholder="Address"></div>
                        <div><label class="block text-sm font-medium">Phone (optional)</label>
                            <input name="phone" maxlength="30" class="mt-1 w-full rounded-lg border p-3" placeholder="Phone"></div>
                        <div><label class="block text-sm font-medium">Outlet GPS Latitude (optional)</label>
                            <input data-branch-lat name="latitude" inputmode="decimal" type="number" step="any" min="6" max="38" class="mt-1 w-full rounded-lg border p-3" placeholder="e.g. 25.2138"></div>
                        <div><label class="block text-sm font-medium">Outlet GPS Longitude (optional)</label>
                            <input data-branch-lon name="longitude" inputmode="decimal" type="number" step="any" min="68" max="98" class="mt-1 w-full rounded-lg border p-3" placeholder="e.g. 75.8648"></div>
                        <div class="md:col-span-3 rounded-lg border border-blue-100 bg-blue-50 p-3 text-sm">
                            <button type="button" data-branch-geolocate class="rounded border bg-white px-3 py-2 font-medium">Use my current position</button>
                            <span class="ml-2" data-branch-geostatus role="status" aria-live="polite">Only use this at the actual outlet. You can also enter verified coordinates manually.</span>
                            <p class="mt-2 text-slate-600">GPS coordinates are a vendor-supplied outlet location, not independently verified. Do not use post-office coordinates for delivery pricing.</p>
                        </div>
                        <div class="flex items-end"><button class="w-full rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white">Add Branch</button></div>
                    </form>
                @endif
            </article>
        @empty
            <div class="rounded-xl bg-white p-6 text-slate-600">No Businesses yet.</div>
        @endforelse
    </section>
</main>
</div>
<script>
// No third-party address service. Values are fetched from the local Laravel directory.
(() => {
  const forms = document.querySelectorAll('[data-address-form]');
  if (!forms.length) return;
  const fill = (select, placeholder, values, asOffice = false) => {
    select.replaceChildren(new Option(placeholder, ''));
    values.forEach(value => {
      if (asOffice) {
        select.add(new Option(`${value.post_office} · ${value.pincode} (${value.delivery_type || 'Post Office'})`, String(value.id)));
        select.options[select.options.length - 1].dataset.pin = value.pincode;
      } else select.add(new Option(value, value));
    });
    select.disabled = values.length === 0;
  };
  const fetchOptions = async (form, params) => {
    const url = new URL(form.dataset.addressUrl, window.location.href);
    Object.entries(params).forEach(([key, value]) => url.searchParams.set(key, value));
    const response = await fetch(url.toString(), {headers: {'Accept':'application/json'}, credentials: 'same-origin'});
    if (!response.ok) throw new Error('Directory could not be loaded');
    return (await response.json()).options || [];
  };
  forms.forEach(async form => {
    const state = form.querySelector('[data-address-state]');
    const district = form.querySelector('[data-address-district]');
    const office = form.querySelector('[data-address-office]');
    const pin = form.querySelector('[data-address-pin]');
    try {
      fill(state, 'Select State', await fetchOptions(form, {level:'states'}));
    } catch (_) { fill(state, 'Directory unavailable: check Seeder', []); }
    state.addEventListener('change', async () => {
      fill(district,'Select District',[]);
      fill(office,'Select Post Office',[]);
      pin.value = '';
      if (!state.value) return;
      try { fill(district,'Select District',await fetchOptions(form,{level:'districts',state:state.value})); }
      catch (_) { fill(district,'Could not load Districts',[]); }
    });
    district.addEventListener('change', async () => {
      fill(office,'Select Post Office',[]); pin.value = '';
      if (!district.value) return;
      try { fill(office,'Select Post Office',await fetchOptions(form,{level:'offices',state:state.value,district:district.value}),true); }
      catch (_) { fill(office,'Could not load Post Offices',[]); }
    });
    office.addEventListener('change', () => {
      pin.value = office.selectedOptions[0]?.dataset.pin || '';
    });
  });
})();
</script>
<script>
(() => {
  // Optional browser GPS capture. No location is collected on page load.
  document.querySelectorAll('[data-branch-geolocate]').forEach(button => {
    button.addEventListener('click', () => {
      const form = button.closest('form');
      const info = form.querySelector('[data-branch-geostatus]');
      if (!navigator.geolocation || !window.isSecureContext) {
        info.textContent = 'GPS is unavailable. Use HTTPS/localhost or enter coordinates manually.';
        return;
      }
      info.textContent = 'Waiting for location permission...';
      navigator.geolocation.getCurrentPosition(position => {
        form.querySelector('[data-branch-lat]').value = position.coords.latitude.toFixed(7);
        form.querySelector('[data-branch-lon]').value = position.coords.longitude.toFixed(7);
        info.textContent = `Captured device position (accuracy ±${Math.round(position.coords.accuracy)} m). Confirm it is the actual outlet before saving.`;
      }, error => {
        info.textContent = 'Unable to get GPS location. Check permission or enter it manually.';
      }, {enableHighAccuracy:true, timeout:15000, maximumAge:0});
    });
  });
})();
</script>
</body>
</html>
