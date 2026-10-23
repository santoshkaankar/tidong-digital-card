@php
$pages = [
  'about' => ['title'=>'About TIDONG','description'=>'About TIDONG Digital and its vision for customers, businesses and service partners.'],
  'services' => ['title'=>'Services','description'=>'An introduction to the TIDONG Digital service areas described on our public website.'],
  'features' => ['title'=>'Features','description'=>'Information about digital visiting cards, catalogues, QR menus and connected business services.'],
  'guides' => ['title'=>'User Guides','description'=>'Public guidance for accessing services and using your own approved TIDONG account.'],
  'ads' => ['title'=>'Sponsored Ads','description'=>'Information about sponsored promotional spaces on the TIDONG website.'],
  'terms' => ['title'=>'Terms overview','description'=>'An introductory overview of TIDONG platform usage and partner responsibilities.'],
  'privacy' => ['title'=>'Privacy overview','description'=>'An introductory overview of the personal information used for account services and support.'],
  'contact' => ['title'=>'Contact Us','description'=>'Ways to contact the TIDONG Digital team about the platform and account access.'],
];
$details = $pages[$page] ?? null;
abort_unless($details, 404);
$links = [
  ['pages.services','Services'],['pages.features','Features'],['pages.guides','User Guides'],['pages.ads','Sponsored Ads'],
  ['pages.about','About TIDONG'],['pages.terms-overview','Terms overview'],['pages.privacy','Privacy overview'],['pages.contact','Contact Us'],
];
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="{{ $details['description'] }}">
  <meta name="robots" content="index,follow">
  <meta name="theme-color" content="#10233a">
  <link rel="canonical" href="{{ $page === 'guides' ? route('pages.guides') : url()->current() }}">
  <link rel="icon" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" href="{{ asset('css/tidong-public.css') }}">
  <title>{{ $details['title'] }} | TIDONG® Digital</title>
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
<header class="site-header"><div class="shell header-inner">
  <a class="brand" href="{{ url('/') }}" aria-label="TIDONG home"><img src="{{ asset('assets/tidong-full.jpeg') }}" alt="TIDONG Digital" width="164" height="50"></a>
  <nav aria-label="Public navigation">
    <a href="{{ url('/') }}">Home</a>
    <a href="{{ route('pages.about') }}">About</a>
    <a href="{{ route('pages.services') }}">Services</a>
    <a href="{{ route('pages.guides') }}">User Guides</a>
    <a href="{{ route('pages.contact') }}">Contact</a>
    <a class="nav-login" href="{{ route('login') }}">Login</a>
  </nav>
</div></header>
<main id="main">
<section class="hero"><div class="shell"><span class="eyebrow">TIDONG® DIGITAL</span><h1>{{ $details['title'] }}</h1><p>{{ $details['description'] }}</p></div></section>
<div class="shell content">
@if($page === 'about')
  <section class="card"><h2>About TIDONG Digital</h2>
    <p>TIDONG Digital is a connected digital business platform designed for local business owners, service providers, professionals and customers. The platform brings account-based services and business tools together in one place.</p>
    <p>Its original website concept includes digital visiting cards, QR-based food and retail catalogues, mobility, currency-information and tour-related service ideas. Some of these are historical or planned concepts and may not be operational today.</p>
    <p>TIDONG Digital is operated by TIDONG MARKETING PRIVATE LIMITED. Public information is open to visitors; private workspaces need the appropriate authorized account.</p>
  </section>
@elseif($page === 'services')
  <section class="card"><h2>Services from our original homepage</h2>
    <p>Our website introduces customer services, vendor and business workspaces, food and restaurant menus, delivery, grocery and other digital services. Account access and service availability depend on the relevant approvals and enabled features.</p>
    <p>Vendors may manage more than one business or outlet, with separate operational data. Public browsing does not provide access to private vendor or administrative information.</p>
    <p><a class="text-link" href="{{ url('/#quick-services') }}">Explore services on the homepage →</a></p>
  </section>
