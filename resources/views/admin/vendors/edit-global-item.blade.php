@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Global Item</h2>
        <a href="{{ route('admin.global.items.index') }}" class="btn btn-secondary">Back to List</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm col-md-8">
        <div class="card-body">
            <form action="{{ route('admin.global.item.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        @foreach($itemCategories as $cat)
                            <option value="{{ $cat->name }}" {{ $item->category == $cat->name ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="item_name" class="form-control" value="{{ old('item_name', $item->item_name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Food Type</label>
                    <select name="food_type" class="form-select" required>
                        <option value="veg" {{ $item->food_type == 'veg' ? 'selected' : '' }}>Veg</option>
                        <option value="non-veg" {{ $item->food_type == 'non-veg' ? 'selected' : '' }}>Non-Veg</option>
                        <option value="egg" {{ $item->food_type == 'egg' ? 'selected' : '' }}>Egg</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tax Slab / GST</label>
                    <select name="tax_id" class="form-select">
                        <option value="">-- Choose Tax --</option>
                        @foreach($taxes ?? [] as $tax)
                            <option value="{{ $tax->id }}" {{ $item->tax_id == $tax->id ? 'selected' : '' }}>
                                {{ $tax->tax_name }} ({{ $tax->tax_percentage }}%)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">MRP (₹)</label>
                    <input type="number" step="0.01" name="mrp" class="form-control" value="{{ old('mrp', $item->mrp) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Image</label><br>
                    @if($item->item_pic)
                        <img src="{{ asset('storage/' . $item->item_pic) }}" width="70" class="rounded mb-2">
                    @else
                        <span class="badge bg-secondary">No Image</span>
                    @endif
                    <input type="file" name="item_pic" class="form-control mt-2">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $item->description) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update Item</button>
            </form>
        </div>
    </div>
</div>
@endsection