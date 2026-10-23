@extends('layouts.admin')
@section('title', 'Legal Advisory Features')
@section('page_heading', 'Legal Advisory Features')
@section('content')
<div style="max-width:700px;margin:2rem auto;font-family:system-ui"><h1>Legal Advisory Features</h1><p>All features default OFF. Switches affect new access, not historical records.</p>@if(session('success'))<p role="status">{{ session('success') }}</p>@endif @foreach(\App\Support\LegalAdvisory\Feature::CODES as $code)<form method="post" action="{{ route('admin.legal.features.update') }}" style="padding:1rem;border-bottom:1px solid #aaa">@csrf<input type="hidden" name="code" value="{{ $code }}"><label>{{ $code }} <select name="enabled"><option value="0" @selected(!($flags[$code] ?? false))>OFF</option><option value="1" @selected($flags[$code] ?? false)>ON</option></select></label><button>Save</button></form>@endforeach<p><a href="{{ route('admin.legal.qualifications.index') }}">Review professional qualifications</a></p></div>
@endsection
