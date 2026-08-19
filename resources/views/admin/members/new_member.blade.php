@if(!$member)
    <!-- New Member Setup Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning text-dark fw-bold py-3">
            <i class="bi bi-person-plus-fill me-2"></i> New Member Setup
        </div>
        <div class="card-body">
            <div class="alert alert-info py-2 mb-3">
                <small><i class="bi bi-info-circle me-1"></i> "<strong>{{ $query }}</strong>" se koi existing member nahi mila. Naya member create karne ke liye niche diya form bhrein.</small>
            </div>

            <form action="{{ route('admin.members.save') }}" method="POST" autocomplete="off">
                @csrf
                <input type="hidden" name="new_member_setup" value="1">
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">Full Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter full name" value="{{ old('name') }}" autocomplete="off" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">Username *</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Enter username" value="{{ old('username') }}" autocomplete="new-username" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">Email Address *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email address" value="{{ old('email', filter_var($query, FILTER_VALIDATE_EMAIL) ? $query : '') }}" autocomplete="off" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">Mobile Number *</label>
                        <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" placeholder="Enter mobile number" value="{{ old('mobile', is_numeric($query) ? $query : '') }}" autocomplete="off" required>
                        @error('mobile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-secondary small">Password *</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password" autocomplete="new-password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-success fw-bold px-4">
                            <i class="bi bi-check-circle me-1"></i> Create & Open Config Form
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif