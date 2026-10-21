@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.platform-ops')
@section('title', 'Module Management')
@section('page_heading', 'Module Management')
@section('content')
<div class="max-w-5xl space-y-5">
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <h1 class="text-xl font-bold">Installed Modules</h1>
        <p class="mt-2 text-sm text-slate-600">Only deployed and reviewed modules and enforceable features can be managed. Company operations remain vendor-controlled. Only deployed and reviewed modules can be added. Switching OFF blocks module routes server-side; existing records stay intact. Enabling does not publish or install new code.</p>
    </div>
    @if(session('success'))<p class="rounded bg-green-50 p-3 text-green-900">{{ session('success') }}</p>@endif
    @if($errors->any())<p class="rounded bg-red-50 p-3 text-red-800">{{ $errors->first() }}</p>@endif
    <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="bg-slate-100"><tr><th class="p-4">Module</th><th class="p-4">Deployment</th><th class="p-4">Status</th><th class="p-4">Action</th></tr></thead>
            <tbody>
            @foreach($modules as $module)
                <tr class="border-t"><td class="p-4"><strong>{{ $module['name'] }}</strong><div class="text-xs text-slate-500">{{ $module['code'] }}</div></td>
                    <td class="p-4">{{ $module['installed'] ? 'Installed' : 'Not installed' }}</td>
                    <td class="p-4">{{ ! $module['installed'] ? 'Unavailable' : ($module['enabled'] ? 'Enabled' : 'Disabled') }}{{ ! $module['registered'] ? ' (not registered)' : '' }}</td>
                    <td class="p-4">
                        @if($module['installed'] && ! $module['registered'] && app(\App\Services\Platform\PlatformCapability::class)->allows(auth()->user(), 'modules.register'))
                            <form method="POST" action="{{ route(explode('.', request()->route()->getName())[0].'.modules.register', $module['code']) }}">@csrf<button class="rounded bg-indigo-700 px-3 py-2 text-white" type="submit">Add to Management</button></form>
                        @elseif($module['installed'] && $module['registered'] && app(\App\Services\Platform\PlatformCapability::class)->allows(auth()->user(), 'modules.toggle'))
                            <form method="POST" action="{{ route(explode('.', request()->route()->getName())[0].'.modules.toggle', $module['code']) }}">@csrf<input type="hidden" name="enabled" value="{{ $module['enabled'] ? '0' : '1' }}"><button class="rounded px-3 py-2 text-white {{ $module['enabled'] ? 'bg-red-700' : 'bg-green-700' }}" type="submit">{{ $module['enabled'] ? 'Turn OFF' : 'Turn ON' }}</button></form>
                        @else
                            <span class="text-slate-500">Developer deployment required</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <section class="rounded-xl bg-white p-5 shadow-sm space-y-3">
      <h2 class="text-lg font-bold">Website Feature Availability</h2>
      <p class="text-sm text-slate-600">Global technical switches only. Vendor operational permissions and business-wise decisions are never granted by these controls. Future features appear here only after backend enforcement is reviewed.</p>
      <div class="space-y-3">@foreach($features as $feature)
        <div class="flex flex-wrap items-center justify-between gap-3 border-b pb-3">
          <div><strong>{{ $feature['name'] }}</strong><div class="text-xs text-slate-500">{{ $feature['code'] }} | module: {{ $feature['module'] }}</div><div class="text-sm">{{ ! $feature['module_enabled'] ? 'Parent Module OFF' : ($feature['enabled'] ? 'ON' : 'OFF') }}</div></div>
          @if(app(\App\Services\Platform\PlatformCapability::class)->allows(auth()->user(), 'features.toggle'))
            <form method="POST" action="{{ route(explode('.', request()->route()->getName())[0].'.modules.features.toggle', $feature['code']) }}">@csrf
              <input type="hidden" name="enabled" value="{{ $feature['enabled'] ? '0' : '1' }}">
              <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-white">{{ $feature['enabled'] ? 'Turn OFF' : 'Turn ON' }}</button>
            </form>
          @endif
        </div>
      @endforeach</div>
    </section>
    <p class="text-sm text-slate-500">This is an operational switch, not a plugin upload facility. Route/API integrations need a security review before adding them to the catalog.</p>
</div>
@endsection
