@extends('layouts.vendor_restaurant')

@push('styles')
<style>
    .content-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease-in-out;
    }

    .table-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06);
        border-color: #4f46e5;
    }

    .table-icon-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 1.5rem;
    }

    @media print {
        body * {
            visibility: hidden !important;
        }
        #printableQrCard, #printableQrCard * {
            visibility: visible !important;
        }
        #printableQrCard {
            position: fixed !important;
            left: 50% !important;
            top: 40% !important;
            transform: translate(-50%, -50%) !important;
            width: 300px !important;
            padding: 24px !important;
            border: 2px solid #000 !important;
            border-radius: 16px !important;
            text-align: center !important;
            background: #fff !important;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<!-- Header Bar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">
            FLOOR & TABLES
        </span>
        <h2 class="fw-bold mt-2 mb-0">Dining Tables & QR Codes</h2>
        <p class="text-muted small mb-0">Manage floor tables, seating capacity, and generate dynamic QR codes.</p>
    </div>
    <div>
        <button class="btn btn-primary px-4 py-2.5 rounded-3 fw-semibold shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addTableModal">
            <i class="bi bi-plus-lg"></i> Add New Table
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Table Grid Cards -->
<div class="row g-4">
    @forelse($tables as $table)
        @php
            $token = $table->qr_code_token ?? $table->token ?? $table->id;
            $qrUrl = Route::has('customer.restaurant.menu') ? route('customer.restaurant.menu', $token) : url('/c/' . $token);
            $restName = auth()->user()->restaurant_name ?? auth()->user()->name ?? 'Restaurant';
        @endphp
        <div class="col-sm-6 col-md-4 col-xl-3">
            <div class="content-card table-card p-4 text-center h-100 d-flex flex-column justify-content-between position-relative">
                
                <!-- Action Controls (Edit & Delete) -->
                <div class="position-absolute top-0 end-0 p-3 d-flex gap-1">
                    <button class="btn btn-light btn-sm rounded-circle p-1" style="width:28px; height:28px;" title="Edit Table" onclick="openEditTableModal('{{ $table->id }}', '{{ $table->table_number }}', '{{ $table->seating_capacity }}')">
                        <i class="bi bi-pencil text-muted" style="font-size: 0.75rem;"></i>
                    </button>
                    @if(Route::has('vendor.restaurant.tables.destroy'))
                        <form action="{{ route('vendor.restaurant.tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete Table {{ $table->table_number }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-light btn-sm rounded-circle p-1" style="width:28px; height:28px;" title="Delete Table">
                                <i class="bi bi-trash text-danger" style="font-size: 0.75rem;"></i>
                            </button>
                        </form>
                    @endif
                </div>

                <div>
                    <div class="table-icon-avatar mt-2">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">T-{{ $table->table_number }}</h4>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-people me-1"></i> {{ $table->seating_capacity }} Persons Capacity
                    </p>
                    
                    @if(($table->status ?? 'available') == 'available')
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 rounded-pill fw-semibold">
                            Available
                        </span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1.5 rounded-pill fw-semibold">
                            {{ ucfirst($table->status) }}
                        </span>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-dark btn-sm w-100 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2" onclick="openQrModal('{{ $restName }}', 'Table {{ $table->table_number }}', '{{ $qrUrl }}')">
                        <i class="bi bi-qr-code"></i> View QR Code
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="content-card p-5">
                <i class="bi bi-grid-3x3-gap display-4 text-muted opacity-25 d-block mb-3"></i>
                <h5 class="fw-bold text-dark mb-1">No Tables Created Yet</h5>
                <p class="text-muted small mb-3">Start by adding your restaurant dining tables to generate QR codes.</p>
                <button class="btn btn-primary btn-sm px-4 py-2 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addTableModal">
                    <i class="bi bi-plus-lg me-1"></i> Add First Table
                </button>
            </div>
        </div>
    @endforelse
</div>

<!-- Add Table Modal -->
<div class="modal fade" id="addTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ Route::has('vendor.restaurant.tables.store') ? route('vendor.restaurant.tables.store') : '#' }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Add Dining Table</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Table Number / Name</label>
                        <input type="text" name="table_number" class="form-control rounded-3 p-2.5" required placeholder="e.g. 01, T-02, VIP-1">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary">Seating Capacity</label>
                        <input type="number" min="1" name="seating_capacity" class="form-control rounded-3 p-2.5" value="4" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">Create Table</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Table Modal -->
<div class="modal fade" id="editTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editTableForm" method="POST" action="" class="w-100">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Edit Dining Table</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Table Number / Name</label>
                        <input type="text" id="edit_table_number" name="table_number" class="form-control rounded-3 p-2.5" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary">Seating Capacity</label>
                        <input type="number" min="1" id="edit_seating_capacity" name="seating_capacity" class="form-control rounded-3 p-2.5" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-semibold">Update Table</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View & Print QR Modal -->
<div class="modal fade" id="qrViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow text-center p-3">
            <div id="printableQrCard" class="p-2">
                <h5 class="fw-bold text-dark mb-1" id="qrRestName">Restaurant</h5>
                <h6 class="text-primary mb-2 fw-semibold" id="qrModalTitle">Table No</h6>
                <div class="my-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill small">
                        📱 Scan to Order
                    </span>
                </div>
                <img id="qrModalImg" src="" alt="QR Code" class="img-fluid border p-2 rounded-3 my-2 mx-auto" style="max-width: 180px;">
                <p class="small text-muted mb-0 mt-1" style="font-size: 0.72rem;">Point camera to scan menu</p>
            </div>
            <div class="d-flex gap-2 justify-content-center mt-3 no-print">
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Print QR
                </button>
                <button type="button" class="btn btn-light btn-sm rounded-pill px-3 fw-bold" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openQrModal(restName, tableTitle, qrUrl) {
        document.getElementById('qrRestName').innerText = restName;
        document.getElementById('qrModalTitle').innerText = tableTitle;
        document.getElementById('qrModalImg').src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrUrl)}`;
        new bootstrap.Modal(document.getElementById('qrViewModal')).show();
    }

    function openEditTableModal(id, number, capacity) {
        @if(Route::has('vendor.restaurant.tables.update'))
            let updateUrl = "{{ route('vendor.restaurant.tables.update', ':id') }}".replace(':id', id);
        @else
            let updateUrl = "/vendor/restaurant/tables/" + id;
        @endif

        document.getElementById('editTableForm').action = updateUrl;
        document.getElementById('edit_table_number').value = number;
        document.getElementById('edit_seating_capacity').value = capacity;

        new bootstrap.Modal(document.getElementById('editTableModal')).show();
    }
</script>
@endpush
@endsection