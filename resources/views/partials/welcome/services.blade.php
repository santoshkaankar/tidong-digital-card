@php
$homeServices = [
 ['name'=>'Customer','sub'=>'Customer Dashboard','icon'=>'fa-user','color'=>'primary','route'=>'customer.home','type'=>'customer'],
 ['name'=>'Business','sub'=>'Vendor Workspace','icon'=>'fa-store','color'=>'danger','route'=>'vendor.home','type'=>'vendor'],
 ['name'=>'Food & Dining','sub'=>'Restaurant POS','icon'=>'fa-utensils','color'=>'warning','route'=>'pos.home','type'=>'vendor'],
 ['name'=>'Delivery','sub'=>'Partner Workspace','icon'=>'fa-truck','color'=>'success','route'=>'delivery.home','type'=>'delivery'],
 ['name'=>'Kitchen Display','sub'=>'Order Operations','icon'=>'fa-clipboard-list','color'=>'info','route'=>'kds.home','type'=>'vendor'],
 ['name'=>'Grocery','sub'=>'Store Discovery','icon'=>'fa-shopping-basket','color'=>'dark','route'=>'grocery.shop.stores','type'=>'grocery'],
];
@endphp
<section id="quick-services" class="py-5 bg-light border-bottom"><div class="container">
<div class="text-center mb-4"><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">Quick Services Menu</span><h2 class="fw-bold text-dark mt-2">Explore Our Digital Ecosystem</h2><p class="text-muted">Select a service to open its real workspace. Permission-controlled areas require an authorized login.</p></div>
<div class="row g-3 justify-content-center">
@foreach ($homeServices as $service)
@php
$serviceRouteExists=Route::has($service['route']);
$groceryOpen=($service['type']!=='grocery') || ($groceryModuleEnabled ?? false);
$serviceAvailable=$serviceRouteExists && $groceryOpen;
$hasServiceRole=($service['type']==='grocery') || in_array($service['type'],$tidongAccountCodes,true);
$href= !$serviceAvailable ? null : ($hasServiceRole ? route($service['route']) : route('login'));
@endphp
<div class="col-6 col-md-4 col-lg-2">
@if($href)<a href="{{ $href }}" class="text-decoration-none service-link">@else<div class="service-unavailable">@endif
<div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 bg-white hover-up"><div class="icon-shape bg-{{ $service['color'] }} text-white rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"><i class="fas {{ $service['icon'] }} fs-5"></i></div>
<h3 class="fw-bold text-dark mb-1 small">{{ $service['name'] }}</h3><span class="text-muted service-sub">{{ $service['sub'] }}</span>@unless($serviceAvailable)<span class="text-muted service-sub">Coming soon</span>@endunless</div>
@if($href)</a>@else</div>@endif
</div>
@endforeach
</div></div></section>