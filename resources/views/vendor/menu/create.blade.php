<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Menu & Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Restaurant / Hotel Menu Setup</h2>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Step 1: Business Details -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">1. Business & WhatsApp Settings</div>
            <div class="card-body">
                <form action="{{ route('menu.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Business Name</label>
                        <input type="text" name="business_name" class="form-control" value="{{ $menu->business_name ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">WhatsApp Number (with Country Code, e.g., 919876543210)</label>
                        <input type="text" name="whatsapp_number" class="form-control" value="{{ $menu->whatsapp_number ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control" required>
                            <option value="restaurant" {{ isset($menu) && $menu->type == 'restaurant' ? 'selected' : '' }}>Restaurant (Table Wise)</option>
                            <option value="hotel" {{ isset($menu) && $menu->type == 'hotel' ? 'selected' : '' }}>Hotel (Room Wise)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Save Menu Settings</button>
                </form>
            </div>
        </div>

        <!-- Step 2: Add Menu Items -->
        @if(isset($menu))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">2. Add Menu Items</div>
            <div class="card-body">
                <form action="{{ route('menu.item.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" name="item_name" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Description (Spicy, Half/Full, etc.)</label>
                            <input type="text" name="description" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Price (₹)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </form>

                <hr class="my-4">

                <h4 class="mb-3">Existing Items List</h4>
                <ul class="list-group">
                    @forelse($menu->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $item->item_name }}</strong> 
                                @if($item->description) <small class="text-muted">({{ $item->description }})</small> @endif
                            </div>
                            <span class="badge bg-success rounded-pill">₹{{ $item->price }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No items added yet.</li>
                    @endforelse
                </ul>

                <div class="mt-4 p-3 bg-light border rounded">
                    <strong>Public QR / Menu Link for Customers:</strong><br>
                    <a href="{{ route('menu.public', $menu->id) }}?loc=Table-1" target="_blank">{{ route('menu.public', $menu->id) }}?loc=Table-1</a> (Example for Table 1)
                </div>
            </div>
        </div>
        @endif
    </div>
</body>
</html>