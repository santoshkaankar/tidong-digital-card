@php
$guides = [
 ['name'=>'Customer Login','desc'=>'Sign in securely to your account.','icon'=>'fa-sign-in-alt','route'=>'login'],
 ['name'=>'Your Dashboard','desc'=>'Open your authorized workspace after login.','icon'=>'fa-columns','route'=>null],
 ['name'=>'Customer Marketplace','desc'=>'Discover services available for customers.','icon'=>'fa-shopping-bag','route'=>'customer.explore.index'],
 ['name'=>'Business Workspace','desc'=>'Manage approved businesses and branches.','icon'=>'fa-store','route'=>'vendor.home'],
 ['name'=>'Restaurant POS','desc'=>'Manage branch menus and orders.','icon'=>'fa-utensils','route'=>'pos.home'],
 ['name'=>'Kitchen Display','desc'=>'View authorized restaurant kitchen orders.','icon'=>'fa-clipboard-list','route'=>'kds.home'],
 ['name'=>'Delivery Partner','desc'=>'Access delivery tasks when approved.','icon'=>'fa-truck','route'=>'delivery.home'],
 ['name'=>'Grocery','desc'=>'Browse grocery shops when enabled.','icon'=>'fa-shopping-basket','route'=>'grocery.shop.stores'],
 ['name'=>'Platform Help','desc'=>'Learn how to access your TIDONG account.','icon'=>'fa-question-circle','route'=>'login'],
];
@endphp
<section class="py-5 bg-white border-top" id="instructions"><div class="container"><div class="text-center mb-5"><h2 class="fw-bold text-dark mt-2">Platform Instructions &amp; User Guides</h2><p class="text-muted">Real navigation paths for currently registered TIDONG workspaces.</p></div><div class="row g-4">
@foreach($guides as $guide)
@php
$guideName=$guide['route'];
$hasGuideRoute=$guideName===null || Route::has($guideName);
$guideEnabled=$guideName!=='grocery.shop.stores' || ($groceryModuleEnabled ?? false);
$guideHref=($hasGuideRoute && $guideEnabled) ? ($guideName===null ? $dashboardRoute : (in_array($guideName,['login'],true) ? route($guideName) : (auth()->check() ? route($guideName) : route('login')))) : null;
@endphp
<div class="col-xl-3 col-lg-4 col-md-6"><div class="card h-100 border shadow-sm rounded-4 p-3 bg-light bg-opacity-25"><div class="card-body"><div class="icon-box bg-primary bg-opacity-10 text-primary mb-3 rounded-3 d-flex align-items-center justify-content-center" style="width:45px;height:45px"><i class="fas {{ $guide['icon'] }} fs-5"></i></div><h3 class="fw-bold text-dark fs-6 mb-2">{{ $guide['name'] }}</h3><p class="text-muted small mb-3">{{ $guide['desc'] }}</p>
@if($guideHref)<a href="{{ $guideHref }}" class="text-primary text-decoration-none fw-semibold small">Open <i class="fas fa-arrow-right ms-1"></i></a>@else<span class="text-muted small">Coming soon</span>@endif</div></div></div>
@endforeach</div></div></section>