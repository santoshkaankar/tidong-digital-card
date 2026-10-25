@extends('layouts.admin')
@section('title', $title)
@section('page_heading', $title)
@section('content')
<div class="mx-auto max-w-6xl space-y-5">
    @if(session('success')) <div role="status" class="rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div> @endif
    @if($errors->any()) <div role="alert" class="rounded-lg bg-red-50 p-4 text-red-800">{{ $errors->first() }}</div> @endif
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div><h1 class="text-2xl font-bold">{{ $title }}</h1><p class="text-sm text-slate-500">Global records available for reuse across vendors.</p></div>
        <button type="button" onclick="document.getElementById('create-master').showModal()" class="rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white">+ Create {{ $title }}</button>
    </div>
    <div class="overflow-x-auto rounded-xl border bg-white">
        <table class="w-full text-left text-sm"><thead class="bg-slate-100"><tr><th class="p-4">Name</th><th class="p-4">Code</th><th class="p-4">Status</th>@if(empty($vendorGlobalMode))<th class="p-4">Manage</th>@endif</tr></thead>
        <tbody>@forelse($records as $record)<tr class="border-t"><td class="p-4 font-medium">{{ $record->name }}</td><td class="p-4">{{ $record->code ?? '—' }}</td><td class="p-4">{{ $record->is_active ? 'Active' : 'Inactive' }}</td>@if(empty($vendorGlobalMode))<td class="p-4"><details><summary class="cursor-pointer text-blue-700">Edit</summary><form method="POST" enctype="multipart/form-data" action="{{ route('admin.global-masters.update',['type'=>$type,'record'=>$record->id]) }}" class="mt-2 flex min-w-48 flex-col gap-2">@csrf @method('PUT')<input aria-label="Name" name="name" required maxlength="150" value="{{ $record->name }}" class="rounded border p-2"><input aria-label="Code" name="code" maxlength="50" value="{{ $record->code }}" class="rounded border p-2"><select aria-label="Status" name="is_active" class="rounded border p-2"><option value="1" @selected($record->is_active)>Active</option><option value="0" @selected(!$record->is_active)>Inactive</option></select>@if($type==='brands')<input name="owner_name" aria-label="Brand owner / firm" value="{{ $record->owner_name }}" class="rounded border p-2">@endif @if(in_array($type,['brands','item_categories']))<input type="file" name="image" accept="image/jpeg,image/png,image/webp" aria-label="Replace image" class="rounded border p-2">@endif <button class="rounded bg-blue-700 p-2 text-white">Save</button></form></details></td>@endif</tr>
        @empty <tr><td colspan="{{ empty($vendorGlobalMode)?4:3 }}" class="p-6 text-slate-500">No records yet. Use Create to add one.</td></tr>@endforelse</tbody></table>
    </div>
    {{ $records->links() }}
</div>
<dialog id="create-master" class="w-[min(94vw,34rem)] rounded-xl p-0 shadow-2xl backdrop:bg-black/50">
<form method="POST" enctype="multipart/form-data" action="{{ route(!empty($vendorGlobalMode)?'vendor.global-catalog.store':'admin.global-masters.store', ['type'=>$type]) }}" class="space-y-4 p-6">
    @csrf
    <h2 class="text-xl font-bold">Create {{ $title }}</h2>
    <label class="block text-sm font-semibold">Name *<input name="name" required maxlength="150" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border p-3"></label>
    <label class="block text-sm font-semibold">Code (optional)<input name="code" maxlength="50" value="{{ old('code') }}" class="mt-1 w-full rounded-lg border p-3"></label>
    @if(in_array($type,['brands','item_categories']))<label class="block text-sm font-semibold">Image (JPG, PNG, WebP; max 2 MB)<input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="mt-1 w-full rounded-lg border p-3"></label>@endif
    @if($type==='brands')<label class="block text-sm font-semibold">Brand Owner / Firm<input name="owner_name" maxlength="200" class="mt-1 w-full rounded-lg border p-3"></label>@endif
    @if($type==='variant_values' || $type==='item_categories')
        <label class="block text-sm font-semibold">{{ $type==='variant_values'?'Attribute *':'Parent Category (optional)' }}
        <select name="parent_id" class="mt-1 w-full rounded-lg border p-3" {{ $type==='variant_values'?'required':'' }}>
            <option value="">Select</option>@foreach($parents as $parent)<option value="{{ $parent->id }}">{{ $parent->name }}</option>@endforeach
        </select></label>
    @endif
    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Active</label>
    <div class="flex justify-end gap-3"><button type="button" onclick="document.getElementById('create-master').close()" class="rounded-lg border px-5 py-3">Cancel</button><button class="rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white">Save Global</button></div>
</form>
</dialog>
@if($errors->any())<script>document.addEventListener('DOMContentLoaded',()=>document.getElementById('create-master').showModal())</script>@endif
@endsection
