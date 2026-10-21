{{--
    Shared navigation for the Vendor home, selected Business workspace and Sublimation Studio.
    Menus are presentation only: backend controllers must independently enforce Vendor ownership.
--}}
@php
    $navBusinesses = $navBusinesses ?? $businesses ?? collect();
    $navBusiness = $navBusiness ?? null;
    $navBranch = $navBranch ?? null;
    $navGroups = config('tidong_business_navigation.groups', []);
    $navTypeGroups = config('tidong_business_navigation.type_groups', []);
    $navGroupCode = $navBusiness ? ($navTypeGroups[$navBusiness->business_type] ?? 'services') : null;
    $navGroup = $navGroupCode ? ($navGroups[$navGroupCode] ?? null) : null;
    $isRestaurant = $navGroupCode === 'restaurant';
    $posSupportsType = $navBusiness && in_array($navBusiness->business_type, ['restaurant', 'cafe'], true);
    $isStudio = $navGroupCode === 'sublimation';
    $isApproved = $navBusiness && $navBusiness->status === 'approved';
@endphp
<aside aria-label="Vendor and business navigation" class="no-print w-full shrink-0 rounded-2xl bg-slate-950 p-4 text-white shadow-lg xl:sticky xl:top-4 xl:w-72">
    <div class="flex items-center justify-between gap-3 border-b border-slate-700 pb-4">
        <a href="{{ route('vendor.home') }}" class="text-lg font-extrabold tracking-wide text-white">TIDONG <span class="text-rose-300">Vendor</span></a>
        <span class="rounded-full bg-slate-800 px-2 py-1 text-xs text-slate-200">Workspace</span>
    </div>
    <details class="mt-3" open>
        <summary class="cursor-pointer rounded-lg px-2 py-2 text-xs font-bold uppercase tracking-wider text-slate-300">Vendor Main Menu</summary>
        <nav class="mt-1 space-y-1 text-sm" aria-label="Main vendor menu">
            <a href="{{ route('vendor.home') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ !$navBusiness ? 'bg-blue-700 font-semibold' : '' }}">Vendor Dashboard</a>
            <a href="{{ route('vendor.home') }}#vendor-businesses" class="block rounded-lg px-3 py-2 hover:bg-slate-800">My Businesses & Branches</a>
            @if(Route::has('vendor.delivery.index') && app(\App\Services\Platform\ModuleRegistry::class)->isEnabled('delivery'))
                <a href="{{ route('vendor.delivery.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-800">Delivery Dispatch</a>
            @endif
            <span class="block px-3 py-2 text-slate-400" title="Not released yet">Employees & Permissions · Planned</span>
            <span class="block px-3 py-2 text-slate-400" title="Not released yet">Consolidated Reports · Planned</span>
        </nav>
    </details>
    <div class="mt-4 border-t border-slate-700 pt-4">
        <p class="px-2 text-xs font-bold uppercase tracking-wider text-slate-300">Switch Business</p>
        <nav class="mt-2 max-h-56 space-y-1 overflow-y-auto text-sm" aria-label="Vendor businesses">
            @forelse ($navBusinesses as $option)
                <a href="{{ route('vendor.business.workspace', $option->id) }}"
                    @if ($navBusiness && (int) $navBusiness->id === (int) $option->id) aria-current="page" @endif
                    class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ $navBusiness && (int) $navBusiness->id === (int) $option->id ? 'bg-slate-700 font-bold' : '' }}">
                    <span class="block break-words">{{ $option->name }}</span>
                    <span class="text-xs text-slate-300">{{ ucwords(str_replace('_', ' ', $option->business_type)) }}</span>
                </a>
            @empty
                <p class="px-3 py-2 text-xs text-slate-300">Create your first Business to see its menu.</p>
            @endforelse
        </nav>
    </div>
    @if ($navBusiness && $navGroup)
        <div class="mt-4 border-t border-slate-700 pt-4">
            <p class="px-2 text-xs font-bold uppercase tracking-wider text-rose-300">{{ $navGroup['title'] }}</p>
            <a href="{{ route('vendor.business.workspace', $navBusiness->id) }}" class="mt-2 block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-slate-800">Business Overview</a>
            <nav aria-label="Current business menu" class="mt-1 space-y-1 text-sm">
                @foreach ($navGroup['items'] as $item)
                    @php
                        $href = null;
                        $key = $item['key'];
                        if ($isStudio && $key && Route::has('vendor.sublimation.index')) {
                            $url = route('vendor.sublimation.index', ['business' => $navBusiness->id] + ($navBranch ? ['branch' => $navBranch->id] : []));
                            $anchors = [
                                'studio' => '', 'studio-add' => '#studio-add-product',
                                'studio-prices' => '#studio-prices', 'studio-orders' => '#studio-new-job',
                                'studio-jobs' => '#studio-job-cards',
                            ];
                            if (array_key_exists($key, $anchors)) $href = $url.$anchors[$key];
                        }
                        if ($isRestaurant && $posSupportsType && $isApproved && $navBranch && $key) {
                            $routes = [
                                'pos' => 'pos.branches.show', 'menu' => 'pos.menu-library.index',
                                'kds' => 'kds.branches.show', 'tables' => 'pos.tables.index',
                                'qr' => 'pos.qr.requests',
                            ];
                            if (isset($routes[$key]) && Route::has($routes[$key])) {
                                $href = route($routes[$key], ['branch' => $navBranch->id]);
                            }
                        }
                    @endphp
                    @if ($href)
                        <a href="{{ $href }}" class="block rounded-lg px-3 py-2 hover:bg-slate-800">{{ $item['label'] }}</a>
                    @else
                        <span class="flex items-start justify-between gap-2 rounded-lg px-3 py-2 text-slate-400">
                            <span>{{ $item['label'] }}</span><span class="text-[10px]">Planned</span>
                        </span>
                    @endif
                @endforeach
            </nav>
            @if ($isRestaurant && !$isApproved)
                <p class="mt-3 rounded-lg bg-slate-800 p-3 text-xs text-amber-200">Restaurant operations will become available after Business approval.</p>
            @elseif ($isRestaurant && !$posSupportsType)
                <p class="mt-3 rounded-lg bg-slate-800 p-3 text-xs text-slate-200">This business type has a dining menu plan, but its POS integration is not released yet.</p>
            @elseif ($isRestaurant && !$navBranch)
                <p class="mt-3 rounded-lg bg-slate-800 p-3 text-xs text-slate-200">Add a Branch to access restaurant operations.</p>
            @endif
        </div>
    @endif
    <p class="mt-5 border-t border-slate-700 pt-4 text-xs text-slate-400">Business permissions are checked by the server. Planned items are not active links.</p>
</aside>
