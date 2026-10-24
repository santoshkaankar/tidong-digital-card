<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title', __('Customer')) | TIDONG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/tidong-customer-v2.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('css/tidong-customer-web.css') }}?v=1">
</head>
<body class="td-customer-shell min-h-screen text-slate-900">
    <a href="#main-content" class="td-skip">{{ __('Skip to main content') }}</a>
    <header class="td-header">
        <div class="td-header-inner">
            <a href="{{ route('customer.home') }}" class="td-brand" aria-label="TIDONG Customer Home">
                <img src="{{ asset('images/brand/tidong-official-logo.jpg') }}" alt="TIDONG" width="178" height="55">
                <span class="td-brand-tag">{{ __('Customer') }}</span>
            </a>
            <button type="button" class="td-menu-toggle" id="td-menu-toggle" aria-controls="td-customer-sidebar" aria-expanded="false" aria-label="{{ __('Open menu') }}">☰ <span>{{ __('Menu') }}</span></button>
            <div class="td-top-actions">
                <a class="td-top-account" href="{{ route('customer.profile.edit') }}">{{ __('Hi, :name', ['name' => \Illuminate\Support\Str::limit(auth()->user()->name, 22)]) }}</a>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button class="td-logout" type="submit">{{ __('Log out') }}</button>
                </form>
            </div>
        </div>
    </header>
    <button type="button" class="td-sidebar-backdrop" id="td-sidebar-backdrop" aria-label="{{ __('Close menu') }}" hidden></button>
    <div class="td-workspace">
        <aside class="td-side" id="td-customer-sidebar" aria-label="{{ __('Customer menu') }}">
            <button type="button" class="td-side-close" id="td-side-close" aria-label="{{ __('Close menu') }}">✕</button>
            <div class="td-side-title">{{ __('My TIDONG') }}</div>
            <nav class="td-side-nav">
                <a href="{{ route('customer.home') }}" @class(['td-side-link','is-current'=>request()->routeIs('customer.home')])>🏠 <span>{{ __('Home') }}</span></a>
                <a href="{{ route('customer.explore.index') }}" @class(['td-side-link','is-current'=>request()->routeIs('customer.explore.*')])>🧭 <span>{{ __('Explore') }}</span></a>
                @if(app(\App\Services\Platform\ModuleRegistry::class)->isEnabled('ai_restaurant_assistant') && app(\App\Services\Platform\FeatureRegistry::class)->isEnabled('ai.restaurant_assistant'))
                    <a href="{{ route('customer.ai-restaurant.index') }}" @class(['td-side-link','is-current'=>request()->routeIs('customer.ai-restaurant.*')])>🎙️ <span>{{ __('TIDONG AI') }}</span></a>
                @endif
                <a href="{{ route('customer.orders.index') }}" @class(['td-side-link','is-current'=>request()->routeIs('customer.orders.*')])>📦 <span>{{ __('My Orders') }}</span></a>
                <a href="{{ route('customer.addresses.index') }}" @class(['td-side-link','is-current'=>request()->routeIs('customer.addresses.*')])>📍 <span>{{ __('Addresses') }}</span></a>
                <a href="{{ route('customer.profile.edit') }}" @class(['td-side-link','is-current'=>request()->routeIs('customer.profile.*')])>👤 <span>{{ __('Profile') }}</span></a>
                <a href="{{ route('customer.preferences.edit') }}" @class(['td-side-link','is-current'=>request()->routeIs('customer.preferences.*')])>⚙️ <span>{{ __('Preferences') }}</span></a>
                <a href="{{ route('customer.security.edit') }}" @class(['td-side-link','is-current'=>request()->routeIs('customer.security.*')])>🔒 <span>{{ __('Security') }}</span></a>
            </nav>
            <div class="td-side-note">{{ __('Your account, your choices. All in one place.') }}</div>
        </aside>
        <main id="main-content" class="td-main">
            @if(session('status'))
                <div role="status" class="td-notice td-notice-success">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div role="alert" class="td-notice td-notice-error">
                    <p class="font-semibold">{{ __('Please correct the highlighted fields.') }}</p>
                    <ul class="ml-5 mt-2 list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    <script>
    (() => {
        const menu = document.getElementById('td-menu-toggle');
        const side = document.getElementById('td-customer-sidebar');
        const backdrop = document.getElementById('td-sidebar-backdrop');
        const close = document.getElementById('td-side-close');
        if (!menu || !side || !backdrop || !close) return;
        let lastFocus = null;
        function setOpen(open) {
            document.body.classList.toggle('td-menu-open', open);
            menu.setAttribute('aria-expanded', String(open));
            backdrop.hidden = !open;
            if (open) { lastFocus = document.activeElement; close.focus(); }
            else if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
        }
        menu.addEventListener('click', () => setOpen(!document.body.classList.contains('td-menu-open')));
        close.addEventListener('click', () => setOpen(false));
        backdrop.addEventListener('click', () => setOpen(false));
        side.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setOpen(false)));
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && document.body.classList.contains('td-menu-open')) setOpen(false); });
        window.addEventListener('resize', () => { if (window.innerWidth > 900 && document.body.classList.contains('td-menu-open')) setOpen(false); });
    })();
    </script>
    <nav class="td-bottom-nav" aria-label="{{ __('Mobile navigation') }}" hidden>
        <a href="{{ route('customer.home') }}" @class(['td-bottom-item','is-current'=>request()->routeIs('customer.home')])><span aria-hidden="true">🏠</span><span>{{ __('Home') }}</span></a>
        <a href="{{ route('customer.explore.index') }}" @class(['td-bottom-item','is-current'=>request()->routeIs('customer.explore.*')])><span aria-hidden="true">🧭</span><span>{{ __('Explore') }}</span></a>
        <a href="{{ route('customer.orders.index') }}" @class(['td-bottom-item','is-current'=>request()->routeIs('customer.orders.*')])><span aria-hidden="true">📦</span><span>{{ __('Orders') }}</span></a>
        <a href="{{ route('customer.addresses.index') }}" @class(['td-bottom-item','is-current'=>request()->routeIs('customer.addresses.*')])><span aria-hidden="true">📍</span><span>{{ __('Addresses') }}</span></a>
        <a href="{{ route('customer.profile.edit') }}" @class(['td-bottom-item','is-current'=>request()->routeIs('customer.profile.*','customer.preferences.*','customer.security.*')])><span aria-hidden="true">👤</span><span>{{ __('Account') }}</span></a>
    </nav>
</body>
</html>
