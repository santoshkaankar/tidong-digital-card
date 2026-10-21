<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#121b32">
    <meta name="description" content="TIDONG brings people, businesses and their everyday operations together on one digital platform.">
    <title>TIDONG | One Platform. Endless Possibilities.</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        :root{--navy:#111b32;--ink:#16213a;--muted:#667189;--coral:#ef5164;--coral-dark:#dc4055;--cream:#fffaf5;--paper:#fff;--line:#e8edf3;--blue:#4070e9;--green:#0b9c76;--shadow:0 20px 55px rgba(17,27,50,.08)}
        *,*::before,*::after{box-sizing:border-box}
        html{scroll-behavior:smooth;scroll-padding-top:90px}
        body{margin:0;font-family:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;color:var(--ink);background:#fff;line-height:1.58}
        a{color:inherit;text-decoration:none}
        button{font:inherit;cursor:pointer}
        img{max-width:100%;display:block}
        .wrap{width:min(1160px,calc(100% - 40px));margin-inline:auto}
        .skip{position:absolute;top:-60px;left:15px;background:#fff;color:#111;padding:10px 15px;z-index:100}
        .skip:focus{top:12px}
        :focus-visible{outline:3px solid #ffbd62;outline-offset:4px}
        .topline{height:5px;background:linear-gradient(90deg,#ef5164,#f6ab5e,#5573e7)}
        .site-header{position:sticky;top:0;z-index:30;background:rgba(255,255,255,.96);border-bottom:1px solid var(--line);backdrop-filter:blur(16px)}
        .nav{display:flex;align-items:center;justify-content:space-between;min-height:76px;gap:24px}
        .brand{display:flex;align-items:center;gap:11px;font-weight:900;letter-spacing:.015em;font-size:26px;color:var(--navy);white-space:nowrap}
        .brand-mark{width:42px;height:42px;border-radius:12px;background:var(--coral);display:grid;place-items:center;color:#fff;font-weight:900;font-size:25px;box-shadow:0 7px 19px rgba(239,81,100,.23)}
        .brand span em{color:var(--coral);font-style:normal}
        .nav-links{display:flex;align-items:center;gap:25px;color:#435068;font-size:14px;font-weight:650}
        .nav-links a:hover,.foot-links a:hover{color:var(--coral)}
        .nav-actions{display:flex;align-items:center;gap:12px}
        .small-user{font-size:13px;font-weight:650;color:#526078;max-width:155px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .btn{display:inline-flex;justify-content:center;align-items:center;gap:10px;padding:13px 22px;border:1px solid transparent;border-radius:12px;font-weight:750;font-size:14px;transition:transform .18s,background .18s,border-color .18s;text-align:center;line-height:1.35;white-space:nowrap}
        .btn:hover{transform:translateY(-2px)}
        .btn-main{background:var(--coral);color:#fff;box-shadow:0 8px 22px rgba(239,81,100,.22)}
        .btn-main:hover{background:var(--coral-dark)}
        .btn-outline{background:#fff;color:var(--navy);border-color:#dce2ec}
        .btn-outline:hover{border-color:var(--coral)}
        .btn-dark{background:var(--navy);color:#fff}
        .btn-dark:hover{background:#26395d}
        .link-button{background:none;border:0;font-size:13px;font-weight:750;color:#526078;padding:5px}
        .link-button:hover{color:var(--coral)}
        .hero{background:radial-gradient(circle at 75% 10%,#ffe5da 0,transparent 35%),linear-gradient(125deg,#fff9f5 0%,#fff 52%,#f4f7ff 100%);overflow:hidden;border-bottom:1px solid #f2edf0}
        .hero-grid{display:grid;grid-template-columns:1.06fr .94fr;align-items:center;gap:56px;min-height:570px;padding-block:80px 76px}
        .eyebrow{display:inline-flex;align-items:center;gap:9px;background:#fff;border:1px solid #f2dee0;border-radius:99px;padding:8px 13px;color:#9a3b4d;font-weight:800;letter-spacing:.08em;font-size:11px;text-transform:uppercase}
        .eyebrow i{height:8px;width:8px;border-radius:50%;background:var(--coral)}
        h1,h2,h3,p{margin-top:0}
        h1{font-size:clamp(42px,5vw,68px);letter-spacing:-.058em;line-height:1.075;margin:22px 0 22px;font-weight:850;color:var(--navy)}
        h1 .accent{color:var(--coral)}
        .lead{color:#59667c;font-size:18px;line-height:1.85;max-width:580px;margin-bottom:30px}
        .hero-actions{display:flex;gap:12px;flex-wrap:wrap}
        .trust{display:flex;align-items:center;gap:10px;color:#687389;margin-top:30px;font-size:13px;font-weight:600}
        .trust .tick{height:24px;width:24px;border-radius:50%;display:grid;place-items:center;background:#e7f6ee;color:#087655;font-weight:900}
        .visual{position:relative;padding:18px 0 20px 22px}
        .visual::before{content:"";position:absolute;inset:38px 20px 25px 30px;border-radius:35px;background:linear-gradient(140deg,#ffe4db,#eeeaff);transform:rotate(-7deg)}
        .dashboard{position:relative;background:white;border:1px solid #e7e9ef;border-radius:24px;padding:24px;box-shadow:0 28px 70px rgba(36,48,79,.18)}
        .dash-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;gap:10px}
        .dash-head strong{font-size:15px;color:var(--navy)}
        .dash-head small{font-size:11px;color:#8791a3}
        .live-pill{border-radius:99px;background:#e7f7ee;color:#167d57;font-size:10px;font-weight:800;padding:6px 10px}
        .dash-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}
        .dash-tile{border:1px solid #edf0f5;border-radius:15px;padding:17px;background:#fbfcff}
        .tile-icon{font-size:24px;display:grid;place-items:center;width:44px;height:44px;background:#f0f3fd;border-radius:12px;margin-bottom:13px}
        .dash-tile strong{display:block;font-size:13px;color:#263651;margin-bottom:4px}
        .dash-tile span:last-child{display:block;color:#8791a3;font-size:11px}
        .dash-banner{display:flex;align-items:center;gap:14px;margin-top:15px;background:#1a2947;color:#fff;padding:13px;border-radius:14px}
        .dash-banner .banner-icon{display:grid;place-items:center;background:#344362;border-radius:10px;width:43px;height:43px;font-size:23px}
        .dash-banner strong{display:block;font-size:12px}
        .dash-banner span{font-size:11px;color:#b9c5dc}
        .floating{position:absolute;bottom:-10px;left:-10px;display:flex;align-items:center;gap:9px;padding:11px 14px;background:#fff;border:1px solid #edf0f5;border-radius:12px;box-shadow:0 12px 32px #1425431a;font-weight:750;font-size:12px}
        .floating b{background:#e6f5ed;color:#0e9868;border-radius:8px;padding:6px}
        .section{padding:88px 0}
        .section-soft{background:#f7f9fd}
        .section-head{display:flex;justify-content:space-between;gap:30px;align-items:end;margin-bottom:35px}
        .overline{font-size:12px;font-weight:850;letter-spacing:.17em;color:var(--coral);text-transform:uppercase;margin-bottom:12px}
        h2{font-size:clamp(29px,3.2vw,42px);letter-spacing:-.04em;line-height:1.2;margin-bottom:13px;color:var(--navy)}
        .section-sub{font-size:16px;color:#6a7487;max-width:660px;margin:0;line-height:1.8}
        .pill{display:inline-block;border:1px solid #dce3ee;border-radius:99px;color:#5e6a81;background:white;font-size:12px;font-weight:750;padding:7px 12px;white-space:nowrap}
        .feature-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}
        .feature{position:relative;background:#fff;border:1px solid #e6ebf3;border-radius:20px;padding:29px;box-shadow:0 8px 24px rgba(22,33,58,.025);transition:transform .18s,box-shadow .18s}
        .feature:hover{transform:translateY(-4px);box-shadow:var(--shadow)}
        .feature-icon{width:57px;height:57px;border-radius:16px;display:grid;place-items:center;font-size:27px;margin-bottom:23px;background:#fff0e7}
        .feature:nth-child(2) .feature-icon{background:#e9f3ff}.feature:nth-child(3) .feature-icon{background:#e8f6ef}
        .feature:nth-child(4) .feature-icon{background:#f1e9ff}.feature:nth-child(5) .feature-icon{background:#fff3df}.feature:nth-child(6) .feature-icon{background:#e9f0ff}
        .feature h3{font-size:20px;letter-spacing:-.025em;margin-bottom:9px;color:var(--navy)}
        .feature p{font-size:14px;color:#6a7487;line-height:1.8;margin-bottom:23px;min-height:76px}
        .feature-link{font-size:13px;font-weight:800;color:var(--coral);display:inline-flex;align-items:center;gap:7px}
        .feature-link:hover{text-decoration:underline}
        .status{font-size:11px;font-weight:750;color:#6b778b;background:#f0f3f7;padding:5px 9px;border-radius:99px;display:inline-block;margin-bottom:16px}
        .showcase{display:grid;grid-template-columns:1fr 1fr;align-items:center;gap:58px}
        .showcase-photos{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        .food-img{aspect-ratio:1.24;object-fit:cover;width:100%;border-radius:19px;box-shadow:0 10px 30px rgba(17,27,50,.1)}
        .food-img:nth-child(2){transform:translateY(20px)}.food-img:nth-child(3){transform:translateY(-5px)}.food-img:nth-child(4){transform:translateY(15px)}
        .point{display:flex;gap:12px;align-items:start;margin-top:20px;font-size:14px;color:#5c6980}
        .point strong{display:block;color:#23314a;font-size:15px}
        .point .check{width:27px;height:27px;border-radius:50%;flex-shrink:0;background:#e5f5ee;color:#09885e;display:grid;place-items:center;font-weight:900}
        .workspace{background:var(--navy);color:#fff;border-radius:28px;padding:46px 48px;display:flex;align-items:center;justify-content:space-between;gap:40px;position:relative;overflow:hidden}
        .workspace:after{content:"";position:absolute;width:260px;height:260px;border:55px solid #ffffff08;border-radius:50%;right:18%;top:-75px;pointer-events:none}
        .workspace h2{color:#fff;margin-bottom:10px}.workspace p{color:#c0c9dc;max-width:650px;margin:0}
        .workspace .btn{position:relative;z-index:1}
        .modules{display:flex;flex-wrap:wrap;gap:10px;margin-top:20px}
        .modules span{border:1px solid #ffffff36;color:#dce4f3;border-radius:30px;padding:7px 13px;font-size:12px;font-weight:650}
        .upcoming{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-top:27px}
        .upcoming div{background:#fff;border:1px solid #e9edf4;padding:20px;border-radius:15px}
        .upcoming strong{display:block;font-size:15px;color:#26344d;margin-bottom:6px}.upcoming span{font-size:12px;color:#8a93a3}
        .site-footer{background:#101a30;color:#fff;padding:60px 0 25px}
        .footer-grid{display:flex;justify-content:space-between;gap:35px;align-items:start;padding-bottom:40px}
        .footer-brand{color:#fff}.footer-intro{color:#adbbd1;font-size:14px;max-width:420px;margin:15px 0 0;line-height:1.9}
        .foot-links{display:flex;gap:22px;flex-wrap:wrap;color:#bfcbde;font-size:13px;font-weight:650}
        .footer-bottom{border-top:1px solid #ffffff1f;padding-top:23px;color:#91a0b8;font-size:12px;display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap}
        @media(max-width:950px){.nav-links{display:none}.hero-grid{gap:30px}.feature-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.showcase{gap:30px}}
        @media(max-width:720px){.wrap{width:min(100% - 32px,1160px)}.nav{min-height:67px}.brand{font-size:23px}.brand-mark{height:38px;width:38px}.small-user{display:none}.nav-actions .btn{padding:11px 13px;font-size:12px}.hero-grid{grid-template-columns:1fr;padding:56px 0 64px;min-height:auto}.hero-grid .visual{max-width:530px;width:100%;margin-inline:auto}.lead{font-size:16px}.section{padding:65px 0}.section-head{display:block}.section-head .pill{margin-top:18px}.feature-grid{grid-template-columns:1fr}.feature p{min-height:0}.showcase{grid-template-columns:1fr}.showcase-photos{max-width:550px;width:100%;margin:20px auto 6px}.workspace{display:block;padding:35px 26px}.workspace .btn{margin-top:25px}.upcoming{grid-template-columns:repeat(2,minmax(0,1fr))}.footer-grid{display:block}.foot-links{margin-top:25px}}
        @media(max-width:420px){.hero-actions .btn{width:100%}.visual{padding:15px 4px}.dashboard{padding:15px}.dash-tile{padding:12px}.dash-tile strong{font-size:11px}.floating{left:0}.upcoming{grid-template-columns:1fr}.nav-actions .link-button{font-size:12px;padding:2px}}
        @media(prefers-reduced-motion:reduce){html{scroll-behavior:auto}*,*::before,*::after{transition:none!important}}
    </style>
</head>
<body>
@php
    // Public landing page remains database-free for guests. Private workspaces
    // are linked only for users carrying an active matching account type.
    $tidongAccountCodes = [];
    if (auth()->check()) {
        $tidongAccountCodes = auth()->user()->userAccountTypes()
            ->where('status', 'active')
            ->whereHas('accountType', fn ($query) => $query->where('is_active', true))
            ->with('accountType')
            ->get()
            ->pluck('accountType.code')
            ->all();
    }
    $tidongWorkspaceHref = [];
    foreach (['customer' => 'customer.home', 'vendor' => 'vendor.home',
              'delivery' => 'delivery.home', 'pos' => 'pos.home',
              'kds' => 'kds.home'] as $section => $routeName) {
        $accountType = in_array($section, ['pos', 'kds'], true) ? 'vendor' : $section;
        $tidongWorkspaceHref[$section] = in_array($accountType, $tidongAccountCodes, true)
            ? route($routeName)
            : (auth()->check() ? '#access' : route('login'));
    }
@endphp
<a class="skip" href="#main">Skip to content</a>
<div class="topline" aria-hidden="true"></div>
<header class="site-header">
    <div class="wrap nav">
        <a href="{{ url('/') }}" class="brand" aria-label="TIDONG home"><span class="brand-mark" aria-hidden="true">T</span><span>TID<em>ONG</em></span></a>
        <nav class="nav-links" aria-label="Main navigation"><a href="#solutions">Solutions</a><a href="#restaurants">Restaurants</a><a href="#access">Workspaces</a><a href="#about">About</a></nav>
        <div class="nav-actions">
            @guest
                <a class="btn btn-outline" href="{{ route('login') }}">Sign in <span aria-hidden="true">→</span></a>
            @else
                <span class="small-user" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="link-button">Sign out</button></form>
            @endguest
        </div>
    </div>
</header>
<main id="main">
    <section class="hero" aria-labelledby="hero-title">
        <div class="wrap hero-grid">
            <div>
                <span class="eyebrow"><i aria-hidden="true"></i> TIDONG DIGITAL PLATFORM</span>
                <h1 id="hero-title">One platform.<br><span class="accent">More possibilities.</span></h1>
                <p class="lead">Connecting customers, businesses, teams and delivery operations in one growing digital ecosystem — designed to make everyday work simpler.</p>
                <div class="hero-actions">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-main">Access your account <span aria-hidden="true">↗</span></a>
                    @else
                        <a href="#access" class="btn btn-main">Open your workspace <span aria-hidden="true">↗</span></a>
                    @endguest
                    <a href="#solutions" class="btn btn-outline">Explore the platform <span aria-hidden="true">↓</span></a>
                </div>
                <div class="trust"><span class="tick" aria-hidden="true">✓</span> Dedicated workspaces · Role-based access · Business tools</div>
            </div>
            <div class="visual" aria-label="Overview of TIDONG platform workspaces">
                <div class="dashboard">
                    <div class="dash-head"><div><strong>TIDONG Workspaces</strong><br><small>Connected business ecosystem</small></div><span class="live-pill">Platform</span></div>
                    <div class="dash-grid">
                        <div class="dash-tile"><span class="tile-icon" aria-hidden="true">🛍️</span><strong>Customer</strong><span>Personal access</span></div>
                        <div class="dash-tile"><span class="tile-icon" aria-hidden="true">🏬</span><strong>Business</strong><span>Vendor workspace</span></div>
                        <div class="dash-tile"><span class="tile-icon" aria-hidden="true">🚚</span><strong>Delivery</strong><span>Partner operations</span></div>
                        <div class="dash-tile"><span class="tile-icon" aria-hidden="true">🍽️</span><strong>Restaurant</strong><span>POS &amp; kitchen tools</span></div>
                    </div>
                    <div class="dash-banner"><span class="banner-icon" aria-hidden="true">🔐</span><div><strong>Secure, permission-based access</strong><span>Each team accesses its own authorized tools</span></div></div>
                </div>
                <div class="floating"><b aria-hidden="true">✓</b> Connected. Organized. TIDONG.</div>
            </div>
        </div>
    </section>
    <section id="solutions" class="section section-soft" aria-labelledby="solutions-title">
        <div class="wrap">
            <div class="section-head"><div><p class="overline">What you can do</p><h2 id="solutions-title">A connected platform for everyone</h2><p class="section-sub">Explore the areas of the platform. Access to private workspaces depends on your account and permissions.</p></div><span class="pill">Built around your work</span></div>
            <div class="feature-grid">
                <article class="feature"><div class="feature-icon" aria-hidden="true">🛍️</div><h3>Customer Workspace</h3><p>Access your customer dashboard and available account services.</p><a class="feature-link" href="{{ $tidongWorkspaceHref['customer'] }}">{{ in_array('customer', $tidongAccountCodes, true) ? 'Customer dashboard' : (auth()->check() ? 'Check access' : 'Sign in to access') }} <span aria-hidden="true">→</span></a></article>
                <article class="feature"><div class="feature-icon" aria-hidden="true">🏪</div><h3>Vendor &amp; Business</h3><p>Manage your business workspace, branches and available operational tools.</p><a class="feature-link" href="{{ $tidongWorkspaceHref['vendor'] }}">{{ in_array('vendor', $tidongAccountCodes, true) ? 'Vendor workspace' : (auth()->check() ? 'Check access' : 'Sign in to access') }} <span aria-hidden="true">→</span></a></article>
                <article class="feature"><div class="feature-icon" aria-hidden="true">🚚</div><h3>Delivery Partners</h3><p>Open the delivery partner dashboard for approved, authorized partners.</p><a class="feature-link" href="{{ $tidongWorkspaceHref['delivery'] }}">{{ in_array('delivery', $tidongAccountCodes, true) ? 'Delivery dashboard' : (auth()->check() ? 'Check access' : 'Sign in to access') }} <span aria-hidden="true">→</span></a></article>
                <article class="feature"><div class="feature-icon" aria-hidden="true">🧾</div><h3>Restaurant POS</h3><p>Branch-level restaurant point-of-sale and menu management tools.</p><a class="feature-link" href="{{ $tidongWorkspaceHref['pos'] }}">{{ in_array('vendor', $tidongAccountCodes, true) ? 'Open POS' : (auth()->check() ? 'Check access' : 'Sign in to access') }} <span aria-hidden="true">→</span></a></article>
                <article class="feature"><div class="feature-icon" aria-hidden="true">👨‍🍳</div><h3>Kitchen Display</h3><p>Manage restaurant kitchen workflows through the KDS workspace.</p><a class="feature-link" href="{{ $tidongWorkspaceHref['kds'] }}">{{ in_array('vendor', $tidongAccountCodes, true) ? 'Open KDS' : (auth()->check() ? 'Check access' : 'Sign in to access') }} <span aria-hidden="true">→</span></a></article>
                <article class="feature"><div class="feature-icon" aria-hidden="true">🛡️</div><h3>Platform Administration</h3><p>Protected administration for authorized platform teams and employees.</p><a class="feature-link" href="#access">View protected access <span aria-hidden="true">→</span></a></article>
            </div>
        </div>
    </section>
    <section id="restaurants" class="section" aria-labelledby="restaurant-title">
        <div class="wrap showcase">
            <div class="showcase-photos" aria-label="Example restaurant menu items"><img class="food-img" src="{{ asset('images/global-menu/masala-dosa.webp') }}" alt="Masala dosa" loading="lazy"><img class="food-img" src="{{ asset('images/global-menu/paneer-butter-masala.webp') }}" alt="Paneer butter masala" loading="lazy"><img class="food-img" src="{{ asset('images/global-menu/veg-biryani.webp') }}" alt="Vegetable biryani" loading="lazy"><img class="food-img" src="{{ asset('images/global-menu/masala-chai.webp') }}" alt="Masala chai" loading="lazy"></div>
            <div><p class="overline">Restaurant operations</p><h2 id="restaurant-title">Made for the moments that matter.</h2><p class="section-sub">From maintaining menus to managing tables, orders and kitchen workflows, TIDONG connects the restaurant tools already built into your business workspace.</p><div class="point"><span class="check" aria-hidden="true">✓</span><div><strong>Menu &amp; table management</strong>Keep branch operations organized with dedicated POS tools.</div></div><div class="point"><span class="check" aria-hidden="true">✓</span><div><strong>Kitchen workflow</strong>Route authorized staff to existing kitchen display screens.</div></div><div class="point"><span class="check" aria-hidden="true">✓</span><div><strong>QR menu access</strong>Customers use a restaurant-provided QR link to view that restaurant's menu.</div></div><a class="btn btn-dark" style="margin-top:27px" href="{{ $tidongWorkspaceHref['pos'] }}">{{ in_array('vendor', $tidongAccountCodes, true) ? 'Go to restaurant POS' : (auth()->check() ? 'Check access' : 'Sign in to manage') }} <span aria-hidden="true">→</span></a></div>
        </div>
    </section>
    <section id="access" class="section section-soft" aria-labelledby="access-title">
        <div class="wrap"><div class="workspace"><div><p class="overline" style="color:#ff9aa6">Your workspace</p><h2 id="access-title">Your account. Your tools.</h2><p>Use your approved account to access only the workspaces assigned to you. Business and administrative access is protected by existing project permissions.</p><div class="modules"><span>Customer</span><span>Vendor</span><span>Delivery</span><span>POS</span><span>KDS</span><span>Admin</span></div></div>
                @guest
                    <a href="{{ route('login') }}" class="btn btn-main">Sign in to continue <span aria-hidden="true">→</span></a>
                @else
                    <a href="#your-workspaces" class="btn btn-main">Choose your workspace <span aria-hidden="true">↓</span></a>
                @endguest
            </div>
            @auth
                <div id="your-workspaces" style="margin-top:36px">
                    <h3 style="font-size:22px;margin-bottom:7px">Your available workspace links</h3>
                    <p class="section-sub">Only approved accounts can enter these protected dashboards.</p>
                    <div class="upcoming">
                        @if(in_array('customer', $tidongAccountCodes, true))<div><strong>Customer</strong><a class="feature-link" href="{{ route('customer.home') }}">Open dashboard →</a></div>@endif
                        @if(in_array('vendor', $tidongAccountCodes, true))
                            <div><strong>Vendor</strong><a class="feature-link" href="{{ route('vendor.home') }}">Open workspace →</a></div>
                            <div><strong>Restaurant POS</strong><a class="feature-link" href="{{ route('pos.home') }}">Open POS →</a></div>
                            <div><strong>Kitchen Display</strong><a class="feature-link" href="{{ route('kds.home') }}">Open KDS →</a></div>
                        @endif
                        @if(in_array('delivery', $tidongAccountCodes, true))<div><strong>Delivery Partner</strong><a class="feature-link" href="{{ route('delivery.home') }}">Open workspace →</a></div>@endif
                        @if(in_array('platform_admin', $tidongAccountCodes, true))<div><strong>Super Admin</strong><a class="feature-link" href="{{ route('home') }}">Open dashboard →</a></div>@endif
                        @if(in_array('admin', $tidongAccountCodes, true))<div><strong>Platform Admin</strong><a class="feature-link" href="{{ route('staff-admin.home') }}">Open dashboard →</a></div>@endif
                        @if(in_array('platform_employee', $tidongAccountCodes, true))<div><strong>Platform Employee</strong><a class="feature-link" href="{{ route('employee.home') }}">Open dashboard →</a></div>@endif
                    </div>
                </div>
            @endauth
        </div>
    </section>
    <section id="about" class="section" aria-labelledby="about-title">
        <div class="wrap"><div class="section-head"><div><p class="overline">What's ahead</p><h2 id="about-title">One vision. Growing possibilities.</h2><p class="section-sub">More experiences are being developed for the TIDONG ecosystem. These areas are not advertised as live ordering or playable services until their own modules are ready.</p></div></div>
            <div class="upcoming"><div><strong>Marketplace &amp; Grocery</strong><span>In development</span></div><div><strong>TIDONG Virtual Life</strong><span>In development</span></div><div><strong>Sublimation Services</strong><span>Business-specific access</span></div><div><strong>Additional Digital Services</strong><span>Coming in phases</span></div></div>
        </div>
    </section>
</main>
<footer class="site-footer"><div class="wrap"><div class="footer-grid"><div><a href="{{ url('/') }}" class="brand footer-brand"><span class="brand-mark" aria-hidden="true">T</span><span>TID<em>ONG</em></span></a><p class="footer-intro">A unified digital ecosystem for customers, vendors, partners and business teams.</p></div><nav class="foot-links" aria-label="Footer navigation"><a href="#solutions">Solutions</a><a href="#restaurants">Restaurants</a><a href="#access">Workspaces</a><a href="{{ auth()->check() ? '#access' : route('login') }}">{{ auth()->check() ? 'Workspaces' : 'Account sign in' }}</a></nav></div><div class="footer-bottom"><span>© {{ date('Y') }} TIDONG MARKETING PRIVATE LIMITED. All rights reserved.</span><span>Made for connected possibilities.</span></div></div></footer>
</body>
</html>
