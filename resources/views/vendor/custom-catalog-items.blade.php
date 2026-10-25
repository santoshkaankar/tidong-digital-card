<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Custom Items</title>@vite(['resources/css/app.css','resources/js/app.js'])</head><body class="bg-slate-100 text-slate-800">
<div class="mx-auto max-w-5xl space-y-5 p-4">
 <h1 class="text-2xl font-bold">Outlet Custom Items</h1>
 <p class="text-sm text-slate-600">These items belong only to this outlet. They are not published to Global Masters.</p>
 @if(session('success'))<p role="status" class="rounded bg-green-50 p-3">{{session('success')}}</p>@endif
 @if($errors->any())<p role="alert" class="rounded bg-red-50 p-3">{{$errors->first()}}</p>@endif
 <form method="POST" action="{{route('vendor.custom-items.store',['branch'=>$branch])}}" class="grid gap-4 rounded-xl border bg-white p-5 sm:grid-cols-2">
 @csrf
 <label class="block">Item name *<input name="name" required maxlength="150" value="{{old('name')}}" class="mt-1 w-full rounded border p-3"></label>
 <label class="block">MRP *<input type="number" step="0.01" min="0" name="mrp" required value="{{old('mrp')}}" class="mt-1 w-full rounded border p-3"></label>
 <label class="block">Sale price *<input type="number" step="0.01" min="0" name="sale_price" required value="{{old('sale_price')}}" class="mt-1 w-full rounded border p-3"></label>
 <label class="block sm:col-span-2">Description<textarea name="description" maxlength="1500" class="mt-1 w-full rounded border p-3">{{old('description')}}</textarea></label>
 <button class="rounded-lg bg-blue-700 px-5 py-3 font-semibold text-white sm:col-span-2">Save Custom Item</button>
 </form>
 <div class="overflow-x-auto rounded-xl border bg-white"><table class="w-full text-left text-sm"><thead><tr><th class="p-3">Item</th><th class="p-3">MRP</th><th class="p-3">Sale Price</th></tr></thead><tbody>
 @forelse($items as $item)<tr class="border-t"><td class="p-3">{{$item->name}}</td><td class="p-3">{{$item->mrp}}</td><td class="p-3">{{$item->sale_price}}</td></tr>
 @empty<tr><td colspan="3" class="p-4">No custom items yet.</td></tr>@endforelse
 </tbody></table></div>{{$items->links()}}
</div>
</body></html>
