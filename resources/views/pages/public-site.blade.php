@php
    $pages = [
        'about' => ['title' => 'About Us', 'description' => 'Learn about TIDONG Digital, a connected platform for customers, vendors, delivery partners and professional services.'],
        'services' => ['title' => 'Our Services', 'description' => 'Explore the customer, vendor, restaurant, grocery, delivery and other services designed for TIDONG Digital.'],
        'help' => ['title' => 'How TIDONG Works', 'description' => 'A public guide to using TIDONG Digital, account access, business registration, approval and support.'],
        'contact' => ['title' => 'Contact Us', 'description' => 'Find TIDONG Digital contact information and guidance on requesting help.'],
    ];
    $details = $pages[$page] ?? null;
    abort_unless($details, 404);
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="index,follow">
    <meta name="description" content="{{ $details['description'] }}">
    <meta name="theme-color" content="#10233a">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/tidong-public.css') }}">
    <title>{{ $details['title'] }} | TIDONG® Digital</title>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<header class="site-header">
  <div class="shell header-inner">
    <a class="brand" href="{{ url('/') }}" aria-label="TIDONG Digital home">
      <img src="{{ asset('assets/tidong-full.jpeg') }}" alt="TIDONG Digital" width="164" height="50">
    </a>
    <nav aria-label="Public pages">
      <a href="{{ url('/') }}">Home</a>
      <a href="{{ route('pages.about') }}" @if($page === 'about') aria-current="page" @endif>About</a>
      <a href="{{ route('pages.services') }}" @if($page === 'services') aria-current="page" @endif>Services</a>
      <a href="{{ route('pages.help') }}" @if($page === 'help') aria-current="page" @endif>Help</a>
      <a href="{{ route('pages.contact') }}" @if($page === 'contact') aria-current="page" @endif>Contact</a>
      <a href="{{ route('login') }}" class="nav-login">Login</a>
    </nav>
  </div>
