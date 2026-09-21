@extends('layouts.vendor_restaurant')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Create Custom Item / Thali</h2>
        <a href="{{ route('vendor.restaurant.items.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="{{ route('vendor.restaurant.items.create_custom') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-3">
                <!-- Item Name -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Item / Thali Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Special Thali, Churma Thali" value="{{ old('name') }}" required>
                </div>

                <!-- Category -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select name="restaurant_category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Food Type -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="veg">Veg</option>
                        <option value="non-veg">Non-Veg</option>
                        <option value="egg">Egg</option>
                    </select>
                </div>

                <!-- Tax Rate -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tax Rate</label>
                    <select name="tax_id" class="form-select">
                        <option value="">Select Tax (GST)</option>
                        @foreach($taxes as $tax)
                            <option value="{{ $tax->id }}">{{ $tax->name ?? 'GST' }} ({{ $tax->tax_percentage }}%)</option>
                        @endforeach
                    </select>
                </div>

                <!-- Regular Price -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Regular Price (₹) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="mrp" class="form-control" placeholder="0.00" required>
                </div>

                <!-- Sale Price / Offer Rate -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Sale Rate / Offer (₹)</label>
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00">
                </div>

                <!-- Item Image -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Thali / Item Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <small class="text-muted">Supported formats: JPEG, PNG, JPG, WEBP (Max: 2MB)</small>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Description (What's included)</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="e.g. 4 Roti, Dal Fry, Shahi Paneer, Rice, Raita, Sweet">{{ old('description') }}</textarea>
                </div>

                <!-- Submit Button -->
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Custom Item</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection