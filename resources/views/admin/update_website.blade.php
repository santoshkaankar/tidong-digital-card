<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Website Code</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Admin Panel - System Update</h4>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">Back to Dashboard</a>
            </div>

            <!-- Success ya Error Messages Dikhane ke liye -->
            @if(session('success'))
                <div class="alert alert-success fw-bold">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger fw-bold">
                    {{ session('error') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Update Form -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Upload New Source Code (ZIP)</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Apne naye code ki <strong>.zip</strong> file yahan upload karein. File extract hone ke baad purani files naye code se replace ho jayengi.</p>
                    
                    <form action="{{ route('admin.upload_update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="update_zip" class="form-label fw-bold">Select ZIP File</label>
                            <input class="form-control form-control-lg" type="file" id="update_zip" name="update_zip" accept=".zip" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Update Website Now</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>