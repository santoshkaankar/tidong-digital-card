<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#1e293b"><meta name="description" content="TIDONG digital marketplace, customer, vendor and delivery services"><title>TIDONG® Digital | Connected Services</title>
<link rel="icon" href="{{ asset('favicon.ico') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/tidong-home.css') }}?v=20261021-home1">
</head><body class="tidong-home">
@php
// Account badges on the public services list remain informational only.
$tidongAccountCodes = auth()->check()
    ? auth()->user()->userAccountTypes()->where('status', 'active')
        ->whereHas('accountType', fn ($query) => $query->where('is_active', true))
        ->with('accountType')->get()->pluck('accountType.code')->all()
    : [];
$dashboardName=auth()->check()
    ? app(\App\Services\Auth\DashboardRedirector::class)->routeFor(auth()->user())
    : null;
$dashboardRoute=$dashboardName ? route($dashboardName) : url('/');
$publicRegistrationEnabled=true; // existing login page states public registration closed
$groceryModuleEnabled=false;
try { $groceryModuleEnabled=app(\App\Services\Platform\ModuleRegistry::class)->isEnabled('grocery'); } catch (\Throwable $exception) { $groceryModuleEnabled=false; } // Never expose a disabled grocery page
@endphp
<a class="visually-hidden-focusable" href="#quick-services">Skip to services</a>
@include('partials.welcome.navbar')
<main id="main">
@include('partials.welcome.hero')
@include('partials.welcome.services')
@include('partials.welcome.carousel-ads')
@include('partials.welcome.qr-section')
@include('partials.welcome.features')
@include('partials.welcome.instructions-grid')
@include('partials.welcome.sponsored-ads')
@include('partials.welcome.tabs-welcome-section')
</main>
@includeIf('global.partials.voice_assistant')
@include('partials.welcome.modals')
@include('partials.welcome.footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="{{ asset('js/tidong-home.js') }}?v=20261021-home1" defer></script>
</body></html>
