<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white fs-6 fs-md-5 fw-bold"><i class="fas fa-store me-2"></i> My Registered Businesses</h5>
    </div>
    <div class="card-body p-3 p-md-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light text-uppercase fs-7">
                    <tr>
                        <th class="py-3">Business Name</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">City</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($businesses as $biz)
                        <tr>
                            <td class="py-3 fw-bold text-dark">{{ $biz->business_name }}</td>
                            <td class="py-3"><span class="badge bg-info-subtle text-info border border-info px-2 py-1">{{ ucfirst($biz->business_type) }}</span></td>
                            <td class="py-3 text-muted">{{ $biz->city ?? 'N/A' }}</td>
                            <td class="py-3">
                                @if(session('active_business_id') == $biz->id)
                                    <span class="badge bg-success px-2 py-1">Active Session</span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
    <a href="{{ route('vendor.businesses.switch', $biz->id) }}" class="btn btn-sm btn-primary px-3 fw-bold shadow-sm">
        <i class="fas fa-external-link-alt me-1"></i> Open {{ ucfirst($biz->business_type) }} Panel
    </a>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open fa-2x mb-2 d-block text-secondary"></i>
                                No business added yet. Add your first business!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>