</header>
<main id="main">
  <section class="hero"><div class="shell">
    <span class="eyebrow">TIDONG® DIGITAL</span>
    <h1>{{ $details['title'] }}</h1>
    <p>{{ $details['description'] }}</p>
  </div></section>
  <div class="shell content">
    @if($page === 'about')
      <section class="card">
        <h2>One platform, different digital services</h2>
        <p>TIDONG Digital brings together customers, independent vendors, businesses, outlets, delivery partners and professional or service-provider accounts in one connected platform. Our goal is to make discovering services and managing business activities simpler.</p>
        <p>TIDONG Digital is operated by TIDONG MARKETING PRIVATE LIMITED. Different account types have separate workspaces and permissions. Public information is available without an account; private dashboards require an authorized login.</p>
      </section>
      <section class="grid-two">
        <article class="card"><h2>For customers</h2><p>Discover participating services, explore available catalogues and manage eligible orders through an authorized customer account.</p></article>
        <article class="card"><h2>For vendors</h2><p>A vendor can manage different businesses and outlets. Operational information such as stock, prices and reports is separated by outlet.</p></article>
        <article class="card"><h2>For partners</h2><p>Delivery partners, professionals and service providers use the relevant workflows when their account or activity has been approved and the feature is enabled.</p></article>
        <article class="card"><h2>Responsible access</h2><p>Account roles, approvals and platform-controlled feature switches determine what a user can access. Not every planned feature is currently available.</p></article>
      </section>
    @elseif($page === 'services')
      <section class="card">
        <h2>Explore the TIDONG ecosystem</h2>
        <p>These are platform service areas, not a guarantee that every function is available today. Availability depends on account permissions, business onboarding and the feature settings shown in the website.</p>
      </section>
      <section class="grid-two">
        <article class="card"><h2>Customer services</h2><p>Explore participating businesses, products and customer order options when enabled.</p></article>
        <article class="card"><h2>Vendor and outlet workspaces</h2><p>Business registration, outlet-specific product information, pricing and operational tools for authorized vendor accounts.</p></article>
        <article class="card"><h2>Restaurant and QR menus</h2><p>Participating restaurants may provide QR-based menus, ordering, point-of-sale and kitchen workflows.</p></article>
        <article class="card"><h2>Grocery and retail</h2><p>Catalogue, inventory and ordering workflows for eligible outlets, subject to rollout and availability.</p></article>
        <article class="card"><h2>Delivery partners</h2><p>Delivery onboarding and assignment workflows are restricted to eligible, approved participants.</p></article>
        <article class="card"><h2>Professional and custom services</h2><p>Professional access, specialized services and custom-product workspaces may be offered where supported.</p></article>
      </section>
      <section class="card callout"><h2>Ready to explore?</h2><p>Visit the homepage to browse public information. Access to private tools requires an appropriate account.</p><a class="button" href="{{ url('/') }}">Explore TIDONG</a></section>
    @elseif($page === 'help')
      <section class="card">
        <h2>Using TIDONG: a quick guide</h2>
        <ol class="steps">
          <li><strong>Browse without signing in.</strong> Read About Us, Services, Help and Contact information as a visitor.</li>
          <li><strong>Register when available.</strong> Choose the account type appropriate for you. Registration may be paused while a module is being configured.</li>
          <li><strong>Verify and complete your profile.</strong> Follow the instructions shown after registration, including email verification or onboarding if required.</li>
          <li><strong>Wait for approval where needed.</strong> Vendor, delivery and professional functions may need additional verification.</li>
          <li><strong>Use your own dashboard.</strong> A customer, vendor, employee or partner can only use the workspace and permissions assigned to that account.</li>
        </ol>
      </section>
      <section class="grid-two">
        <article class="card"><h2>Cannot log in?</h2><p>Check your email and password and verify that your account is active and eligible. Use password recovery on the Login page if available; never share passwords or one-time codes.</p><a class="text-link" href="{{ route('login') }}">Go to Login →</a></article>
        <article class="card"><h2>Permission denied?</h2><p>A 403 message can mean the account or role does not have the required permission. Ask the platform team to review your account status; do not try another person's dashboard.</p><a class="text-link" href="{{ route('pages.contact') }}">Contact support →</a></article>
        <article class="card"><h2>Registration unavailable?</h2><p>Some registration or service modules are enabled only after administrative review. An unavailable page does not necessarily mean your existing account has been deleted.</p></article>
        <article class="card"><h2>Privacy and account safety</h2><p>Keep your login details private and provide accurate registration information. Read the website's published policies before using a transaction service.</p><a class="text-link" href="{{ route('pages.terms') }}">Terms & Conditions →</a></article>
      </section>
    @elseif($page === 'contact')
      <section class="grid-two">
        <article class="card">
          <h2>Platform operator</h2>
          <p><strong>TIDONG MARKETING PRIVATE LIMITED</strong></p>
          <p>For general platform, account access and website support enquiries, use the contact details already published on the TIDONG website.</p>
          <p><strong>Email:</strong> <a href="mailto:santoshkaankar@gmail.com">santoshkaankar@gmail.com</a></p>
          <p><strong>Phone:</strong> <a href="tel:+919634759912">+91 96347 59912</a></p>
        </article>
        <article class="card">
          <h2>What to include</h2>
          <p>Describe which page or service you were using, the approximate time and any visible error message. Do not send your password, OTP, payment PIN or other confidential credentials.</p>
          <p>Vendor and delivery enquiries should mention the relevant business or account type, without sharing sensitive internal data publicly.</p>
        </article>
      </section>
    @endif
  </div>
</main>
<footer class="site-footer"><div class="shell footer-inner">
  <p>© {{ date('Y') }} TIDONG MARKETING PRIVATE LIMITED · TIDONG® Digital</p>
  <nav aria-label="Footer navigation"><a href="{{ route('pages.about') }}">About</a><a href="{{ route('pages.services') }}">Services</a><a href="{{ route('pages.help') }}">Help</a><a href="{{ route('pages.contact') }}">Contact</a><a href="{{ route('pages.terms') }}">Terms</a></nav>
</div></footer>
</body>
</html>