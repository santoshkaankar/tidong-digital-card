@extends('layouts.admin')
@section('title','Vendor Global Master Permissions')
@section('page_heading','Vendor Global Master Permissions')
@section('content')
<h1 class="mb-4 text-2xl font-bold">Vendor Global Master Permissions</h1>
<p class="mb-5 text-sm text-slate-600">Global Create and Edit permissions are OFF by default. Custom outlet items are independent.</p>
@if(session('success'))<p class="mb-4 rounded bg-green-50 p-3">{{session('success')}}</p>@endif
<div class="overflow-x-auto rounded-xl border bg-white"><table class="w-full text-left text-sm"><thead><tr><th class="p-3">Vendor</th><th class="p-3">Status</th><th class="p-3">Global Permissions</th></tr></thead><tbody>
@foreach($vendors as $vendor)<tr class="border-t"><td class="p-3">{{$vendor->display_name}}</td><td class="p-3">{{$vendor->status}}</td><td class="p-3">
<form method="POST" action="{{route('admin.global-master-permissions.update',['vendor'=>$vendor->id])}}" class="flex flex-wrap items-center gap-3">
@csrf @method('PUT')
<input type="hidden" name="can_create" value="0"><label><input type="checkbox" name="can_create" value="1" @checked($vendor->can_create)> Create</label>
<input type="hidden" name="can_edit" value="0"><label><input type="checkbox" name="can_edit" value="1" @checked($vendor->can_edit)> Edit</label>
<button class="rounded bg-blue-700 px-4 py-2 text-white">Save</button></form></td></tr>@endforeach
</tbody></table></div><div class="mt-4">{{$vendors->links()}}</div>
@endsection
