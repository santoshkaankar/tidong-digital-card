@extends('layouts.admin') {{-- आपकी Admin layout फ़ाइल का नाम --}}

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Global Master Items List</h2>
        <a href="{{ route('admin.global.item.create') }}" class="btn btn-primary">+ Add New Item</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>MRP (₹)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if($item->item_pic)
                                    <img src="{{ asset('storage/' . $item->item_pic) }}" alt="{{ $item->item_name }}" width="50" height="50" class="rounded">
                                @else
                                    <span class="badge bg-secondary">No Image</span>
                                @endif
                            </td>
                            <td><strong>{{ $item->item_name }}</strong></td>
                            <td>{{ $item->category }}</td>
                            <td>₹{{ number_format($item->mrp, 2) }}</td>
                            <td>
                                <span class="badge bg-success">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td>
    {{-- यह रहा एडिट बटन --}}
    <a href="{{ route('admin.global.item.edit', $item->id) }}" class="btn btn-sm btn-warning me-1">Edit</a>

    <form action="{{ route('admin.global.item.delete', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
    </form>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No Global Items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection