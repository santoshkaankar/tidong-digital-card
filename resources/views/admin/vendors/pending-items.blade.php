<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pending Item Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-user-shield me-2 text-danger"></i> Admin Dashboard - Pending Requests</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
        </div>
    </nav>

    <div class="container py-5">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white">
            <h4 class="fw-bold text-dark mb-4"><i class="fas fa-clock text-warning me-2"></i> Items Requested by Businesses</h4>

            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Category</th>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Default Price (₹)</th>
                            <th>Requested By (User ID)</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingItems as $item)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $item->category }}</span></td>
                            <td class="fw-bold">{{ $item->item_name }}</td>
                            <td class="text-muted small">{{ $item->description ?? 'N/A' }}</td>
                            <td class="text-success fw-bold">₹{{ $item->default_price }}</td>
                            <td>{{ $item->requested_by ?? 'Admin' }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Approve Form -->
                                    <form action="{{ route('admin.item.approve', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check me-1"></i> Approve</button>
                                    </form>

                                    <!-- Reject Form -->
                                    <form action="{{ route('admin.item.reject', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times me-1"></i> Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">No pending item requests right now! All caught up. 🎉</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>