@elseif($page === 'features')
  <section class="grid-two">
    <article class="card"><h2>Digital identity</h2><p>Digital visiting-card concepts and shareable business or contact information are part of TIDONG's website vision, subject to feature availability.</p></article>
    <article class="card"><h2>Catalogues and QR menus</h2><p>Participating business and restaurant workflows can present item catalogues, menus and outlet-level information.</p></article>
    <article class="card"><h2>Business workspaces</h2><p>Different accounts access their own approved tools; private dashboards and business records are not public pages.</p></article>
    <article class="card"><h2>Connected services</h2><p>Service availability varies by location, business, account type and platform settings.</p></article>
  </section>
  <section class="card"><a class="text-link" href="{{ url('/#features') }}">See homepage features →</a></section>
@elseif($page === 'guides')
  <section class="card"><h2>How to use the website</h2>
    <ol class="steps">
      <li><strong>Explore:</strong> Read Services, Features, Sponsored Ads, About and Contact without logging in.</li>
      <li><strong>Account access:</strong> Use your own login details. Registration may be temporarily unavailable when the administrator has disabled a module.</li>
      <li><strong>Approval:</strong> Vendor, employee, delivery and other controlled accounts may require authorization before using their workspace.</li>
      <li><strong>Permissions:</strong> A 403 error may mean your assigned account or role cannot access a particular area.</li>
      <li><strong>Need assistance?</strong> Use the Contact Us page. Never disclose passwords, OTPs or private security information.</li>
    </ol>
    <p><a class="text-link" href="{{ url('/#instructions') }}">See homepage user guides →</a></p>
  </section>
@elseif($page === 'ads')
  <section class="card"><h2>Sponsored and promotional information</h2>
    <p>The original TIDONG homepage includes an area for sponsored advertisements and promotional content. An ad slot may be empty or unavailable at a given time.</p>
    <p>Third-party promotions are separate from TIDONG's platform instructions and account permissions. Do not share passwords or payment verification codes in response to an advertisement.</p>
    <p><a class="text-link" href="{{ url('/#ads-section') }}">View sponsored area on the homepage →</a></p>
  </section>
@elseif($page === 'terms')
  <section class="card"><h2>Terms &amp; Conditions: introduction</h2>
    <p>These points summarize the original website's Terms overview. This page is an introductory summary, not a substitute for the complete terms applicable to a transaction.</p>
    <h3>Service usage</h3><p>Users and partners should provide accurate information and must not attempt unauthorized access or misuse the platform.</p>
    <h3>Partner listings</h3><p>Participating vendors and service partners are responsible for maintaining accurate descriptions, prices and availability relevant to their listings.</p>
    <p>Applicable legal terms and consumer rights depend on the service and transaction. This overview should not be interpreted as a guarantee that any particular service is currently available.</p>
  </section>
@elseif($page === 'privacy')
  <section class="card"><h2>Privacy: introduction</h2>
    <p>The original TIDONG website included a Privacy overview for people using its services. Account identity and contact information may be processed for registration, account verification, service requests, order fulfilment, security and user support.</p>
    <p>Access to private dashboards should be limited to authorized accounts. Please do not send passwords, one-time codes or other secret credentials to support contacts.</p>
    <p>This is a public overview based on the legacy website, not a complete legally reviewed Privacy Policy. For data questions, please contact TIDONG directly.</p>
  </section>
@elseif($page === 'contact')
  <section class="grid-two">
    <article class="card"><h2>TIDONG MARKETING PRIVATE LIMITED</h2>
      <p><strong>Phone:</strong> <a href="tel:+919634759912">+91 96347 59912</a></p>
      <p><strong>Email:</strong> <a href="mailto:santoshkaankar@gmail.com">santoshkaankar@gmail.com</a></p>
      <p><a class="button" href="https://wa.me/919634759912" rel="noopener noreferrer" target="_blank">Chat on WhatsApp</a></p>
    </article>
    <article class="card"><h2>Getting help</h2><p>For an account or website issue, mention the page and the error message you saw. Never share account passwords, payment PINs or OTPs.</p></article>
  </section>
@endif
</div></main>
<footer class="site-footer"><div class="shell footer-inner">
  <p>© {{ date('Y') }} TIDONG MARKETING PRIVATE LIMITED · TIDONG® Digital</p>
  <nav aria-label="Footer links">
    @foreach($links as [$routeName,$label])
      <a href="{{ route($routeName) }}">{{ $label }}</a>
    @endforeach
  </nav>
</div></footer>
</body></html>