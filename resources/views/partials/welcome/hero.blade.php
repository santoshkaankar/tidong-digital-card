<section class="hero-section text-center text-lg-start text-white py-5"><div class="container py-4"><div class="row align-items-center g-5">
<div class="col-lg-7"><span class="badge bg-primary bg-opacity-25 text-light mb-3 px-3 py-2 rounded-pill fw-bold">🌍 TIDONG Digital Platform</span>
<h1 class="display-4 fw-bold mb-4 lh-base text-white">One Platform for Customers, Businesses &amp; Digital Services</h1>
<p class="lead mb-5">Explore customer services, vendor workspaces, restaurant menus, delivery and growing digital experiences—connected in one TIDONG platform.</p>
<div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
@auth<a href="{{ $dashboardRoute }}" class="btn btn-primary btn-lg shadow rounded-pill px-4">Go to Dashboard</a>
@else<a href="{{ route('login') }}" class="btn btn-primary btn-lg shadow rounded-pill px-4">Login to TIDONG</a>@endauth
<a href="#quick-services" class="btn btn-outline-light btn-lg rounded-pill px-4">Explore Services</a></div></div>
<div class="col-lg-5 text-center"><div class="p-4 bg-white bg-opacity-10 rounded-4 shadow-lg border border-secondary border-opacity-25">
<div class="d-flex justify-content-center gap-3 mb-3"><span class="service-icon-circle" aria-label="Shopping"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i></span><span class="service-icon-circle" aria-label="Business"><i class="fa-solid fa-store" aria-hidden="true"></i></span><span class="service-icon-circle" aria-label="Delivery"><i class="fa-solid fa-truck-fast" aria-hidden="true"></i></span></div>
<h4 class="fw-bold text-white mb-2">TIDONG® Digital Hub</h4><p class="text-light small mb-0">Customer marketplace, verified business workflows, restaurant POS and delivery partner tools.</p>
</div></div></div></div></section>