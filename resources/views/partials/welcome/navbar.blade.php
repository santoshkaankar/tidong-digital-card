<nav class="navbar navbar-expand-lg navbar-light sticky-top py-3 bg-white shadow-sm" aria-label="Main navigation">
 <div class="container">
  <a class="navbar-brand fw-bold fs-4 text-primary" href="{{ url('/') }}"><i class="fas fa-layer-group me-2"></i>TIDONG<span class="text-dark">®</span> Digital</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarNav"><ul class="navbar-nav align-items-center gap-3">
   <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('pages.services') }}">Services</a></li>
   <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('pages.features') }}">Features</a></li>
   <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('pages.ads') }}">Sponsored Ads</a></li>
   <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('pages.about') }}">About Us</a></li>
   <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('pages.guides') }}">User Guides</a></li>
   <li class="nav-item"><a class="nav-link fw-semibold text-dark" href="{{ route('pages.contact') }}">Contact</a></li>
   @auth
    <li class="nav-item"><a href="{{ $dashboardRoute }}" class="btn btn-primary btn-sm px-3 rounded-pill"><i class="fas fa-columns me-1"></i> Dashboard</a></li>
    <li class="nav-item"><span class="nav-link text-dark">{{ auth()->user()->name }}</span></li>
    <li class="nav-item"><form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">Logout</button></form></li>
   @else
    <li class="nav-item"><a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-4 rounded-pill">Login</a></li>
    @if (Route::has('register') && ($publicRegistrationEnabled ?? false))<li class="nav-item"><a href="{{ route('register') }}" class="btn btn-primary btn-sm px-4 rounded-pill">Register</a></li>@endif
   @endauth
  </ul></div>
 </div>
</nav>