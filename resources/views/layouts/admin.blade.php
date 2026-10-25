<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Digital Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen overflow-x-hidden bg-slate-100 text-slate-800">

    <div class="min-h-screen md:flex">

        {{-- Mobile overlay --}}
        <div id="sidebar-overlay"
             class="fixed inset-0 z-40 hidden bg-black/50 md:hidden"
             aria-hidden="true"></div>

        {{-- Sidebar --}}
        <aside id="admin-sidebar"
               class="fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] -translate-x-full flex-col overflow-y-auto bg-slate-900 text-white transition-transform duration-200 md:sticky md:top-0 md:h-screen md:w-64 md:max-w-none md:shrink-0 md:translate-x-0">

            <div class="flex shrink-0 items-center justify-between border-b border-slate-700 px-5 py-5">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold">Digital Platform</h1>
                    <p class="mt-1 text-sm text-slate-400">Super Admin Panel</p>
                </div>

                <button id="sidebar-close"
                        type="button"
                        class="ml-3 rounded-lg px-3 py-2 text-xl text-white hover:bg-slate-800 md:hidden"
                        aria-label="Close navigation menu">
                    &times;
                </button>
            </div>

            <nav class="flex-1 space-y-1 p-4">
                <a href="{{ route('home') }}"
                   class="block rounded-lg bg-blue-600 px-4 py-3 font-medium">
                    Dashboard
                </a>

                <p class="px-4 pb-1 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Management
                </p>

                <a href="{{ route('admin.users.index') }}" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Users Management</a>
                <a href="{{ route('admin.vendors.index') }}" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Vendors Management</a>
                <a href="{{ route('admin.businesses.index') }}" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Businesses Approval</a>
                <a href="{{ route('admin.branch-changes.index') }}" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Branch Change Approvals</a>
                <p class="px-4 pb-1 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">Global Catalog Masters</p>
                @foreach(['brands'=>'Brand Master','item_categories'=>'Item Categories','units'=>'Units of Measure','variant_attributes'=>'Variant Attributes','variant_values'=>'Variant Values'] as $masterType=>$masterLabel)
                    <a href="{{ route('admin.global-masters.index',['type'=>$masterType]) }}" class="block rounded-lg px-4 py-2 hover:bg-slate-800">{{ $masterLabel }}</a>
                @endforeach
                <a href="{{ route('admin.global-master-permissions.index') }}" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Vendor Global Permissions</a>
                <a href="{{ route('admin.global-menu.index') }}" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Global Item Master</a>

                <p class="px-4 pb-1 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Platform Technical Controls
                </p>

                <a href="{{ route('admin.modules.index') }}" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Module Management</a>
                <a href="{{ route('admin.employee-permissions.index') }}" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Admin Employee Permissions</a>
                <a href="{{ route('admin.delivery.index') }}" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Delivery Diagnostics</a>
                <a href="#" class="block rounded-lg px-4 py-2 hover:bg-slate-800">POS & KDS</a>
                <a href="#" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Marriage Planner</a>
                <a href="#" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Game Management</a>

                <p class="px-4 pb-1 pt-5 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    Finance & Security
                </p>

                <a href="#" class="block rounded-lg px-4 py-2 hover:bg-slate-800">INR Wallet & T-Coin</a>
                <a href="#" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Affiliate & Royalty</a>
                <a href="#" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Roles & Permissions</a>
                <a href="#" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Audit Logs</a>
                <a href="#" class="block rounded-lg px-4 py-2 hover:bg-slate-800">Settings</a>
            </nav>

            <div class="shrink-0 border-t border-slate-700 p-4">
                <p class="mb-3 break-words text-sm text-slate-300">
                    {{ auth()->user()->name ?? 'Administrator' }}
                </p>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-lg bg-red-600 px-4 py-3 text-sm font-medium hover:bg-red-700">
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="min-w-0 flex-1">

            {{-- Header --}}
            <header class="flex min-w-0 items-center gap-3 border-b border-slate-200 bg-white px-4 py-3 sm:px-6">

                <button id="sidebar-open"
                        type="button"
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-100 md:hidden"
                        aria-label="Open navigation menu"
                        aria-controls="admin-sidebar"
                        aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-6 w-6"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="min-w-0 flex-1">
                    <h2 class="break-words text-base font-semibold sm:text-lg">
                        @yield('page_heading', 'Dashboard')
                    </h2>
                    <p class="text-xs text-slate-500 sm:text-sm">
                        Platform Administration
                    </p>
                </div>

                <span class="shrink-0 rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 sm:px-3 sm:text-sm">
                    Super Admin
                </span>
            </header>

            <main class="min-w-0 p-3 sm:p-4 md:p-8">
                @yield('content')
            </main>
        </div>

    </div>

    {{-- Mobile menu behavior --}}
    <script>
        (() => {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const openButton = document.getElementById('sidebar-open');
            const closeButton = document.getElementById('sidebar-close');

            if (!sidebar || !overlay || !openButton || !closeButton) return;

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                openButton.setAttribute('aria-expanded', 'true');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                openButton.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('overflow-hidden');
            }

            openButton.addEventListener('click', openSidebar);
            closeButton.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);

            sidebar.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 768) closeSidebar();
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeSidebar();
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    overlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    openButton.setAttribute('aria-expanded', 'false');
                } else {
                    closeSidebar();
                }
            });
        })();
    </script>

</body>
</html>
