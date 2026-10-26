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
<style>
/* Keep the desktop navigation independent from the dashboard scroll. */
@media (min-width: 1280px) {
  #tidong-vendor-sidebar {
    position: sticky;
    top: 1rem;
    align-self: flex-start;
    min-width: 18rem;
    width: 18rem;
    min-height: calc(100vh - 2rem);
    max-height: calc(100dvh - 2rem);
    overflow-y: auto;
    overscroll-behavior-y: contain;
    scrollbar-width: thin;
  }
}
</style>
<aside id="tidong-vendor-sidebar" aria-label="Vendor and business navigation" class="no-print w-full shrink-0 self-start rounded-2xl bg-slate-950 p-4 text-white shadow-lg lg:w-72 lg:min-w-72">
        <div id="tidong-sidebar-body" class="tidong-sidebar-body">
    <div class="flex items-center justify-between gap-3 border-b border-slate-700 pb-4">
        <a href="{{ route('vendor.home') }}" class="text-lg font-extrabold tracking-wide text-white">TIDONG <span class="text-rose-300">Vendor</span></a>
        <span class="rounded-full bg-slate-800 px-2 py-1 text-xs text-slate-200">Workspace</span>
    </div>

    @if(\Illuminate\Support\Facades\DB::table('vendor_global_catalog_permissions')->where('vendor_id',optional($navBusiness)->vendor_id ?? optional($navBusinesses->first())->vendor_id)->where('can_create',true)->exists())
    <nav class="mt-3 border-b border-slate-700 pb-3" aria-label="Global catalog creation">
        <p class="mb-2 px-2 text-xs font-bold uppercase text-slate-400">Global Masters</p>
        @foreach(['brands'=>'Brands','item_categories'=>'Categories','units'=>'Units','variant_attributes'=>'Variant Attributes','variant_values'=>'Variant Values'] as $catalogType=>$catalogLabel)
        <a class="block rounded-lg px-3 py-2 text-sm text-white hover:bg-slate-800" href="{{route('vendor.global-catalog.index',['type'=>$catalogType])}}">{{$catalogLabel}}</a>
        @endforeach
    </nav>
    @endif
    @if($navBranch)
    <nav class="mt-3 border-b border-slate-700 pb-3" aria-label="Outlet item catalog">
        <p class="mb-2 px-2 text-xs font-bold uppercase text-slate-400">Outlet Catalog</p>
        <a href="{{ route('vendor.custom-items.index',['branch'=>$navBranch->id]) }}"
           class="block rounded-lg px-3 py-2 text-sm text-white hover:bg-slate-800">Create / List Custom Items</a>
    </nav>
    @endif
    @if ($isStudio && $navBusiness)
    @php
        $studioRoot = ['business' => $navBusiness->id] + ($navBranch ? ['branch' => $navBranch->id] : []);
        $studioMenus = [
            ['name' => 'Studio Dashboard', 'route' => 'vendor.sublimation.index', 'current' => 'vendor.sublimation.index'],
            ['name' => 'Item List', 'route' => 'vendor.sublimation.products.index', 'current' => 'vendor.sublimation.products.index'],
            ['name' => 'Create Item', 'route' => 'vendor.sublimation.products.create', 'current' => 'vendor.sublimation.products.create'],
            ['name' => 'Order List', 'route' => 'vendor.sublimation.orders.index', 'current' => 'vendor.sublimation.orders.index'],
            ['name' => 'New Studio Order', 'route' => 'vendor.sublimation.orders.create', 'current' => 'vendor.sublimation.orders.create'],
            ['name' => 'Design Approvals', 'route' => 'vendor.sublimation.orders.index', 'query' => ['queue' => 'design']],
            ['name' => 'Production Queue', 'route' => 'vendor.sublimation.orders.index', 'query' => ['queue' => 'production']],
            ['name' => 'Dispatch & Delivery', 'route' => 'vendor.sublimation.orders.index', 'query' => ['queue' => 'delivery']],
            ['name' => 'Materials & Stock', 'route' => 'vendor.sublimation.materials.index', 'current' => 'vendor.sublimation.materials.*'],
            ['name' => 'Studio Reports', 'route' => 'vendor.sublimation.reports.index', 'current' => 'vendor.sublimation.reports.*'],
        ];
    @endphp
    <nav class="mt-4 border-b border-slate-700 pb-4" aria-label="Sublimation studio menu">
        <p class="mb-2 px-2 text-xs font-bold uppercase tracking-wider text-rose-300">Sublimation Studio</p>
        <div class="grid grid-cols-2 gap-1 lg:grid-cols-1">
            @foreach ($studioMenus as $entry)
                @php
                    $active = isset($entry['query'])
                        ? request()->routeIs('vendor.sublimation.orders.index') && request('queue') === $entry['query']['queue']
                        : (isset($entry['current']) && request()->routeIs($entry['current']) && !request('queue'));
                @endphp
                @if (Route::has($entry['route']))
                    <a href="{{ route($entry['route'], array_merge($studioRoot, $entry['query'] ?? [])) }}"
                       @if ($active) aria-current="page" @endif
                       class="block rounded-lg px-3 py-2 text-sm {{ $active ? 'bg-rose-700 font-bold text-white' : 'text-slate-100 hover:bg-slate-800' }}">{{ $entry['name'] }}</a>
                @else
                    <span class="rounded-lg px-3 py-2 text-sm text-slate-400">{{ $entry['name'] }} · Setup pending</span>
                @endif
            @endforeach
        </div>
        <p class="mt-2 px-2 text-xs text-slate-400">Select any menu. Sidebar scrolls separately from the page.</p>
    </nav>
    @endif
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
    @if ($navBusiness && $navGroup && !$isStudio)
        <div class="mt-4 border-t border-slate-700 pt-4">
            <p class="px-2 text-xs font-bold uppercase tracking-wider text-rose-300">{{ $navGroup['title'] }}</p>
            <a href="{{ route('vendor.business.workspace', $navBusiness->id) }}" class="mt-2 block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-slate-800">Business Overview</a>
            @if ($isStudio && Route::has('vendor.sublimation.orders.index'))
                <nav aria-label="Sublimation quick links" class="mt-2 space-y-1 border-b border-slate-700 pb-3 text-sm">
                    <a href="{{ route('vendor.sublimation.products.index', ['business' => $navBusiness->id] + ($navBranch ? ['branch' => $navBranch->id] : [])) }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('vendor.sublimation.products.*') ? 'bg-slate-700 font-semibold' : '' }}">Product List &amp; Options</a>
                    <a href="{{ route('vendor.sublimation.orders.index', ['business' => $navBusiness->id] + ($navBranch ? ['branch' => $navBranch->id] : [])) }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('vendor.sublimation.orders.*') ? 'bg-slate-700 font-semibold' : '' }}">Order List &amp; Delivery</a>
                </nav>
            @endif
            @if ($isRestaurant && $posSupportsType && $isApproved && $navBranch && Route::has('pos.menu-library.index'))
                <nav aria-label="Outlet item management" class="mt-2 space-y-1 border-b border-slate-700 pb-3 text-sm">
                    <a href="{{ route('pos.branches.show', ['branch' => $navBranch->id]) }}"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800 {{ request()->routeIs('pos.branches.show') ? 'bg-slate-700 font-semibold' : '' }}">Outlet Dashboard</a>
                    <a href="{{ route('pos.menu-library.index', ['branch' => $navBranch->id]) }}#item-list"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800">Item List</a>
                    <a href="{{ route('pos.menu-library.index', ['branch' => $navBranch->id]) }}#create-item"
                       class="block rounded-lg px-3 py-2 hover:bg-slate-800">Create Item</a>
                </nav>
            @endif
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
    </div>
</aside>
