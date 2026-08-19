<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Configuration - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        #sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 250px; background: #1e293b; color: #fff; overflow-y: auto; z-index: 1000; }
        #main-content { margin-left: 250px; padding: 30px; }
        @media (max-width: 768px) {
            #sidebar { width: 100%; height: auto; position: relative; }
            #main-content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>
<body>

    @include('admin.layouts.sidebar')

    <div id="main-content">
        <div class="container-fluid">
            <h3 class="fw-bold mb-4"><i class="fas fa-id-card me-2"></i> Master Profile & Card Configuration</h3>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Universal Search Box -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <form action="{{ route('admin.members.manage') }}" method="GET">
                        <label class="form-label fw-semibold">Search Member by Email or Mobile for Configuration</label>
                        <div class="input-group">
                            <input type="text" name="query" class="form-control" placeholder="Enter email or mobile..." value="{{ $query ?? '' }}" required>
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(isset($query))
                @if(!$member)
                    <!-- NEW MEMBER SETUP FORM (Included from separate file) -->
                    @include('admin.members.new_member')
                @else
                    <!-- DIRECT CONFIG FORM -->
                    @include('admin.members.config', ['card' => $card, 'member' => $member])
                @endif
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>