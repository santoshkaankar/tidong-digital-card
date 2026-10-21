@extends(request()->routeIs('admin.*') ? 'layouts.admin' : 'layouts.platform-ops')
@section('title','Employee Permissions')
@section('page_heading','Employee Permissions')
@section('content')
<div class="max-w-4xl space-y-5"><h1 class="text-xl font-bold">Grant Individual Employee Permissions</h1>
<p class="text-sm text-slate-600">Only Super Admin or Admin can grant individual rights. Employee grants do not affect other employees; every change is recorded in the audit log.</p>
@if(session('success'))<div class="bg-green-50 p-3">{{ session('success') }}</div>@endif
@if($errors->any())<div class="bg-red-50 p-3">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route(explode('.', request()->route()->getName())[0].'.employee-permissions.update') }}" class="rounded-xl bg-white shadow-sm p-5 grid gap-4">@csrf
<label>Admin Employee<select required name="user_id" class="block mt-1 w-full border p-2 rounded"><option value="">Select Employee</option>@foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->name }} ({{ $e->email }})</option>@endforeach</select></label>
<label>Permission<select required name="permission_code" class="block mt-1 w-full border p-2 rounded">@foreach(\App\Services\Platform\PlatformCapability::CAPABILITIES as $code)<option value="{{ $code }}">{{ $code }}</option>@endforeach</select></label>
<label>Action<select required name="enabled" class="block mt-1 w-full border p-2 rounded"><option value="1">Grant</option><option value="0">Revoke</option></select></label>
<button type="submit" class="rounded bg-blue-700 p-3 text-white">Update Employee Permission</button>
</form>
<h2 class="font-semibold">Current Individual Grants</h2>
<div class="rounded-xl bg-white p-4 space-y-2">@forelse($employees as $e)<div class="border-b py-2"><strong>{{ $e->name }}</strong> — {{ $e->email }}<div class="text-sm text-slate-600">@forelse(($grants[$e->id] ?? collect()) as $g)<span class="inline-block mr-3">{{ $g->permission_code }}: {{ $g->is_enabled ? 'Granted' : 'Revoked' }}</span>@empty None @endforelse</div></div>@empty No eligible employees.@endforelse</div></div>
@endsection
